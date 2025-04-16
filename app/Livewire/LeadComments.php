<?php

namespace App\Livewire;

use App\Models\Lead;
use App\Models\LeadComment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class LeadComments extends Component
{
    use AuthorizesRequests;

    public $leadId;
    public $newComment = '';
    public $editingCommentId = null;
    public $editedContent = '';
    public $historyType = 'lead'; // 'lead' или 'comment'

    protected $listeners = ['commentAdded' => '$refresh'];

    protected $rules = [
        'newComment' => 'required|string|min:1|max:2000',
        'editedContent' => 'required|string|min:1|max:2000',
    ];

    public function mount($leadId)
    {
        $this->leadId = $leadId;
    }

    public function render()
    {
        $lead = Lead::with(['comments' => function ($query) {
            $query->with('user')->orderBy('created_at', 'desc');
        }])->findOrFail($this->leadId);

        $leadEvents = $lead->events()
            ->when($this->historyType === 'lead', function ($query) {
                $query->whereRaw("COALESCE(JSON_EXTRACT(metadata, '$.type'), '') != 'comment'");
            })
            ->when($this->historyType === 'comment', function ($query) {
                $query->whereRaw("JSON_EXTRACT(metadata, '$.type') = 'comment'");
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.lead-comments', [
            'lead' => $lead,
            'comments' => $lead->comments,
            'leadEvents' => $leadEvents,
        ]);
    }

    public function addComment()
    {
        $this->validate([
            'newComment' => 'required|string|min:1|max:2000',
        ]);

        $lead = Lead::findOrFail($this->leadId);

        // Проверить, что пользователь имеет доступ к компании заявки
        $this->authorize('view', $lead);

        // Создать новый комментарий
        $lead->comments()->create([
            'company_id' => $lead->company_id,
            'user_id' => auth()->id(),
            'content' => $this->newComment,
        ]);

        // Очистить поле ввода
        $this->newComment = '';

        // Обновить компонент
        $this->dispatch('commentAdded');
    }

    public function startEditing($commentId)
    {
        $comment = LeadComment::findOrFail($commentId);

        // Проверить, что пользователь имеет доступ к редактированию комментария
        $this->authorize('update', $comment);

        $this->editingCommentId = $commentId;
        $this->editedContent = $comment->content;
    }

    public function cancelEditing()
    {
        $this->editingCommentId = null;
        $this->editedContent = '';
    }

    public function updateComment()
    {
        $this->validate([
            'editedContent' => 'required|string|min:1|max:2000',
        ]);

        $comment = LeadComment::findOrFail($this->editingCommentId);

        // Проверить, что пользователь имеет доступ к редактированию комментария
        $this->authorize('update', $comment);

        // Если содержимое изменилось
        if ($comment->content !== $this->editedContent) {
            // Сохранить оригинальное содержимое при первом редактировании
            if (!$comment->is_edited) {
                $comment->original_content = $comment->content;
            }

            // Обновить комментарий
            $comment->update([
                'content' => $this->editedContent,
                'is_edited' => true,
                'edited_at' => now(),
            ]);
        }

        $this->editingCommentId = null;
        $this->editedContent = '';
    }

    public function deleteComment($commentId)
    {
        $comment = LeadComment::findOrFail($commentId);

        // Проверить, что пользователь имеет доступ к удалению комментария
        $this->authorize('delete', $comment);

        $comment->delete();
    }

    public function setHistoryType($type)
    {
        $this->historyType = $type;
    }
}

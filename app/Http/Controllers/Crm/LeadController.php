<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadController extends Controller
{
    /**
     * Конструктор контроллера заявок
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Отображает список всех заявок компании
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;

        // Получаем базовый запрос заявок для текущей компании
        $leadsQuery = Lead::forCompany($companyId);

        // Применяем фильтры, если они указаны
        if ($request->has('status')) {
            $leadsQuery->where('status', $request->status);
        }

        if ($request->has('source')) {
            $leadsQuery->where('source', $request->source);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $leadsQuery->where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('message', 'like', "%{$search}%");
            });
        }

        // Получаем отсортированные заявки с пагинацией
        $leads = $leadsQuery->orderBy('created_at', 'desc')->paginate(10);

        // Получаем уникальные источники для фильтра
        $sources = Lead::forCompany($companyId)
            ->distinct()
            ->pluck('source')
            ->filter()
            ->toArray();

        return view('crm.leads.index', compact('leads', 'sources'));
    }

    /**
     * Отображает информацию о конкретной заявке
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $companyId = Auth::user()->company_id;
        $lead = Lead::forCompany($companyId)->findOrFail($id);

        return view('crm.leads.show', compact('lead'));
    }

    /**
     * Обновляет статус заявки
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, $id)
    {
        $companyId = Auth::user()->company_id;
        $lead = Lead::forCompany($companyId)->findOrFail($id);

        $request->validate([
            'status' => 'required|in:new,in_progress,completed,archived'
        ]);

        $lead->status = $request->status;
        $lead->save();

        return redirect()->back()->with('success', 'Статус заявки успешно обновлен');
    }
}

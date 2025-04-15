<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

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

        // Используем QueryBuilder от Spatie для более удобной фильтрации
        $leads = QueryBuilder::for(Lead::class)
            ->where('company_id', $companyId) // Применяем фильтр по компании
            ->allowedFilters([
                'status',
                'source',
                AllowedFilter::callback('search', function ($query, $value) {
                    $query->where(function($query) use ($value) {
                        $query->where('name', 'like', "%{$value}%")
                            ->orWhere('email', 'like', "%{$value}%")
                            ->orWhere('phone', 'like', "%{$value}%")
                            ->orWhere('message', 'like', "%{$value}%");
                    });
                }),
            ])
            ->defaultSort('-created_at')
            ->paginate(10)
            ->withQueryString();

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

    /**
     * Экспортирует заявки в CSV с учетом фильтров
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportCsv(Request $request)
    {
        $companyId = Auth::user()->company_id;

        // Базовый запрос с фильтрацией по компании
        $query = Lead::forCompany($companyId);

        // Применяем фильтры из запроса
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('source') && $request->source) {
            $query->where('source', $request->source);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        // Применяем сортировку
        if ($request->has('sort') && $request->sort) {
            $sortField = ltrim($request->sort, '-');
            $sortDirection = str_starts_with($request->sort, '-') ? 'desc' : 'asc';
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->latest(); // По умолчанию - по дате создания (убывание)
        }

        // Получаем данные
        $leads = $query->get();

        // Подготавливаем имя файла
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "leads_export_{$timestamp}.csv";

        // Создаем временный файл с кодировкой UTF-8 и BOM-маркером
        $handle = fopen(storage_path('app/' . $filename), 'w');

        // Добавляем BOM-маркер UTF-8 в начало файла для правильного определения кодировки Excel
        fputs($handle, "\xEF\xBB\xBF");

        // Заголовки CSV
        fputcsv($handle, [
            'ID',
            'Источник',
            'Имя',
            'Email',
            'Телефон',
            'Сообщение',
            'Статус',
            'Категория',
            'Оценка релевантности',
            'Дата создания',
            'Первый ответ',
            'Завершение',
            'Время ответа (мин)',
            'Время резолюции (мин)',
            'Теги'
        ], ';'); // Используем разделитель ";" для лучшей совместимости с Excel

        // Данные
        foreach ($leads as $lead) {
            // Получаем теги заявки в виде строки, разделенной запятыми
            $tags = $lead->tags->pluck('name')->implode(', ');

            fputcsv($handle, [
                $lead->id,
                $lead->source,
                $lead->name,
                $lead->email,
                $lead->phone,
                $lead->message,
                $lead->status_label,
                $lead->category,
                $lead->relevance_score,
                $lead->created_at->format('Y-m-d H:i:s'),
                $lead->first_response_at ? $lead->first_response_at->format('Y-m-d H:i:s') : '-',
                $lead->resolved_at ? $lead->resolved_at->format('Y-m-d H:i:s') : '-',
                $lead->response_time_minutes ?? '-',
                $lead->resolution_time_minutes ?? '-',
                $tags
            ], ';'); // Используем разделитель ";" для лучшей совместимости с Excel
        }

        fclose($handle);

        // Возвращаем файл с указанием кодировки
        return response()->download(
            storage_path('app/' . $filename),
            $filename,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]
        )->deleteFileAfterSend();
    }
}

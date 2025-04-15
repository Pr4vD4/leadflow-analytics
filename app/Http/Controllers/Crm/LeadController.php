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
}

<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TagController extends Controller
{
    /**
     * Создает новый тег
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50'
        ]);

        $companyId = Auth::user()->company_id;

        // Проверяем существование тега с таким именем в компании
        $existingTag = Tag::where('company_id', $companyId)
            ->where('name', $request->name)
            ->first();

        if ($existingTag) {
            return response()->json([
                'success' => true,
                'tag' => $existingTag,
                'message' => 'Тег уже существует'
            ]);
        }

        // Создаем новый тег
        $tag = new Tag();
        $tag->company_id = $companyId;
        $tag->name = $request->name;
        $tag->save();

        return response()->json([
            'success' => true,
            'tag' => $tag,
            'message' => 'Тег успешно создан'
        ]);
    }
}

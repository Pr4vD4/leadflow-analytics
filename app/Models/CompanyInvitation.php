<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CompanyInvitation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'company_id',
        'user_id',
        'is_active',
        'activated_at',
        'activated_by_user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'activated_at' => 'datetime',
    ];

    /**
     * Генерирует уникальный код приглашения
     *
     * @return string
     */
    public static function generateUniqueCode(): string
    {
        $code = Str::random(8);

        // Проверяем уникальность кода
        while (self::where('code', $code)->exists()) {
            $code = Str::random(8);
        }

        return $code;
    }

    /**
     * Получить компанию, к которой относится приглашение
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Получить пользователя, создавшего приглашение
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Получить пользователя, активировавшего приглашение
     */
    public function activatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'activated_by_user_id');
    }

    /**
     * Деактивировать код приглашения
     */
    public function deactivate(User $user): void
    {
        $this->is_active = false;
        $this->activated_at = now();
        $this->activated_by_user_id = $user->id;
        $this->save();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadAnalytics extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'lead_id',
        'generated_response',
        'analysis_data',
        'sentiment',
        'urgency_score',
        'complexity_score',
        'key_points',
        'ai_model_used',
        'processing_status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'analysis_data' => 'array',
        'urgency_score' => 'integer',
        'complexity_score' => 'integer',
    ];

    /**
     * Статусы обработки
     */
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    /**
     * Типы тональности
     */
    const SENTIMENT_POSITIVE = 'positive';
    const SENTIMENT_NEUTRAL = 'neutral';
    const SENTIMENT_NEGATIVE = 'negative';

    /**
     * Получить заявку, к которой относится аналитика
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }
}

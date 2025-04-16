<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lead extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'source',
        'name',
        'email',
        'phone',
        'message',
        'custom_fields',
        'status',
        'category',
        'summary',
        'generated_response',
        'relevance_score',
        'first_response_at',
        'resolved_at',
        'response_time_minutes',
        'resolution_time_minutes',
        'status_changed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'custom_fields' => 'array',
        'relevance_score' => 'integer',
        'first_response_at' => 'datetime',
        'resolved_at' => 'datetime',
        'status_changed_at' => 'datetime',
    ];

    /**
     * Get the company that owns the lead.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Scope a query to only include leads for a specific company.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $companyId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Get the lead's status as a formatted string.
     *
     * @return string
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->getStatusLabel($this->status);
    }

    /**
     * Get status label for the given status value.
     *
     * @param string $status
     * @return string
     */
    public function getStatusLabel(string $status): string
    {
        return match($status) {
            'new' => 'Новая',
            'in_progress' => 'В работе',
            'completed' => 'Завершена',
            'archived' => 'В архиве',
            default => $status,
        };
    }

    /**
     * The tags that belong to the lead.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Get the files for the lead.
     */
    public function files(): HasMany
    {
        return $this->hasMany(LeadFile::class);
    }

    /**
     * Get the events for the lead.
     */
    public function events(): HasMany
    {
        return $this->hasMany(LeadEvent::class);
    }

    /**
     * Record a new event for this lead.
     *
     * @param  string  $eventType
     * @param  string|null  $previousValue
     * @param  string|null  $newValue
     * @param  string|null  $description
     * @param  array|null  $metadata
     * @return \App\Models\LeadEvent
     */
    public function recordEvent(string $eventType, ?string $previousValue = null, ?string $newValue = null, ?string $description = null, ?array $metadata = null): LeadEvent
    {
        return $this->events()->create([
            'company_id' => $this->company_id,
            'user_id' => auth()->id(),
            'event_type' => $eventType,
            'previous_value' => $previousValue,
            'new_value' => $newValue,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Get the comments for the lead.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(LeadComment::class);
    }

    /**
     * Calculates or returns the response time in minutes.
     *
     * @return int|null
     */
    public function getResponseTimeMinutesAttribute($value)
    {
        if ($value) {
            return $value;
        }

        if ($this->first_response_at && $this->created_at) {
            return $this->first_response_at->diffInMinutes($this->created_at);
        }

        return null;
    }

    /**
     * Calculates or returns the resolution time in minutes.
     *
     * @return int|null
     */
    public function getResolutionTimeMinutesAttribute($value)
    {
        if ($value) {
            return $value;
        }

        if ($this->resolved_at && $this->created_at) {
            return $this->resolved_at->diffInMinutes($this->created_at);
        }

        return null;
    }

    /**
     * Get the analytics for the lead.
     */
    public function analytics(): HasOne
    {
        return $this->hasOne(LeadAnalytics::class);
    }

    /**
     * Проверяет, есть ли у заявки аналитические данные.
     *
     * @return bool
     */
    public function hasAnalytics(): bool
    {
        return $this->analytics()->where('processing_status', LeadAnalytics::STATUS_COMPLETED)->exists();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class LeadFile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'lead_id',
        'filename',
        'original_filename',
        'filepath',
        'mime_type',
        'size',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'url',
        'is_image',
    ];

    /**
     * Get the lead that owns the file.
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * Get the URL for the file.
     *
     * @return string
     */
    public function getUrlAttribute(): string
    {
        return route('lead.file.download', $this->id);
    }

    /**
     * Check if the file is an image.
     *
     * @return bool
     */
    public function getIsImageAttribute(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Get the full path to the file.
     *
     * @return string
     */
    public function getFullPath(): string
    {
        return storage_path('app/' . $this->filepath);
    }

    /**
     * Delete the file from storage when the model is deleted.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($file) {
            Storage::delete($file->filepath);
        });
    }
}

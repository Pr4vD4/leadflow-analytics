<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'description',
        'description_quality_score',
        'ai_analysis',
        'telegram_chat_id',
        'is_active',
        'settings',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'description_quality_score' => 'integer',
        'ai_analysis' => 'json',
        'settings' => 'json',
    ];

    /**
     * Generate a unique API key for the company.
     *
     * @return string
     */
    public static function generateApiKey(): string
    {
        return Str::random(32);
    }

    /**
     * Set a new API key for the company.
     *
     * @return $this
     */
    public function setNewApiKey()
    {
        $this->api_key = self::generateApiKey();
        return $this;
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (Company $company) {
            if (empty($company->api_key)) {
                $company->setNewApiKey();
            }
        });
    }

    /**
     * Get the leads for the company.
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    /**
     * Get the users for the company.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the invitations for the company.
     */
    public function invitations(): HasMany
    {
        return $this->hasMany(CompanyInvitation::class);
    }

    /**
     * Create a new invitation code for the company.
     */
    public function createInvitation(User $user): CompanyInvitation
    {
        return $this->invitations()->create([
            'code' => CompanyInvitation::generateUniqueCode(),
            'user_id' => $user->id,
            'is_active' => true,
        ]);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Password extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'titre',
        'site_web',
        'mot_de_passe_crypte',
        'user_id',
        'last_used',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        // Remove 'encrypted_data' since it's not used anymore
        // 'encrypted_data',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'last_used' => 'datetime',
    ];

    /**
     * Get the user that owns the password.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

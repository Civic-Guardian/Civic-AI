<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'reputation_score',
        'reports_submitted',
        'reports_verified',
        'badge_level',
        'role',
        'phone',
        'two_factor_enabled',
        'aadhaar_number',
        'id_card_verified',
        'email_notifications',
        'push_notifications',
        'hazard_alerts',
        'high_accuracy_location',
        'background_location',
        'offline_map_downloaded',
        'voice_alerts_enabled',
        'sound_alerts_enabled',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Add reputation points and update badge level.
     */
    public function addReputationPoints(int $points)
    {
        $this->reputation_score += $points;

        if ($this->reputation_score >= 1500) {
            $this->badge_level = 'Civic Champion';
        } elseif ($this->reputation_score >= 700) {
            $this->badge_level = 'Civic Guardian';
        } elseif ($this->reputation_score >= 300) {
            $this->badge_level = 'Safety Reporter';
        } else {
            $this->badge_level = 'Contributor';
        }

        $this->save();
    }
}

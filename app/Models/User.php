<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
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
        'profile_photo',
    ];

    protected $casts = ['birthdate' => 'date'];

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

    public function diaryEntries()
{
    return $this->hasMany(DiaryEntry::class);
}


    public function links() 
    {
        return $this->hasMany(links::class);
    }

    public function reminders()
    {
        return $this->hasMany(ReminderModel::class);
    }

    public function recalcTier(): void
    {
        $spent = (float) $this->lifetime_spent;
        $th    = config('membership.thresholds');

        $tier = 'bronze';
        if ($spent >= ($th['gold'] ?? 1000))   $tier = 'gold';
        elseif ($spent >= ($th['silver'] ?? 300)) $tier = 'silver';

        $this->member_tier = $tier;
        $this->save();
    }

    public function getAvatarUrlAttribute(): string
    {
        // เก็บไฟล์ไว้ที่ storage/app/public/...
        if ($this->profile_photo) {
            return asset('storage/'.$this->profile_photo);
        }
        return asset('images/default-avatar.png'); // ไฟล์ fallback
    }

public function getProfilePhotoUrlAttribute(): string
    {
        $f = $this->profile_photo;

        if (!$f) {
            return asset('images/default-avatar.png');
        }

        // ถ้าเก็บเป็น URL เต็มหรือ path เองแล้ว
        if (Str::startsWith($f, ['http://','https://','/storage/','/images/'])) {
            return $f;
        }

        // ปกติ: เก็บเป็นชื่อไฟล์ใน storage/app/public/avatars
        return Storage::url('avatars/'.$f);
    }

}

<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
    use HasFactory, Notifiable, HasUuids;
    protected $primaryKey = 'userID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // We handle createdAt manually

    protected $fillable = ['username', 'email', 'password', 'fullName', 'phone', 'role', 'rating', 'bio', 'avatar', 'createdAt'];
    protected $hidden = ['password', 'remember_token'];

    protected $appends = ['avatar_url'];

    /**
     * Get the avatar URL. Returns stored avatar or falls back to UI Avatars.
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        $name = urlencode($this->fullName ?? 'User');
        return "https://ui-avatars.com/api/?name={$name}&background=4f46e5&color=ffffff&bold=true&size=128";
    }

    public function employer() { return $this->hasOne(Employer::class, 'employerID', 'userID'); }
    public function jobSeeker() { return $this->hasOne(JobSeeker::class, 'jobSeekerID', 'userID'); }
}
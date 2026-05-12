<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class JobSeeker extends Model {
    use HasUuids;
    protected $primaryKey = 'jobSeekerID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    
    protected $fillable = ['jobSeekerID', 'education', 'availabilityWeek'];
    protected $casts = ['availabilityWeek' => 'array'];

    public function user() { return $this->belongsTo(User::class, 'jobSeekerID', 'userID'); }
    public function applications() { return $this->hasMany(Application::class, 'jobSeekerID', 'jobSeekerID'); }
}
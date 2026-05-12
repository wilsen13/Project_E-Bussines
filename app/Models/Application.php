<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Application extends Model {
    use HasUuids;
    protected $primaryKey = 'applicationID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    
    protected $fillable = ['jobID', 'jobSeekerID', 'letter', 'status', 'isActive', 'createdAt'];

    public function job() { return $this->belongsTo(Job::class, 'jobID', 'jobID'); }
    public function jobSeeker() { return $this->belongsTo(JobSeeker::class, 'jobSeekerID', 'jobSeekerID'); }
}
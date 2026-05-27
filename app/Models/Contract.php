<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Contract extends Model
{
    use HasUuids;
    
    protected $primaryKey = 'contractID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    
    protected $fillable = ['contractID', 'jobID', 'employerID', 'jobSeekerID', 'status', 'proof_of_work', 'proof_file_path', 'revision_notes', 'isActive', 'startAt', 'endAt'];

    public function job() { return $this->belongsTo(Job::class, 'jobID', 'jobID'); }
    public function employer() { return $this->belongsTo(Employer::class, 'employerID', 'employerID'); }
    public function jobSeeker() { return $this->belongsTo(JobSeeker::class, 'jobSeekerID', 'jobSeekerID'); }
    public function payment() { return $this->hasOne(Payment::class, 'contractID', 'contractID'); }
}

<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Employer extends Model {
    use HasUuids;
    protected $primaryKey = 'employerID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    
    protected $fillable = ['employerID', 'displayName', 'address'];

    public function user() { return $this->belongsTo(User::class, 'employerID', 'userID'); }
    public function jobs() { return $this->hasMany(Job::class, 'employerID', 'employerID'); }
}
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Job extends Model {
    use HasUuids;
    protected $primaryKey = 'jobID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    
    protected $fillable = ['employerID', 'categoryID', 'locationID', 'title', 'description', 'payAmount', 'jobStatus', 'is_remote', 'image_url', 'createdAt'];

    public function employer() { return $this->belongsTo(Employer::class, 'employerID', 'employerID'); }
    public function category() { return $this->belongsTo(Category::class, 'categoryID', 'categoryID'); }
    public function location() { return $this->belongsTo(Location::class, 'locationID', 'locationID'); }
    public function applications() { return $this->hasMany(Application::class, 'jobID', 'jobID'); }
}
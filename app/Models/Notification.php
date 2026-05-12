<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Notification extends Model {
    use HasUuids;
    protected $primaryKey = 'notificationID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    
    protected $fillable = ['userID', 'title', 'message', 'sentAt'];

    public function user() { return $this->belongsTo(User::class, 'userID', 'userID'); }
}
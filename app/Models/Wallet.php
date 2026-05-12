<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Wallet extends Model
{
    use HasUuids;
    
    protected $primaryKey = 'walletID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    
    protected $fillable = ['walletID', 'userID', 'balance'];

    public function user() { 
        return $this->belongsTo(User::class, 'userID', 'userID'); 
    }
}

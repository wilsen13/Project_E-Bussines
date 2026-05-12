<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Payment extends Model
{
    use HasUuids;
    
    protected $primaryKey = 'paymentID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    
    protected $fillable = ['paymentID', 'walletID', 'contractID', 'amount', 'status', 'createdAt'];

    public function wallet() { return $this->belongsTo(Wallet::class, 'walletID', 'walletID'); }
    public function contract() { return $this->belongsTo(Contract::class, 'contractID', 'contractID'); }
}

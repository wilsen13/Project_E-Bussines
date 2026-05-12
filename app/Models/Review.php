<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $table = 'reviews';
    protected $primaryKey = 'reviewID';
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['reviewID', 'contractID', 'reviewerUserID', 'revieweeUserID', 'rating', 'comment', 'createdAt'];

    public function reviewer() { return $this->belongsTo(User::class, 'reviewerUserID', 'userID'); }
    public function reviewee() { return $this->belongsTo(User::class, 'revieweeUserID', 'userID'); }
    public function contract() { return $this->belongsTo(Contract::class, 'contractID', 'contractID'); }
}

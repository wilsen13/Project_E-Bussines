<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Location extends Model {
    use HasUuids;
    protected $primaryKey = 'locationID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    
    protected $fillable = ['addressLine', 'city', 'province', 'postalCode', 'latitude', 'longitude'];
}
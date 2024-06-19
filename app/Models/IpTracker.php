<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IpTracker extends Model
{
    use HasFactory;
    protected $fillable = [
        'ipaddress',
        'times',
    ];
}

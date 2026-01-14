<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prize extends Model
{
    protected $fillable = [
        'img',
        'name',
        'point',
        'stock',
    ];
}

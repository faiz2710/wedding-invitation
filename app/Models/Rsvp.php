<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rsvp extends Model
{
    protected $fillable = [
        'name',
        'attendance',
        'guests',
        'message',
    ];
}

<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'id',
        'created_at'
    ];

    public $incrementing = false;
    public $timestamps = false;
    protected $table = 'messages';

    protected $casts = [
        'created_at' => 'datetime',
    ];


}
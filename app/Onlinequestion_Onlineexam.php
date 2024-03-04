<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Onlinequestion_Onlineexam extends Model
{
    use HasFactory;
    protected $fillable = [
        'onlinequestion_id',
        'onlinexam_id',
        'marks'
];
    protected $table = 'onlinequestion_onlinexam';
}

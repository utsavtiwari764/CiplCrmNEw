<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subqualification extends Model
{
    use HasFactory;
    public function qualification(){
        return $this->belongsTo(Qualification::class, 'qualification_id');
    }
    protected $guarded = ['id'];
}

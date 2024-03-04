<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobApplicationsubqualification extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function subqualification(){
        return $this->belongsTo(Subqualification::class, 'subqualifications_id');
    }
}

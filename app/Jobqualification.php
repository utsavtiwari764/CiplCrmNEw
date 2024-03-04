<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jobqualification extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function qualificationdetails(){
        return $this->belongsTo(Qualification::class, 'qualification_id');
    }
}

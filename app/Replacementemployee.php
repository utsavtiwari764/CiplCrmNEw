<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class Replacementemployee extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function jobreplacements(){
        return $this->belongsToMany(Job::class, 'id');
    }
    public function getResignDateAttribute($value)
    {  
        return \Carbon\Carbon::parse($value)->format('d/m/Y');        
    }
    public function getLastWorkingDateAttribute($value)
    {  
        return \Carbon\Carbon::parse($value)->format('d/m/Y');        
    }
}

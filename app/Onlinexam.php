<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Onlinequestion;
class Onlinexam extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'total_question',
        'time_duration',
        'scheduled_time',
        'scheduled_date',
        'attempt',
        'description',
        'status',
      
     ];
  
    public function onlinequestion()
    {
        return $this->belongsToMany(Onlinequestion::class)->with('category')->orderByRaw('RAND()');
    }

    // public function roles()
    // {
    //     return $this->belongsToMany(Role::class, 'role_user');
    // }
  public function onlinequestionby()
    {
        return $this->belongsToMany(Onlinequestion::class)->with('onlinexamjobapplicationresult');
    }
  
    
    public function getScheduledDateAttribute($value)
    {  
       
        return \Carbon\Carbon::parse($value)->format('d/m/Y');
        
    }
  
}

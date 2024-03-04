<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Erf extends Model
{
    use HasFactory;
    protected $fillable = ['department','requisition_date','project_name','ref_no','required_position_details','erf_type','target_date','level','project_manager_name','team_lead','total_positions','category_id','location_id','jobtype_id','positionbudget','reasonfor','replacement','status'];
}

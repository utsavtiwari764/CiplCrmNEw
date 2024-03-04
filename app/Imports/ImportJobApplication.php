<?php

namespace App\Imports;

use App\JobApplication;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Validator;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Notifications\NewUserNotification;
use DB;
use App\Models\UserProfile;
use App\Models\Usereducation;
use App\Models\Userskill;
use App\Models\Useremploymentdetail;
use App\Models\Usercertification;
use App\Models\Userprojectdetail;
use App\Models\Userprojecttechnology;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;
use App\JobLocation;
use App\Helper\Files;
use App\Helper\Reply; 
use App\ApplicationStatus;
use App\InterviewSchedule;
use App\ApplicationSetting;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Notifications\ScheduleInterview;
use App\Http\Requests\UpdateJobApplication;
use App\Notifications\CandidateStatusChange;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CandidateScheduleInterview;
use App\Http\Requests\InterviewSchedule\StoreRequest;
use App\JobApplicationsubqualification; 
use App\JobCategory;
use App\Notifications\RatingNotification;
use App\Subqualification;
use App\Qualification;
class ImportJobApplication implements ToCollection,WithHeadingRow,WithValidation
{
    public function collection(Collection $rows)
    {
        
        foreach ($rows as $row) 
        {
              
                if (Qualification::where('name',$row['qualification'])->count()!=0) {

                $category = Qualification::where('name',$row['qualification'])->first();

                $jobrecruitment_id = session()->get('jobrecruitment_id');
               $job_id= session()->get('job_id');
                $jobApplication = new JobApplication();
                $jobApplication->full_name = $row['first_name'];
                $jobApplication->lastname = $row['last_name'];

                $jobApplication->job_id = $job_id;
                $jobApplication->status_id = '1'; //applied status id
                $jobApplication->email = $row['email'];
                $jobApplication->phone = $row['phone'];
                $jobApplication->fatherfirst= $row['father_first_name'];
                $jobApplication->fatherlast= $row['father_last_name'];

                $jobApplication->column_priority = '0';
                $jobApplication->relevent_exp=$row['relevent_exp'];
                $jobApplication->total_exp=$row['total_exp'];
                $jobApplication->qualification_id=$category->id;
                $jobApplication->jobrecruitment_id=$jobrecruitment_id;
                $jobApplication->save();
		 if($row['sub_qualification'] !='' || $row['sub_qualification'] !=null){
                    $subcatery_array=explode(",",$row['sub_qualification']);
                    if($subcatery_array){
                       
                        foreach($subcatery_array as $rowdata){
                          
                            if (Subqualification::where('name',$rowdata)->count()!=0) {
                              
                                $subcategory = Subqualification::where('name',$rowdata)->first();
                               
                                $jobapplicationqualification = new JobApplicationsubqualification();
                                $jobapplicationqualification->jobapplication_id = $jobApplication->id;
                                $jobapplicationqualification->status='1';
                                $jobapplicationqualification->subqualifications_id = $subcategory->id;
                              
                                $jobapplicationqualification->save();
                                                           }
                        }
                    }
                }
               


	        }
        	 
        }
        

    }

    public function rules(): array
    {       
     
        $qualificationarr = array();
        $qualification = Qualification::get(['id','name']);
        foreach ($qualification as $c) {
           
            array_push($qualificationarr, $c->name);
        }
       
       

        return [

             'qualification'=>'required|in:'.implode(',',$qualificationarr), 
              //'qualification'=>'in:'.implode(',',$qualificationarr),           
            'first_name'=>'required',
            'phone'=>'required',
            'email'=>'required',
            
            'relevent_exp'=>'required',
            'total_exp'=>'required',                              
        ];
    }


    public function withValidator($validator)
    {
        
        $validator->after(function ($validator) {
            
            $datas = $validator->getData();

            $dataarr = array();
            
            foreach ($datas as $data =>$value) {

                
                $category = Qualification::where('name',$value['qualification'])->first();

                if ($category==null) {
                    $validator->errors()->add($data, 'Qualification does not exists.');
                    
                } else{
                    $tell =true;
                }


                


            }
                       
        });
    }


}

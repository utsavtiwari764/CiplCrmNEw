<?php

namespace App\Http\Requests;

use App\Job;
use App\JobApplication;
use App\Question;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class UpdateJobApplication extends CoreRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $job = Job::where('id', $this->job_id)->first();
        $application = JobApplication::select('id', 'job_id', 'photo')->with(['resumeDocument'])->where('id', $this->route('job_application'))->first();
       
        $rules = [
            'full_name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'job_id' => 'required|exists:jobs,id',
            'qualification_id'=>'required',
            'subqualification_id.0'=>'required',
        ];

     

        return $rules;
    }

    public function messages()
    {
        return [
            'answer.*.required' => 'This answer field is required.',
            'dob.required' => 'Date of Birth field is required.',
            'country.min' => 'Please select country.',
            'state.min' => 'Please select state.',
            'city.required' => 'Please enter city.',
        ];
    }
}

<?php

namespace App\Http\Requests;

use App\Job;

use Illuminate\Support\Arr;

class StoreJobApplication extends CoreRequest
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
       
         
        return [
            'fatherfirst' => 'required',
            
	    'email' => 'required|email|unique:job_applications,email',
            'phone' => 'numeric|required',
            'job_id' => 'required|exists:jobs,id',
           // 'full_name' => 'unique:job_applications,full_name,NULL,id,fatherfirst,' . $request->input('fatherfirst'),
	 'full_name' => 'required|string|unique:job_applications,full_name,NULL,id,fatherfirst,' . $this->input('fatherfirst'),

        ];
       
 
 
    }

    
}

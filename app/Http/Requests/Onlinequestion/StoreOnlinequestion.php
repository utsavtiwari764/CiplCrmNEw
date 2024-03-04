<?php

namespace App\Http\Requests\Onlinequestion;

use Illuminate\Foundation\Http\FormRequest;

class StoreOnlinequestion extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            // 'option_a'  => 'required',
            // 'option_b' => 'required',
            // 'option_c' => 'required',
            // 'option_d' => 'required',
            // // 'question' => 'required|unique:onlinequestions,question',
            'question'=>'required',
            'question_type'=>'required',
            'answer'   =>'required',
            'jobcategory_id'   => 'required'
        ];
    }
}

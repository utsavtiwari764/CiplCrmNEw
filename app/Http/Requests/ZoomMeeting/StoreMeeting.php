<?php

namespace App\Http\Requests\ZoomMeeting;

use Froiden\LaravelInstaller\Request\CoreRequest;
use Illuminate\Foundation\Http\FormRequest;

class StoreMeeting extends CoreRequest
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
        // $setting = company_setting();
        return [
            'meeting_title' => 'required',
            'start_date' => 'required',
            'end_date' => 'required|after_or_equal:start_date',
            'all_employees' => 'sometimes',
            // 'employee_id.0' => 'required_without_all:all_employees,all_clients,client_id.0',

        ];
    }

    public function messages()
    {
        return [
            // 'employee_id.0.required_without_all' => __('zoom::modules.zoommeeting.attendeeValidation'),
        ];
    }
}

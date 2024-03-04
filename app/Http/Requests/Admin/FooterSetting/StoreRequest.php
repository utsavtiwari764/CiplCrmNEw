<?php

namespace App\Http\Requests\Admin\FooterSetting;

use App\Package;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{

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
            'name'        => 'required|unique:footer_settings,name',
            'description' => 'required',
        ];
    }
}

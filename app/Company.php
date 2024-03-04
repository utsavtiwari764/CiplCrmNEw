<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['company_name', 'company_email', 'company_phone', 'website', 'address', 'show_in_frontend','status','logo'];
    protected $appends = [
        'logo_url'
    ];

    public function getLogoUrlAttribute()
    {
        if (is_null($this->logo)) {
            return asset('logo-not-found.png');
        }
        return asset_url_local_s3('company-logo/' . $this->logo);
    }
}

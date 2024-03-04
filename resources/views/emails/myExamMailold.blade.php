@component('mail::message')
# Introduction

Kindly follow the link for your online exam

@component('mail::button', ['url' =>  $details['url']])
Click Here

@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent

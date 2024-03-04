@component('mail::message')
# {{ $details['title'] }}
  
The body of your message. 
   
@component('mail::button', ['url' => $details['url']])
Click Here
@endcomponent
   
Thanks,<br>
{{ config('app.name') }}
@endcomponent
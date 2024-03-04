@component('mail::layout')
@slot('header')
        @component('mail::header', ['url' => $details['url']])
            {{ config('app.name') }}
        @endcomponent
    @endslot

# {{ $details['title'] }}
  
The body of your message. 
   
@component('mail::button', ['url' => $details['url']])
Click Here
@endcomponent
   
Thanks,<br>
  @slot('footer')
        @component('mail::footer')
        {{ config('app.name') }} 
        @endcomponent
    @endslot
@endcomponent
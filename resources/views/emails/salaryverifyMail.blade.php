@component('mail::layout')
@slot('header')
        @component('mail::header', ['url' => $details['url']])
            {{ config('app.name') }}
        @endcomponent
    @endslot

# {{ $details['title'] }}
  
  You have been identified to verify the salary of the <strong>{{$job->full_name}}</strong> who has been interviewed on {{ \Carbon\Carbon::parse($job->interviewround2->schedule_date)->format('d-m-Y') }} at {{ \Carbon\Carbon::parse($job->interviewround2->schedule_date)->format('h:i A') }}.
   
@component('mail::button', ['url' => $details['url']])
Click Here
@endcomponent
   
Thank You!

Regards,
Corporate Infotech Pvt. Ltd.
  @slot('footer')
        @component('mail::footer')
        {{ config('app.name') }} 
        @endcomponent
    @endslot
@endcomponent
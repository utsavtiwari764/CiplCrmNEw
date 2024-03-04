@component('mail::layout')
@slot('header')
        @component('mail::header', ['url' => $details['url']])
            {{ config('app.name') }}
        @endcomponent
    @endslot

 
 

Dear Sir/Mam,
New ERF has been generated for multiple positions,
@if(!empty($details['detailsposistion']))
@component('mail::table')
| Position Name  | No of Position  |  Location
| :--------------| :-------------- |  :--------
@foreach ($details['detailsposistion'] as $detailsdata)
| {{$detailsdata['degination']}} | {{$detailsdata['total_positions']}} |  {{$detailsdata['location']}}
@endforeach
@endcomponent
@endif
at {{@$details['jobdetails']->project_name}}. Kindly @component('mail::button', ['url' => $details['url']])
Click Here
@endcomponent

to view the ERF and perform the necessary action.
 
Thanks,<br>
  @slot('footer')
        @component('mail::footer')
        {{ config('app.name') }} 
        @endcomponent
    @endslot
@endcomponent
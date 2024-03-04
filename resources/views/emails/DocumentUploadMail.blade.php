@component('mail::layout')
@slot('header')
        @component('mail::header', ['url' => $details['url']])
            {{ config('app.name') }}
        @endcomponent
    @endslot

 
  
    We are happy to inform you that you have been selected for the <strong>{{$details['position']}}</strong> at CIPL (Corporate Infotech Private Limited). 

Congratulations on your Selection!

To proceed with the Onboarding Process,Kindly upload the below mentioned documents:
<ul>
    <li>All Educational Documents (10th, 12th, Graduation, PG)</li>
    <li>Last 3 months salary slips</li>
    <li>Bank statement (last 6 months)</li>
    <li>Appointment letter from your current company</li>
    <li>Passport scan copy</li>
    <li>Cancelled cheque of your Bank Account</li>
    <li>PF, UAN, ESIC No (if applicable)</li>
    <li>Relieving Letter from all previous employers</li>
    <li>Scanned Passport Size Photograph</li>
    <li>PAN and Aadhar Copy (Mandatory)</li>
    <li>Certificates (if any)</li>
    <li>Updated CV</li>
</ul>

Kindly click on the link below to upload the necessary documents:
   
@component('mail::button', ['url' => $details['url']])
Document Upload Link
@endcomponent
   
We look forward to welcoming you to the CIPL team and are excited to have you onboard.
<br>
  @slot('footer')
        @component('mail::footer')
        {{ config('app.name') }} 
        @endcomponent
    @endslot
@endcomponent
<?php

namespace App\Notifications;

use App\JobApplication;
use App\SmsSetting;
use App\Traits\SmsSettings;
use App\Traits\SmtpSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\NexmoMessage;

class ScheduleInterview extends Notification
{
    use Queueable, SmtpSettings, SmsSettings;
protected $interviewSchedule;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(JobApplication $jobApplication ,$interviewSchedule , $meetings)
    {
        $this->jobApplication = $jobApplication;
        $this->smsSetting = SmsSetting::first();
        $this->meetings = $meetings;
         $this->interviewSchedule = $interviewSchedule;

        $this->setMailConfigs();
        $this->setSmsConfigs();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $via = ['mail', 'database'];

        if ($this->smsSetting->nexmo_status == 'active' && $notifiable->mobile_verified == 1) {
            array_push($via, 'nexmo');   
        }
        
        return $via;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {

        if($this->meetings['interview_type']=='interview round 1'){

            $emailContent = (new MailMessage)
            ->subject('Interview Schedule')
            ->line('You have been identified to evaluate/interview ' . @$notifiable->name . ' for the '. $this->jobApplication->job->department->name .' position. interview round 1')
            ->line('Please complete an interview for the candidate at the date and time '.$this->interviewSchedule->schedule_date->format('M d, Y h:i a').'. After the interview, the candidate's performance can be recorded at the link below.');
            if($this->meetings != null){
               
                 $emailContent = $emailContent->action(__('modules.zoommeeting.joinUrl'), url($this->meetings['meetingurl']));
              
           }else{
               $emailContent = $emailContent->line(__('modules.interviewSchedule.interviewType').' - ' . __('modules.meetings.offline'));
            
           }
           $emailContent = $emailContent->line('The candidate is reachable at Mobile No '.$this->jobApplication->phone.'  and Email '.$this->jobApplication->email.'.');
           $emailContent = $emailContent->line(__('email.thankyouNote'));
         return $emailContent;

        }
	else
	{
         $emailContent = (new MailMessage)
            ->subject('Interview Schedule')
            ->line('You have been identified to evaluate/interview ' . @$notifiable->name . 'for the '. $this->jobApplication->job->department->name .' position. interview round 2')
            ->line('Please complete an interview for the candidate at the date and time '.$this->interviewSchedule->schedule_date->format('M d, Y h:i a').'. After the interview, the candidate s performance can be recorded at the link below.');
            if($this->meetings != null){
               
                 $emailContent = $emailContent->action(__('modules.zoommeeting.joinUrl'), url($this->meetings['meetingurl']));
              
           }else{
               $emailContent = $emailContent->line(__('modules.interviewSchedule.interviewType').' - ' . __('modules.meetings.offline'));
            
           }
           $emailContent = $emailContent->line('The candidate is reachable at Mobile No'.$this->jobApplication->phone.'  and Email '.$this->jobApplication->email.'.');
           $emailContent = $emailContent->line(__('email.thankyouNote'));
         return $emailContent;
          }
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'data' => $this->jobApplication->toArray()
        ];
    }

    /**
     * Get the Nexmo / SMS representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return NexmoMessage
     */
    public function toNexmo($notifiable)
    {
        return (new NexmoMessage)
                    ->content(
                        __($this->jobApplication->full_name).' '.__('email.interviewSchedule.text').' - ' . ucwords($this->jobApplication->job->title)
                    )->unicode();
    }
}

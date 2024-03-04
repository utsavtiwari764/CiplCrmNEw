<?php

namespace App\Notifications;

use App\InterviewSchedule;
use App\JobApplication;
use App\Traits\SmtpSettings;
use Illuminate\Bus\Queueable;
use App\ZoomMeeting;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Carbon\Carbon;

class CandidateScheduleInterview extends Notification
{
    use Queueable, SmtpSettings;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(JobApplication $jobApplication,InterviewSchedule $interviewSchedule , $meetings)
    {
        $this->jobApplication = $jobApplication;
        $this->interviewSchedule = $interviewSchedule;
        $this->meetings = $meetings;

        $this->setMailConfigs();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
       $dateTimeString = $this->interviewSchedule->schedule_date;
           $carbon = \Carbon\Carbon::parse($dateTimeString);
           $date = $carbon->format('M d, Y'); // Contains the date part, e.g., "2023-09-14"
           $time = $carbon->format('h:i A'); // Contains the time part, e.g., "15:30:00"

        if($this->meetings['interview_type']=='interview round 1'){
        $emailContent = (new MailMessage)
            ->subject('Interview Schedule || Round 1')
           ->line('Hi ' .$this->jobApplication->full_name)
           ->line('As part of our evaluation for the  ' . $this->jobApplication->job->department->name .' Position.')
           ->line('The Interview/Evaluation Round 1 has been scheduled for you on  ' . $date  .' at '.$time.'. Please make yourself available at the specified time slot.');    
             if($this->meetings['meetingurl'] != 'null'){

               $emailContent = $emailContent->action(__('modules.zoommeeting.joinUrl'), url($this->meetings['meetingurl']));

            }
            else{
               $emailContent = $emailContent->line(__('modules.interviewSchedule.interviewType').' - ' . __('modules.meetings.offline'));
            
           }

         // $emailContent = $emailContent->line('In case of questions, please reach out to '.$this->interviewSchedule->employees[0]->name .' '. $this->interviewSchedule->employees[0]->email);

            $emailContent = $emailContent->line(__('email.thankyouNote'));

            return $emailContent;
            }
         if($this->meetings['interview_type']=='interview round 2'){
        $emailContent = (new MailMessage)
            ->subject('Interview Schedule || Round 2')
           ->line('Hi ' .$this->jobApplication->full_name)
           ->line('As part of our evaluation for the  ' . $this->jobApplication->job->department->name .' Position.')
           ->line('The Interview/Evaluation Round 2 has been scheduled for you on  ' . $date  .' at '.$time.'. Please make yourself available at the specified time slot.');    
             if($this->meetings['meetingurl'] != 'null'){
               $emailContent = $emailContent->action(__('modules.zoommeeting.joinUrl'), url($this->meetings['meetingurl']));

            }
	    else{
               $emailContent = $emailContent->line(__('modules.interviewSchedule.interviewType').' - ' . __('modules.meetings.offline'));
            
           }

         // $emailContent = $emailContent->line('In case of questions, please reach out to '.$this->interviewSchedule->employees[0]->name .' '. $this->interviewSchedule->employees[0]->email);

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

}
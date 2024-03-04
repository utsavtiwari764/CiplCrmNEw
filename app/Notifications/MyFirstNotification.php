<?php
   
namespace App\Notifications;
   
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
   
class MyFirstNotification extends Notification
{
    use Queueable;
  
    private $details;
   
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($jobApplication,$interviewSchedule,$meetings)
    {
        $this->jobApplication = $jobApplication;
        $this->interviewSchedule = $interviewSchedule;
        $this->meetings = $meetings;
    }
   
    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail','database'];
    }
   
    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {

       // print_r($this->interviewSchedule->schedule_date);die();
        return (new MailMessage) 
                   ->subject(__('email.interviewSchedule.subject'))
                    ->greeting(__('email.hello').' ' . ucwords($this->jobApplication['full_name']) . '!')
                    ->line(__('email.your').' '.__('email.interviewSchedule.text').' - ')
                    ->line(__('email.on').' - ' . $this->interviewSchedule->schedule_date->format('M d, Y h:i a'))
                     ->action('Click Here', $this->meetings['meetingurl']);
                   
 
    }
  
    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'data' => $this->jobApplication->toArray()
        ];
    }
}
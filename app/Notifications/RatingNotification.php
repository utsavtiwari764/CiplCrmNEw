<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Traits\SmtpSettings;
class RatingNotification extends Notification
{
    use Queueable,SmtpSettings;
    protected $jobApplication;
     protected $ratingdetail;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($jobApplication,$ratingdetail)
    {
         $this->jobApplication = $jobApplication;
          $this->ratingdetail=$ratingdetail;
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
        return (new MailMessage) 
                     
                     ->line('Job Application Name' .' ' . ucwords($this->jobApplication->full_name))
                     ->line('Tottal Rating' .' ' . $this->jobApplication->rating .' ' .'Points')
                    ->line('OVERALL PERSONALITY' .' ' . $this->ratingdetail->overall_personality .' ' .'Points')
                    ->line('MOBILITY' .' ' . $this->ratingdetail->mobility .' ' .'Points')
                    ->line('SELF_CONCEPT' .' ' . $this->ratingdetail->self_concept .' ' .'Points')
                    ->line('OPENNESS TO FEEDBACK' .' ' . $this->ratingdetail->openness_to_feedback .' ' .'Points')
                    ->line('DRIVE' .' ' . $this->ratingdetail->drive .' ' .'Points')
                    ->line('LEADERSHIP_POTENTIAL' .' ' . $this->ratingdetail->leadership_potential .' ' .'Points')
                    ->line('PERSONAL EFFICACY' .' ' . $this->ratingdetail->personal_efficacy .' ' .'Points')
                    ->line('MATURITY UNDERSTANDING' .' ' . $this->ratingdetail->maturity_understanding .' ' .'Points')
                    ->line('COMPREHENSIBILITY_ELOQUENCE' .' ' . $this->ratingdetail->comprehensibility_eloquence .' ' .'Points')
                    ->line('KNOWLEDGE OF SUBJECT JOB PRODUCT' .' ' . $this->ratingdetail->knowledge_of_subject_job_product .' ' .'Points')
                    ->line('POISE MANNERISM' .' ' . $this->ratingdetail->poise_mannerism .' ' .'Points');                                          
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
            //
        ];
    }
}

<?php

namespace App\Notifications;
 
use App\SmsSetting;
use App\Traits\SmsSettings;
use App\Traits\SmtpSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\NexmoMessage;

class HoldNotification extends Notification
{
    use Queueable, SmtpSettings;
    protected $jobapplication;
    public function __construct($jobapplication)
    {
        
        $this->jobapplication = $jobapplication;
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
        ->subject('Hold') 
        ->line('Dear'.' '. ucwords($this->jobapplication->full_name))
        ->line('Thank you for your interest in CIPL (Corporate Infotech Private Limited). We appreciate your time and effort in applying for the '.$this->jobapplication->recruitment->degination.' role .')
        ->line('At this moment, our hiring process is currently on hold. We are diligently reviewing all applications and assessing our current needs. While we understand this may cause some inconvenience, please bear with us as we carefully evaluate each candidate.')
        ->line(' We will reach out to you as soon as we have further updates or if we require any additional information. We value your patience and understanding during this time.')
        ->line('Thank you for your interest in joining our team. We look forward to speaking with you soon.');
        
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        //
    }

    /**
     * Get the Nexmo / SMS representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return NexmoMessage
     */
     
}



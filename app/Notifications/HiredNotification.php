<?php

namespace App\Notifications;
 
use App\SmsSetting;
use App\Traits\SmsSettings;
use App\Traits\SmtpSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\NexmoMessage;

class HiredNotification extends Notification
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
        ->subject('Selected') 
        ->line('Dear'.' '. ucwords($this->jobapplication->full_name))         
        ->line('We are delighted to inform you that you have been selected for the Position of '.$this->jobapplication->recruitment->degination.' at CIPL (Corporate Infotech Private Limited). Your impressive performance during your Interview process has led to this decision.')
        ->line('Our HR Team will contact you shortly to share further information and initiate the offer letter process.');
        
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



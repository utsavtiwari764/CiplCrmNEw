<?php

namespace App\Notifications;
 
use App\SmsSetting;
use App\Traits\SmsSettings;
use App\Traits\SmtpSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\NexmoMessage;

class RejectNotification extends Notification
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
        if($this->jobapplication->status_id=='23'){
            return (new MailMessage)
            ->subject('Rejected') 
            ->line('Dear'.' '. ucwords($this->jobapplication->full_name))
            ->line('We want to express our gratitude for your interest in CIPL (Corporate Infotech Private Limited) and for taking the time to interview with us. We appreciate your enthusiasm and the effort you put into the application process.')
          
            ->line('After careful consideration, we regret to inform you that we have chosen to move forward with another candidate for the '. $this->jobapplication->recruitment->degination .' role. We understand that this news may be disappointing, but please know that the decision was a difficult one as we were highly impressed with your qualifications and experience.')
            ->line('We would like to keep your information on file for future opportunities that may be a better fit.')
            ->line('We appreciate your understanding and wish you the very best in your job search.')        
            ->line('Thank you once again for considering a career with CIPL (Corporate Infotech Private Limited). We wish you every success in your future endeavors.');
        } else if($this->jobapplication->status_id=='24'){
            return (new MailMessage)
            ->subject('Rejected') 
            ->line('Dear'.' '. ucwords($this->jobapplication->full_name))
            ->line('We want to express our gratitude for your interest in CIPL (Corporate Infotech Private Limited) and for taking the time to interview with us. We appreciate your enthusiasm and the effort you put into the application process.')
          
            ->line('After careful consideration, we regret to inform you that we have chosen to move forward with another candidate for the '. $this->jobapplication->recruitment->degination .'  role. We understand that this news may be disappointing, but please know that the decision was a difficult one as we were highly impressed with your qualifications and experience.')
            ->line('We would like to keep your information on file for future opportunities that may be a better fit.')
            ->line('We appreciate your understanding and wish you the very best in your job search.')        
            ->line('Thank you once again for considering a career with CIPL (Corporate Infotech Private Limited). We wish you every success in your future endeavors.');
       
        }else{
            return (new MailMessage)
            ->subject('Rejected') 
            ->line('Dear'.' '. ucwords($this->jobapplication->full_name))
            ->line('Thank you for taking the time to apply for the '.$this->jobapplication->recruitment->degination.' position at CIPL (Corporate Infotech Private Limited) .')
            ->line('We appreciate your interest in our organization and the effort you put into your application.')
            ->line('After careful consideration, we regret to inform you that we have decided not to proceed with your application. Please note that this decision was based on a thorough evaluation of your qualifications and the requirements of the position.')
            ->line('We want to assure you that the decision was not made lightly, and we recognize the time and effort you invested in the application process. We encourage you to continue exploring other opportunities that align with your skills and experience.')        
            ->line('We genuinely appreciate your interest in our company and wish you the best in your future endeavors. Should a suitable position arise in the future, we will certainly keep your application in mind and may reach out to you.')
            ->line('We Thank you again for your interest in joining our team.');
    
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
        //
    }

    /**
     * Get the Nexmo / SMS representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return NexmoMessage
     */
     
}



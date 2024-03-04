<?php

namespace App\Notifications;

use App\JobApplication;
use App\SmsSetting;
use App\Traits\SmsSettings;
use App\Traits\SmtpSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\NexmoMessage;

class JobOffer extends Notification
{
    use Queueable, SmtpSettings;
//        , SmsSettings;

    /**
     * JobOffer constructor.
     * @param JobApplication $jobApplication
     */
    public function __construct(JobApplication $jobApplication)
    {
        $this->jobApplication = $jobApplication;
//        $this->smsSetting = SmsSetting::first();

        $this->setMailConfigs();
//        $this->setSmsConfigs();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $via = ['mail'];

//        if ($this->smsSetting->nexmo_status == 'active' && $notifiable->mobile_verified == 1) {
//            array_push($via, 'nexmo');
//        }

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
        return (new MailMessage)
            ->subject(__('email.jobOffer.subject'))
            ->greeting('Dear ' . ucwords($this->jobApplication->full_name) . '!')
            ->line('Congratulations !!')
          
->line('We are thrilled to inform you that you have been selected for the position of '.$this->jobApplication->recruitment->degination.' at CIPL (Corporate Infotech Private Limited) .')
        ->line('After a rigorous selection process, your exceptional skills, qualifications, and experience stood out among the many talented candidates. We are confident that you will make significant contributions to our team and help us achieve our goals.')
        ->line('Your enthusiasm, professionalism, and impressive achievements demonstrated during the interview process convinced us that you are the perfect fit for this role. We believe that your expertise and unique perspective will greatly benefit our organization.')
        ->line('Our HR department will contact you shortly to discuss the next steps, including the completion of any necessary paperwork and an overview of the onboarding process.
        ')
        ->line('Once again, congratulations on your well-earned success! We are excited to welcome you aboard and look forward to seeing your outstanding contributions to our team.')

            ->action(__('email.viewOffer'), 'https://cipcrm.org.in/job-offer/'.$this->jobApplication->onboard->offer_code)
            ->line(__('email.thankyouNote')); 

        
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
    public function toNexmo($notifiable)
    {
        return (new NexmoMessage)
                    ->content(
                        __($this->jobApplication->full_name).' '.__('email.jobOffer.text').' - ' . ucwords($this->jobApplication->job->pid)
                    )->unicode();
    }
}

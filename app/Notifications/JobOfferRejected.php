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

class JobOfferRejected extends Notification
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
        $via = ['mail', 'database'];

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
            ->subject(__('email.jobOfferAccepted.subject'))
            ->greeting(__('email.hello').' ' . ucwords($notifiable->name) . '!')
            ->line(__('email.jobOfferAccepted.text').' by '.__($this->jobApplication->full_name).' for PID ' . ucwords($this->jobApplication->job->pid))
            ->action(__('email.loginDashboard'),'https://cipcrm.org.in')
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
        return [
            'data' => array_merge($this->jobApplication->onboard->toArray(), ['full_name' => $this->jobApplication->full_name, 'pid' => ucwords($this->jobApplication->job->pid)])
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
                        __('email.jobOfferRejected.text').' by '.__($this->jobApplication->full_name).' for job' . ucwords($this->jobApplication->job->pid)
                    )->unicode();
    }
}

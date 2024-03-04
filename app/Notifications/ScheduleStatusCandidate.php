<?php

namespace App\Notifications;

use App\InterviewSchedule;
use App\JobApplication;
use App\Traits\SmtpSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ScheduleStatusCandidate extends Notification
{
    use Queueable, SmtpSettings;

    protected $jobApplication;
    protected $interviewSchedule;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($jobApplication, $interviewSchedule)
    {
        $this->jobApplication = $jobApplication;
        $this->interviewSchedule = $interviewSchedule;
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
        return ['mail', 'database'];
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
            ->subject(__('email.ScheduleStatusCandidate.subject'))
            ->greeting(__('email.hello').' ' . ucwords($notifiable->full_name) . '!')
            ->line(__('email.your').' '.__('email.ScheduleStatusCandidate.text'))
            ->line(__('email.ScheduleStatusCandidate.hasBeen').' - ' . ucFirst($this->jobApplication->status->status))
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
            'data' => $this->jobApplication->toArray()
        ];
    }

}
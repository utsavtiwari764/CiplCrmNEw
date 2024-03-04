<?php

namespace App\Notifications;

use App\InterviewSchedule;
use App\JobApplication;
use App\Traits\SmtpSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Carbon\Carbon;
class AdminStatusDocument extends Notification
{
    use Queueable, SmtpSettings;

    protected $jobApplication;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($jobApplication)
    {
        $this->jobApplication = $jobApplication;
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
           $dateTimeString =Carbon::now();
          $carbon = \Carbon\Carbon::parse($dateTimeString);
           $date = $carbon->format('M d, Y'); // Contains the date part, e.g., "2023-09-14"
           $time = $carbon->format('h:i A'); // Contains the time part, e.g., "15:30:00"


        return (new MailMessage)
            ->subject('Documents Uploaded Notification')

            ->greeting(__('Hi ').' ' .  ucwords($notifiable->name)  . '!')
        ->line(ucwords($this->jobApplication->full_name) .' has successfully uploaded the documents against the  '.$this->jobApplication->job->department->name.' on '.$date .' at '.$time.' .')
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
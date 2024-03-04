<?php

namespace App\Notifications;
 
use App\SmsSetting;
use App\Traits\SmsSettings;
use App\Traits\SmtpSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\NexmoMessage;
use Carbon\Carbon;
class InterviewRatingNotification extends Notification
{
    use Queueable, SmtpSettings;
    protected $interviewDate;
    protected $interviewerName;
    protected $interviewLink;
    protected $round;
    public function __construct($interviewDate, $interviewerName, $interviewLink,$round=null)
    {

        
        $this->interviewDate = $interviewDate;
        $this->interviewerName = $interviewerName;
        $this->interviewLink = $interviewLink;
        $this->round=$round;
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
         $dateTimeString = $this->interviewDate;
           $carbon = \Carbon\Carbon::parse($dateTimeString);
           $date = $carbon->format('d-m-Y'); // Contains the date part, e.g., "2023-09-14"
           $time = $carbon->format('h:i A'); // Contains the time part, e.g., "15:30:00"
        return (new MailMessage)
        ->subject('Interview Rating')
        ->line('Dear ' . @$notifiable->name . ',')
        ->line('You had an '.@$this->round.' on ' . $date  .' at '.$time . ' with ' . $this->interviewerName . '.')
        ->line('We value your feedback and would appreciate it if you could take a moment to rate your interview experience.')
        ->action('Rate Interview', $this->interviewLink)
        ->line('Thank you for your participation!');


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



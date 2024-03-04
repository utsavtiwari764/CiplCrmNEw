<?php

namespace App\Notifications;
 
use App\SmsSetting;
use App\Traits\SmsSettings;
use App\Traits\SmtpSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\NexmoMessage;

class SelectedApplication extends Notification
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
        if($this->jobapplication->status_id=='21'){
            return (new MailMessage)
            ->subject('Selected') 
            ->line('Dear'.' '. ucwords($this->jobapplication->full_name))
            ->line('Congratulations !!')
            ->line('Thank you for your Interest with CIPL (Corporate Infotech Private Limited) for the position of '.$this->jobapplication->recruitment->degination.'.')
            ->line('We would like to inform that you have been selected for next round of Interview process.')
            ->line('Our HR Department will now be reaching out to you to initiate the next step.');
           } else  if($this->jobapplication->status_id=='22'){
                return (new MailMessage)
                ->subject('Selected') 
                ->line('Dear'.' '. ucwords($this->jobapplication->full_name))
                ->line('Congratulations !!')
                ->line('We are happy to inform you that you have been selected for the position of  '.$this->jobapplication->recruitment->degination.' at CIPL (Corporate Infotech Private Limited). Your qualifications and experience for the role stood out during the interview process, and we believe you will be a valuable addition to our team.')
               
                ->line('Our HR department will now be reaching out to you to initiate the next steps in the hiring process, which include the completion of necessary documentation and the formal onboarding process. They will guide you through the required paperwork and provide you with all the information you need to make a smooth transition for this role.');
                }else{
                    return (new MailMessage)
                    ->subject('Selected') 
                    ->line('Dear'.' '. ucwords($this->jobapplication->full_name))
                    ->line('Congratulations !!')
                    ->line('We are thrilled to inform you that you have been selected for the position of '.$this->jobapplication->recruitment->degination.' at CIPL (Corporate Infotech Private Limited) .')
                    ->line('After a rigorous selection process, your exceptional skills, qualifications, and experience stood out among the many talented candidates. We are confident that you will make significant contributions to our team and help us achieve our goals.')
                    ->line('Your enthusiasm, professionalism, and impressive achievements demonstrated during the interview process convinced us that you are the perfect fit for this role. We believe that your expertise and unique perspective will greatly benefit our organization.')
                    ->line('Our HR department will contact you shortly to discuss the next steps, including the completion of any necessary paperwork and an overview of the onboarding process.
                    ')
                    ->line('Once again, congratulations on your well-earned success! We are excited to welcome you aboard and look forward to seeing your outstanding contributions to our team.');
                 
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
        return [
            //
        ];
    }
}

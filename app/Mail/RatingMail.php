<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use App\Traits\SmtpSettings;

class RatingMail extends Mailable
{
    use Queueable, SerializesModels, SmtpSettings;

     public $ratingdetail;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($ratingdetail)
    {
         
        $this->setMailConfigs();
         $this->ratingdetail = $ratingdetail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
       return $this->subject('Rating Notification')->markdown('emails.rating_details');
    }
}

<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Traits\SmtpSettings;
use App\JobApplication;
use Illuminate\Support\Facades\Config;

class MyExamMail extends Mailable
{
   use Queueable, SerializesModels, SmtpSettings;
    public $details;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
      
        //$this->setMailConfigs();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

	return $this->subject('Online Interview Test')->markdown('emails.myExamMail')->with('details', $this->details);

    }
}

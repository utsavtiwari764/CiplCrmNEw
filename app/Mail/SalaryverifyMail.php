<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SalaryverifyMail extends Mailable
{
    use Queueable, SerializesModels; 
    public $details;
    public $job;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details,$job)
    {
        $this->details = $details;
        $this->job =$job;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Salary verification link')->markdown('emails.salaryverifyMail')->with(['details'=>$this->details,'job'=>$this->job]);
        
    }
}

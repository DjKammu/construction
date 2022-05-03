<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MaitToSubcontractor extends Mailable
{
    use Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $heading = $this->data['heading'];
        $content = $this->data['content'];
        $subject = $this->data['subject'];
        $plans = $this->data['plans'];
        $file = $this->data['file'];

        $mail = $this->subject($subject)
            ->markdown('itb_tracker.mail', [
            'heading' => $heading,
            'content' => $content,
            'plans'   => $plans
          ]);

        if ($file) {
            $fileName = pathinfo($file,PATHINFO_FILENAME);
            $mail = $mail->attach($file, array(
                'as' => $fileName, // If you want you can chnage original name to custom name      
                'mime' => pathinfo($file, PATHINFO_EXTENSION))
            );
        }

        return $mail;
            

    }
}

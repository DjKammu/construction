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
        $plans =   $this->data['plans'];
        $file  =   $this->data['file'];
        $files   =    @$this->data['files'];
        $pdffile  =  @$this->data['pdffile'];
        $fileName =  @$this->data['fileName'];

        $setting = \App\Models\Setting::latest()->first();

        $mail = $this->subject($subject)
            ->replyTo(@$setting->from_email)
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

        if (isset($pdffile) && $pdffile && isset($fileName) && $fileName) {
            $mail =  $mail->attachData($pdffile,$fileName);
        }

        if (isset($files) && count($files) > 0) {
            foreach ($files as $filePath) {
                if(empty($filePath)){
                    continue;
                }
                $fileName = pathinfo($filePath,PATHINFO_FILENAME);
                $extension =  pathinfo($filePath, PATHINFO_EXTENSION);
                if($extension == 'pdf'){
                      $mail->attach($filePath,[ 'as' => $fileName.'.pdf',
                           'mime' => 'application/pdf'
                       ]);
                }else{
                     $mail->attach($filePath);
                } 
            }
        }

        return $mail;
            

    }
}

<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Subcontractor;
use App\Models\ITBTracker;
use App\Models\Project;
use App\Models\Trade;
use App\Models\User;
use App\Mail\MaitToSubcontractor;
use Illuminate\Http\Request;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * Create a new job instance.
     *
     * @return void
     */
     public function __construct()
    {
      
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Request $request)
    {
        $productId = $request->projectId;
        $senders = $request->senders;
        $trdSubArray = [];

        foreach (@$senders as $key => $sender) {
              $sender = explode(',', $sender);
              $trdSubArray[$sender[0]][] = $sender[1];
        }
       
        
        $project = Project::find($productId);

        foreach ($trdSubArray as $key => $trdSub) {
            
            $trade = Trade::find($key);

            foreach ($trdSub as $key => $sub) {
          
            $subcontractor = Subcontractor::find($sub);

           $email = ($subcontractor['email_1']) ? $subcontractor['email_1'] : (  ($subcontractor['email_2']) ? $subcontractor['email_2'] :   $subcontractor['email_3'] );


           $heading = 'Dear '.$subcontractor->name;
           $content = 'We are Building   . We are inviting you to bid on this project. The Bids are due by '.$project->project_due_date.'. Please click on Drobox URL Link for access to the Architectural Plans. Please look at the attached scope file and bid according to plans and scope file.';


            if($project->plans_url){
                $content .= '<br><a href='.$project->plans_url.'>PLANS</a>';
            }

            $subject = @ucwords($project->name);
            $file = @$trade->scope ? realpath(public_path(\Storage::url($trade->scope))) : '';

            $data = [
              'subject' => $subject,
              'heading' => $heading,
              'content' => $content,
              'file' => $file
            ];

            $mail = \Mail::to($email)->send(new MaitToSubcontractor($data));

           if (!\Mail::failures()) {
                ITBTracker::updateOrCreate(
                    ['project_id' => $productId, 'trade_id' => $trade->id , 
                      'subcontractors_id' => $subcontractor->id ],
                    [ 'mail_sent' => true ]
                );
            }
         }

        }

       
    }
}

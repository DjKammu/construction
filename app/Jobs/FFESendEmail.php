<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\FFEITBTracker;
use App\Models\Project;
use App\Models\FFEVendor;
use App\Models\FFETrade;
use App\Models\User;
use App\Mail\MaitToSubcontractor;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FFESendEmail implements ShouldQueue
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
            
            $trade = FFETrade::find($key);

            foreach ($trdSub as $key => $v) {

            $vendor = FFEVendor::find($v);
            $email =$vendor['email'];

           $heading = 'Dear '.$vendor->name;
           $content = 'We building a new <b>'.@$project->project_type->name.'</b>. We are inviting you to bid on the project called <b>'.@$project->name.'</b>. The Bids are due by <b>'.@Carbon::parse($project->due_date)->format('m-d-Y').'</b>. Please click on Drobox URL Link for access to the Architectural Plans. Please look at the attached scope file and bid according to plans and scope file.';


            if($project->plans_url){
               // $content .= '<br/><a href='.$project->plans_url.'>PLANS</a>';
            }

            $subject = @ucwords($project->name);
            $file = @$trade->scope ? realpath(public_path(\Storage::url($trade->scope))) : '';

            $data = [
              'subject' => $subject,
              'heading' => $heading,
              'content' => $content,
              'plans'   => @$project->plans_url,
              'project_email' =>($project->project_email_user) ?  @$project->projectEmailUser : '',
              'file' => $file
            ];
            
            if($email){
              $mail = \Mail::to($email)->send(new MaitToSubcontractor($data));
               if (!\Mail::failures()) {
                    FFEITBTracker::updateOrCreate(
                        ['project_id' => $productId, 
                        'ffe_trade_id' => $trade->id , 
                        'ffe_vendor_id' => $vendor->id ],
                        [ 'mail_sent' => true ]
                    );
                }
            }
           
         }

        }

       
    }
}

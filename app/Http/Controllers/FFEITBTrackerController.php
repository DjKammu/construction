<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\FFEITBTracker;
use App\Mail\MaitToSubcontractor;
use Gate;


class FFEITBTrackerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
         //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function sendMail(Request $request){
      
      set_time_limit(0);

      (new \App\Jobs\FFESendEmail())
                ->dispatch();

      return response()->json(
           [
            'status' => 200,
            'message' => 'Sent Successfully!'
           ]
       );

    }

    public function sendMailWithPdf(Request $request){
      
      set_time_limit(0);

        $data = [
          'heading' => '',
          'plans' => '',
          'file' => '',
          'subject' => $request->subject,
          'content' => $request->message,
          'pdffile' => $request->file
        ];

        $pdffile = (new PaymentController())->downloadPDF(4,true);

        dd($pdffile);
 

        dispatch(
          function() use ($request, $data){
           \Mail::to($request->recipient)->send(new MaitToSubcontractor($data));
          }
        )->afterResponse();

      return response()->json(
           [
            'status' => 200,
            'message' => 'Sent Successfully!'
           ]
       );

    } 

    public function bidRecieved(Request $request){
       
      $id = $request->tracker_id;
      $value = $request->value;

     $itb_tracker = FFEITBTracker::find($id);
      if(!$itb_tracker){
       return response()->json(
           [
            'status' => 200,
            'message' => 'ITBTracker not found!'
           ]
       );
     }
     $itb_tracker->bid_recieved = $value;
     $itb_tracker->save();

      return response()->json(
           [
            'status' => 200,
            'message' => 'Updated Successfully!'
           ]
       );

    }  

  public function contractSigned(Request $request){
       
      $id = $request->tracker_id;
      $value = $request->value;

     $itb_tracker = FFEITBTracker::find($id);

     if(!$itb_tracker){
       return response()->json(
           [
            'status' => 200,
            'message' => 'ITBTracker not found!'
           ]
       );
     }

     $itb_tracker->contract_sign = $value;
     $itb_tracker->save();

      return response()->json(
           [
            'status' => 200,
            'message' => 'Updated Successfully!'
           ]
       );

    }
}

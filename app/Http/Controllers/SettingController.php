<?php
namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Models\Setting;
use Gate;
use Carbon\Carbon;
use Config;

class SettingController extends Controller
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
        if(Gate::denies('add')) {
               return abort('401');
        } 

        $setting = Setting::first();        

        return view('setting',compact('setting'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $id)
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
          if(Gate::denies('add')) {
               return abort('401');
        } 

        $data = $request->except('_token');

        $request->validate([
              'server_type' => 'required',
              'server_name' => 'required',
              'port'        => 'required',
              'mail_encryption' => 'required',
              'user_name'   => 'required',
              'password'    => 'required',
              'from_email'  => 'required'
        ]);

       $setting = Setting::first();

       if(!$setting){

        Setting::create($data);

       }else{
        $setting->update($data);
       }

       if($setting){

         $config = array(
            'host'       => $setting->server_name,
            'port'       => $setting->port,
            'from'       => array('address' => $setting->from_email, 'name' =>  env('MAIL_FROM_NAME', 'QPM CONSTRUCTION') ),
            'encryption' => $setting->mail_encryption,
            'username'   => $setting->user_name,
            'password'   => $setting->password
          );

          Config::set('mail', $config);

       }

        return redirect()->back()->with('message', 'Setting Updated Successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
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


}

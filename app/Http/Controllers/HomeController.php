<?php

namespace App\Http\Controllers;

use App\Models\Subcontractor;
use Illuminate\Http\Request;
use App\Models\DocumentType;
use App\Models\ProjectType;
use App\Models\FavouriteUrl;
use App\Models\Category;
use App\Models\Vendor;
use App\Models\Trade;
use App\Models\User;
use App\Models\Role;
use Auth;
use App\Http\Controllers\FileController;

class HomeController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $projectTypes = ProjectType::count();
        // $properties = Property::count();
        $users = User::count();
        $roles = Role::count();
        $documentTypes = DocumentType::count();
        $vendors = Vendor::count();
        $trades = Trade::count();
        $subcontractors = Subcontractor::count();
        $categories = Category::count();

        $files = \Storage::disk(FileController::DOC_UPLOAD)
                 ->allFiles(FileController::PROPERTY);

        $files = @count($files);

        return view('home',compact('projectTypes','documentTypes',
            'users','roles','files','vendors','trades','subcontractors',
            'categories'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function profile()
    {
        $user = Auth::user();

        return view('profile',compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $data = $request->all();

        $request->validate([
              'email' => 'required|email|unique:users,email,'.$user->id,
        ]);
        
         if($request->hasFile('profile_picture')){
               $profile_picture = $request->file('profile_picture');
               $photoName = $user->name.'-'.time() . '.' . $profile_picture->getClientOriginalExtension();
              
               $avatar  = $request->file('profile_picture')->storeAs('users', $photoName, 'public');

               $user->avatar = $avatar;
        }

        $user->email = $data['email'];
        $user->name = $data['name'];
        $user->save();

        return redirect()->back()->with('message', 'Profile Updated Successfully!');

    }

     public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
             'current_password' => ['required', function ($attribute, $value, $fail) use ($user) {
                    if (!\Hash::check($value, $user->password)) {
                        return $fail(__('The current password is incorrect.'));
                    }
                }],
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);
    
        $user->password = \Hash::make($request->password);
        $user->save();

        return redirect()->back()->with('message', 'Password Updated Successfully!');

    }


    public function setup(){

        return view('setup');
    }

    public function favourites(Request $request){

       $user = Auth::user();

       $status = 1;
      
       $favourites =  FavouriteUrl::where(function($q){
            $q->where('user_id', auth()->user()->id); 
            $q->where('status',1); 
        })->get();

      return view('favourites',compact('favourites'));

    }


    public function makeFavourite(Request $request){

      
       $user = Auth::user();

       $status = ($request->status == 'true' ) ? 1 : 0;
      
        FavouriteUrl::updateOrCreate(
            ['url' =>$request->url, 'user_id' => $user->id],
            ['url' =>$request->url, 'user_id' => $user->id,'status' => (int) $status]
        );

        return redirect($request->url)->with('message', 'Favourite URL '.( $status == 1 ? "Added" : "Removed").' Successfully!');

    }

     public function getFavourite(Request $request){

       $user = Auth::user();

       $url = $request->url;

       $favourites =  FavouriteUrl::where(function($q) use ($url){
            $q->where('user_id', auth()->user()->id); 
            $q->where('url',$url); 
        })->pluck('status')->first();

         return response()->json(
           [
            'status' => 200,
            'data' => $favourites
           ]
        );
        
    }


}

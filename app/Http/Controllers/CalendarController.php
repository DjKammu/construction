<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Models\Project;
use App\Models\Event;
use Gate;
use Carbon\Carbon;


class CalendarController extends Controller
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
    public function index(Request $request)
    {
         if(Gate::denies('view')) {
               return abort('401');
         } 
         
         if($request->ajax()){
            
            $projects = Project::query();
            $events = Event::query();

            if (!empty($request->input('start',null))) {
                $rangeStart = $request->input('start');
                $rangeEnd = $request->input('end');
            } else {
                $rangeStart = Carbon::now()->toDateString();
                $rangeEnd = Carbon::now()->toDateString();
            }
            

             $projects = $projects->WhereNotNull(['start_date','end_date'])
                                 ->where('start_date', ">=", $rangeStart)
                                 ->where('end_date', "<=", $rangeEnd)
                                 ->get();

            $events = $events->WhereNotNull(['start','end'])
                                 ->where('start', ">=", $rangeStart)
                                 ->where('end', "<=", $rangeEnd)
                                 ->with('project')
                                 ->get();

            $eventsKey = 0;

             foreach ($events as $key => $event) {
                  $events[$key]['color'] = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
                  $events[$key]['eventTitle'] = $event['title'];
                  $events[$key]['title'] = $event['title'].' - '.$event->project->name;

            }
           
            foreach ($projects as $key => $project) {

                $className = 'aasa';

                $events->push([
                    'start'                => $project->start_date->format('Y-m-d H:i:s'),
                    'end'                  => $project->start_date->format('Y-m-d H:i:s'),
                    'title'                => $project->name.' starts',
                    'allDay'               => false,
                    'color'                => sprintf('#%06X', mt_rand(0, 0xFFFFFF))
                ]);

                $events->push([
                    'start'                => $project->end_date->format('Y-m-d H:i:s'),
                    'end'                  => $project->end_date->format('Y-m-d H:i:s'),
                    'title'                => $project->name.' ends',
                    'allDay'               => false,
                    'color'                => sprintf('#%06X', mt_rand(0, 0xFFFFFF))
                ]);

            }

            return response()->json($events);

         }
         return view('calendar.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if(Gate::denies('view')) {
               return abort('401');
         } 
      
        $projects = Project::query();
        $events = Event::query();

        if (!empty($request->input('start',null))) {
            $rangeStart = $request->input('start');
            $rangeEnd = $request->input('end');
        } else {
            $rangeStart = Carbon::now()->toDateString();
            $rangeEnd = Carbon::now()->toDateString();
        }
        

         $projects = $projects->WhereNotNull(['start_date','end_date'])
                             ->where('start_date', ">=", $rangeStart)
                             ->where('end_date', "<=", $rangeEnd)
                             ->get();

        $events = $events->WhereNotNull(['start','end'])
                             ->where('start', ">=", $rangeStart)
                             ->where('end', "<=", $rangeEnd)
                             ->with('project')
                             ->get();

        $eventsKey = 0;

         foreach ($events as $key => $event) {

            $events[$key]['eventTitle'] = $event['title'];
            $events[$key]['title'] = $event['title'].' - '.$event->project->name;

        }
       
        foreach ($projects as $key => $project) {

            $className = '';

            $events->push([
                'start'                => $project->start_date->format('Y-m-d H:i:s'),
                'end'                  => $project->start_date->format('Y-m-d H:i:s'),
                'title'                => $project->name.' starts',
                'allDay'               => false,
                'className'            => $className
            ]);

            $events->push([
                'start'                => $project->end_date->format('Y-m-d H:i:s'),
                'end'                  => $project->end_date->format('Y-m-d H:i:s'),
                'title'                => $project->name.' ends',
                'allDay'               => false,
                'className'            => $className
            ]);

        }


        return response()->json($events);
        
    }


    public function getProjects(){

        $all = Project::all();
        
        return response()->json($all); 

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
      

        $data['start'] = Carbon::parse($data['start'])->toDateTimeString();
        $data['end']   = Carbon::parse($data['end'])->toDateTimeString();

        Event::UpdateOrCreate(
               ['id' => @$data['id']],
               $data);
        
        return response()->json(
           [
            'status' => 200,
            'message' => "Event ".( @$data['id']) ? 'Updated' : 'Created'." Successfully!!"
          ]
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
          if(Gate::denies('edit')) {
               return abort('401');
          } 

         $vendor = Vendor::find($id);
        
         return view('vendors.edit',compact('vendor'));
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
        if(Gate::denies('update')) {
               return abort('401');
        } 

       $data = $request->except('_token');

       $request->validate([
              'name' => 'required|unique:vendors,name,'.$id
       ]);

        $date['slug'] = $slug = \Str::slug($request->name);
         
        $vendor = Vendor::find($id);
       
        if(!$vendor){
            return redirect()->back();
        }

        $vendor->update($data);
 
        return redirect('vendors')->with('message','Vendor Updated Successfully!');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
         if(Gate::denies('delete')) {
               return abort('401');
          } 

         $event = Event::find($id);

         $event->delete();


           return response()->json(
           [
            'status' => 200,
            'message' => 'Event Deleted Successfully!!'
          ]
        );

    }
}

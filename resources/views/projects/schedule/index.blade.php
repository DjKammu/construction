@extends('layouts.admin-app')

@section('title', 'Edit Project')

@section('content')

<script src="{{ asset('js/scheduler.js') }}"></script>
<script src="{{ asset('js/scheduler_limit.js') }}"></script>
<link href="{{ asset('css/dhtmlxscheduler_material.css') }}"  rel="stylesheet" />
<div class="row">
@include('includes.back', 
['url' => route("projects.show", ['project' => request()->project]),
'to' => 'to Project'])

</div>

  <div class="card p-2">
    <div class="row">
        <div class="col-md-12">
              <!-- Start Main View -->
            @if(session()->has('message'))
                <div class="alert alert-success alert-dismissible fade show">
                  <strong>Success!</strong> {{ session()->get('message') }}
                  <button type="button" class="close" data-dismiss="alert">&times;</button>
              </div>
            @endif

             @if ($errors->any())
               <div class="alert alert-warning alert-dismissible fade show">
                 <button type="button" class="close" data-dismiss="alert">&times;</button>
                  <strong>Error!</strong>  
                   {{implode(',',$errors->all() )}}
                </div>
             @endif

              @if(session()->has('error'))
                <div class="alert alert-warning alert-dismissible fade show">
                  <strong>Error!</strong> {{ session()->get('error') }}
                  <button type="button" class="close" data-dismiss="alert">&times;</button>
              </div>
            @endif

            <div class="card-body">
               <div class="row">
                        <div class="col-md-12">
                               <div class="row">
                                <div class="col-6">
                                    <h4 class="mt-0 text-left">{{ @$project->name }} - Schedule </h4>
                                </div>
                              
                                 <!-- <div class="col-6 text-right">
                                    <button type="button" class="btn btn-danger mt-0"  onclick="sendEmailPopup()">
                                      Send Email
                                    </button>

                                    <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='budget/pdf/download'">Download
                                    </button> 

                                     <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='budget/excel/download'">Download to Excel
                                    </button>
                                </div> -->
                            </div>
                             
                             <!-- <div class="row">
                                <div class="col-8">
                                    <form method="post" action="{{ route('projects.gantt.other.assign', [ 'project' => request()->project]) }}"> 
                                      @csrf
                                     <select style="height: 26px;" onchange="return window.location.href = '?project_type='+this.value" name="project_type"> 
                                      <option value="">Select Project Type</option>
                                      @foreach($projectTypes as $type)
                                         <option value="{{ $type->slug }}" {{ (@request()->project_type == $type->slug) ? 'selected' : ''}}> {{ $type->name }}</option>
                                      @endforeach
                                      </select>
                                    <select style="height: 26px;"  name="project_id"> 
                                      <option value=""> Select Project</option>
                                      @foreach($projects as $p)
                                       <option value="{{ $p->id }}" >{{ $p->name}}
                                       </option>
                                      @endforeach
                                    </select>
                                    <button >Add Gantt from other projects</button>
                                  </form>
                                </div>
                                <div class="col-4">
                                  
                                </div>
                            </div> -->

<style>
    .event_work div,
    .dhx_cal_editor.event_work,
    .dhx_cal_event_line.event_work{
      background-color: #ff9633!important;
    }
    .dhx_cal_event_clear.event_work{
      color: #ff9633!important;
    }

    .event_meeting div,
    .dhx_cal_editor.event_meeting,
    .dhx_cal_event_line.event_meeting
    {
      background-color: #9575cd!important;
    }
    .dhx_cal_event_clear.event_meeting{
      color: #9575cd!important;
    }

    .event_movies div,
    .dhx_cal_editor.event_movies,
    .dhx_cal_event_line.event_movies{
      background-color: #ff5722!important;
    }
    .dhx_cal_event_clear.event_movies{
      color: #ff5722!important;
    }

    .event_rest div,
    .dhx_cal_editor.event_rest,
    .dhx_cal_event_line.event_rest{
      background-color: #0fc4a7!important;
    }
    .dhx_cal_event_clear.event_rest{
      color: #0fc4a7!important;
    }

    .add_event_button{
      position: absolute;
      width: 55px;
      height: 55px;
      background: #ff5722;
      border-radius: 50px;
      bottom: 40px;
      right: 55px;
      box-shadow: 0 2px 5px 0 rgba(0,0,0,0.3);
      z-index: 5;
      cursor:pointer;
    }
    .add_event_button:after{
      background: #000;
      border-radius: 2px;
      color: #FFF;
      content: attr(data-tooltip);
      margin: 16px 0 0 -137px;
      opacity: 0;
      padding: 4px 9px;
      position: absolute;
      font-family: "Roboto", Arial, sans-serif;
      font-size: 14px;
      visibility: hidden;
      transition: all .5s ease-in-out;
    }
    .add_event_button:hover{
      background: #ff774c;
    }
    .add_event_button:hover:after{
      opacity: 0.55;
      visibility: visible;
    }
    .add_event_button span:before{
      content:"";
      background: #fff;
      height: 16px;
      width: 2px;
      position: absolute;
      left: 26px;
      top: 20px;
    }
    .add_event_button span:after{
      content:"";
      height: 2px;
      width: 16px;
      background: #fff;
      position: absolute;
      left: 19px;
      top: 27px;
    }

    .dhx_cal_event div.dhx_event_resize.dhx_footer{
      background-color: transparent !important;
    }
 </style>


<script type="text/javascript" charset="utf-8">
  var initSkin = scheduler._skin_init;

  function init() {
    scheduler.config.xml_date = "%Y-%m-%d %H:%i";

    scheduler.config.first_hour = 00;
    scheduler.config.last_hour = 24;

    var targetSkin;
    scheduler.changeSkin = function changeSkin(skin){
      targetSkin = skin;
      var link = document.createElement("link");
      link.onload = function(){
        var children = document.getElementsByClassName("landing-view-demo")[0].getElementsByTagName('link');
        children = [].slice.call(children);
        if(children.length > 1){
          for (var i = 0; i < children.length-1; i++){
            if((children[i].href + "").indexOf('dhtmlxscheduler') > -1)
              document.getElementsByClassName("landing-view-demo")[0].removeChild(children[i]);
          }
        }

        scheduler.xy={
          min_event_height:40,
          scale_width:50,
          scroll_width:18,
          scale_height:20,
          month_scale_height:20,
          menu_width:25,
          margin_top:0,
          margin_left:0,
          editor_width:140,
          month_head_height:22
        };

        scheduler.skin = skin;
        scheduler._skin_init = initSkin;
        scheduler.init('scheduler_here', null, null);
        scheduler.skin = skin;
        setTimeout(function(){
          scheduler.setCurrentView();
          scheduler.resetLightbox();
        });
        if(skin == "material"){
          document.querySelector(".add_event_button").style.display = "";
        }else{
          document.querySelector(".add_event_button").style.display = "none";
        }

      }

      var skinFile = ["dhtmlxscheduler", skin ? ("_" + skin) : "", ".css"].join("");
      alert(skinFile);
      link.href = "/docs/products/dhtmlxScheduler/demo/lib/dhtmlxScheduler/" + skinFile;
      link.rel= "stylesheet";



      document.getElementsByClassName("landing-view-demo")[0].appendChild(link);

    }

    scheduler.config.details_on_create = true;
    // scheduler.config.now_date = new Date(2020, 3, 24, 14, 17);
    scheduler.config.now_date = new Date();

    scheduler.templates.event_class=function(start, end, event){
      var css = "";

      if( (scheduler.skin == "material" || targetSkin == "material") && event.evType) // if skin == "material" and event has type property then special class should be assigned
        css += "event_"+getLabel(evType, event.evType).toLowerCase();

      return css; // default return
    };

    function getLabel(array, key){
      for (var i = 0; i < array.length; i++) {
        if (key == array[i].key)
          return array[i].label;
      }
      return "";
    }

    var evType = [
      { key: '', label: 'Select event type' },
      { key: 1, label: 'Rest' },
      { key: 2, label: 'Meeting' },
      { key: 3, label: 'Movies' },
      { key: 4, label: 'Work' }
    ];

    scheduler.locale.labels.section_evType = "Event type";

    scheduler.config.lightbox.sections=[
      { name:"description", height:43, map_to:"text", type:"textarea" , focus:true },
      { name:"evType", height:20, type:"select", options: evType, map_to:"evType" },
      { name:"time", height:72, type:"time", map_to:"auto" }
    ];

    scheduler.attachEvent("onBeforeViewChange", function(old_mode,old_date,mode,date){
      if(!scheduler.skin){
        if(old_mode!=mode || old_date.getTime()!=date.getTime())
            scheduler.skin = targetSkin;
      }

        return true;
    });

    scheduler.attachEvent("onBeforeDrag", function (id, mode, e){
        if(!scheduler.skin)
          scheduler.skin = targetSkin;

        return true;
    });
    
    scheduler.config.date_format = "%Y-%m-%d %H:%i:%s";
 
    scheduler.setLoadMode("day"); 

    // scheduler.init("scheduler_here",new Date(2020,3,20),"week");
    scheduler.init("scheduler_here",new Date(),"week");

    scheduler.load("schedule/get/data");
      
      scheduler.createDataProcessor({
        url: "schedule/do",
        mode: "REST"
      });


    // scheduler.parse([
    //   { start_date: "2020-04-20 10:00", end_date: "2020-04-20 12:00", text:"Front-end meeting"},
    //   { start_date: "2020-04-21 16:30", end_date: "2020-04-21 18:30", text:"Feed ducks and city walking", evType:1},
    //   { start_date: "2020-04-22  8:00", end_date: "2020-04-22 11:00", text:"World Darts Championship (morning session)"},
    //   { start_date: "2020-04-22 12:00", end_date: "2020-04-22 14:00", text:"Lunch with Ann & Alex", evType:2},
    //   { start_date: "2020-04-23 16:00", end_date: "2020-04-23 17:30", text:"Game of Thrones", evType:3},
    //   { start_date: "2020-04-25  9:00", end_date: "2020-04-25 11:00", text:"Design workshop", evType:4},
    //   { start_date: "2020-04-25 14:00", end_date: "2020-04-25 17:00", text:"World Darts Championship (evening session)"},
    //   { start_date: "2020-04-23 00:00", end_date: "2020-04-23 00:00", text:"Couchsurfing. Family from Portugal"}
    // ], "json");


  }

  function addNewEv(){
    scheduler.addEventNow();
  }
</script>

<script type="text/javascript">
  window.onload = function(){
    init();
  }
</script>


                  <div style="position:relative; height: 600px; padding-top: 30px; overflow: hidden; background: #f7f7f7;">
                

                  <div id="scheduler_here" class="dhx_cal_container" style='width:100%; height:100%;'>
                    <div class="dhx_cal_navline">
                      <div class="dhx_cal_prev_button">&nbsp;</div>
                      <div class="dhx_cal_next_button">&nbsp;</div>
                      <div class="dhx_cal_today_button"></div>
                      <div class="dhx_cal_date"></div>
                      <div class="dhx_cal_tab" name="day_tab"></div>
                      <div class="dhx_cal_tab" name="week_tab"></div>
                      <div class="dhx_cal_tab" name="month_tab"></div>
                    </div>
                    <div class="dhx_cal_header">
                    </div>
                    <div class="dhx_cal_data">
                    </div>
                  </div>

                  <div class="add_event_button" onclick="addNewEv()" data-tooltip="Create new event"><span></span></div>
                </div>



            </div>
        </div>
    </div>
</div>


@endsection

@section('pagescript')
<script type="text/javascript">

  function sendEmailPopup(){   
      $("#myModal").modal('show');
  }

   function sendMail(){
   
    var recipient = $('#recipient').val();
    var subject = $('#subject').val();
    var message = $('#message').val();
    var file = $('#file').val();
     var cc = $('#cc').val();
    var bcc = $('#bcc').val();


    const validateEmail = (email) => {
    return String(email)
      .toLowerCase()
      .match(
        /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
      );
  };


    if(!recipient){
      alert('Recipient cant be blank')
      return
    }else if(!validateEmail(recipient)) {
        alert('Recipient is invalid')
      return
  
    }else if(!subject){
      alert('Subject cant be blank')
      return
    } else if(!message){
      alert('Message cant be blank')
      return
    }
    
    let projectId = '{{ @$project->id }}';

    let _token   =   "{{ csrf_token() }}";

    let url = '/projects/'+projectId+'/budget/send-mail'

   $.ajax({
        url: url,
        type:"POST",
        data:{
          recipient:recipient,
          subject:subject,
          message:message,
          file:file,
          cc:cc,
          bcc:bcc,
          _token: _token
        },
        success:function(response){
           alert(response.message); 
           $("#myModal").modal('hide');
           location.reload();
        },
        error: function(error) {
          alert(error);
        }
       });

   }


</script>
<style type="text/css">
  .form-switch {
    padding-left: 2.5em;
}

.form-switch .form-check-input:checked {
    background-position: right center;
    background-image: url(data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e);
}
.form-check-input:checked[type=checkbox] {
    background-image: url(data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23fff' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='M6 10l3 3l6-6'/%3e%3c/svg%3e);
}
.form-switch .form-check-input {
    width: 2em;
    margin-left: -2.5em;
    background-image: url(data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='rgba%280, 0, 0, 0.25%29'/%3e%3c/svg%3e);
    background-position: left center;
    border-radius: 2em;
    transition: background-position .15s ease-in-out;
}
.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}
.form-check-input[type=checkbox] {
    border-radius: 0.25em;
}
.form-check .form-check-input {
    float: left;
    margin-left: -1.5em;
}
.form-check-input {
    width: 1em;
    height: 1em;
    margin-top: 0.25em;
    vertical-align: top;
    background-color: #fff;
    background-repeat: no-repeat;
    background-position: center;
    background-size: contain;
    border: 1px solid rgba(0,0,0,.25);
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    -webkit-print-color-adjust: exact;
    color-adjust: exact;
}


#gantt_here{
  max-height: 500px;
}

span.cross{
    position: absolute;
    z-index: 10;
    right: 30px;
    display: none;
}
tr:hover span.cross{
  display: block;
}
button.btn.btn-neutral.bg-transparent.btn-icon{
  background-color: transparent !important;
}
#documents td{
  width: 100%;
}

span.doc-type{
 font-size: 12px;
 padding-top: 8px;
 display: block;
}

span.doc_type_m{
 font-size: 10px;
 padding-top: 3px;
 display: block;
}

.btn-group-sm .btn{
    padding: .25rem .5rem;
    font-size: .875rem;
    line-height: 1.5;
    border-radius: .2rem;
}
.avatar.proposal_file{
    width: 30px;
    height: 30px;
}


.list span {
    
    text-align: left;
    display: table-cell;
    vertical-align: middle;
    border-bottom: 1px solid #dee2e6;
    border-top: 1px solid #dee2e6;
    border-left: 1px solid #dee2e6;
    
}

.list li span:last-child {    
    border-right: 1px solid #dee2e6;   
}

.list p {
    font-size: 16px;
    padding: 12px 7px;
    vertical-align: middle;
    border-top: 1px solid #dee2e6;
    border-left: 1px solid #dee2e6;
    margin: 0 !important;
    
}

.list .h6 {
    margin-bottom: 0;  
}

.list li p:last-child {    
    border-right: 1px solid #dee2e6;   
}

.list {
    
    list-style: none;
    margin: 0;
    padding: 0;
    display: grid;
    white-space: nowrap;
    width: 100%;
    
}

.list li {  
    color: #5c5c5c;
}

.list li.multi-line{
 display: inline-table;
}

.list li.single-line{
 /*display: table-caption;*/
}

span.awarded-green, span.awarded-green a{
    /*background: #38ef38;*/
    /*color: #fffdfa;*/
    color: #038303;
    font-weight: 800;
    text-decoration: none;
}

.list li span.bid-text{
      font-size: 12px;
      padding: 4px 0px;
} 


table.payments-table{
      font-size: 12px;
      font-family: Arial;
}

table.payments-table thead>tr>th{
   font-size: 12px;
}
i.fa.fa-sort-desc {
    position: relative;
    left: -8px;
    cursor: pointer;
    top: 1px;
}
i.fa.fa-sort-asc{
  position: relative;
    left: 4px;
    cursor: pointer;
    top: -2px;
}
.sorting-outer{
  position: absolute;
}

.sorting-outer a{
  color: #ef8157 ;
}
.table-responsive.table-payments{
  overflow: auto;
}

  #category-types-table{
    font-size: 12px;
  }
  .checkbox{
    margin-right: 4px;
  }

    .budget-image{
    float: left;
    margin-top: 5px;
  }
  .budget-image .avatar.proposal_file{
    height: 15px;
    width: 15px;
  }
  
</style>

@endsection
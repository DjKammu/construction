@extends('layouts.admin-app')

@section('title', 'Categories')

@section('content')
      <!-- Start Main View -->
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

            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-6">
                        <h4 class="mt-0 text-left">Calendar</h4>
                    </div>
                </div>
                <!-- Categories Table -->
                <div class="table-responsive">

                      <div class="response"></div>
                      <div id='fullCalendar'></div>
                    

                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Create new event</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-12">
                    <label class="col-xs-4" for="title">Event title</label>
                    <input type="text" class="form-control" name="title" id="title" required="" />
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <label class="col-xs-4" for="starts-at">Project</label>
                    <select class="form-control" name="project_id" id="projects">
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <label class="col-xs-4" for="starts-at">Starts at</label>
                    <input type="text" class="form-control" name="starts_at" id="starts-at" />
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <label class="col-xs-4" for="ends-at">Ends at</label>
                    <input type="text" class="form-control" name="ends_at" id="ends-at" />
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="save-event">Save changes</button>
            <button type="button" class="btn btn-primary" id="update-event">Update changes</button>
            <button type="button" class="btn btn-danger" event-id="0" id="delete-event">Delete</button>
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->


@endsection

@section('pagescript')

<link rel="stylesheet" href="fullcalendar/fullcalendar.min.css" />
<script src="fullcalendar/lib/moment.min.js"></script>
<script src="fullcalendar/fullcalendar.min.js"></script>

<script>

$(document).ready(function () {

    var intervalStart, intervalEnd; //variables with higher scope level

    var calendar = $('#fullCalendar').fullCalendar({
        droppable: true,
        editable: true,
        events: "{{ route('calendar.index')}}",
        displayEventTime: false,
        eventRender: function (event, element, view) {
            if (event.allDay === 'true') {
                event.allDay = true;
            } else {
                event.allDay = false;
            }
        },
        selectable: true,
        selectHelper: true,
        select: function (start, end, allDay) {
              // Display the modal.
              // You could fill in the start and end fields based on the parameters
               modalOpen();
        },
        
        editable: true,
      
        eventClick: function (event) {
            modalOpen(event);
        },
        viewRender: function (view, element)
        {
            intervalStart = view.intervalStart;
            intervalEnd = view.intervalEnd;

        }
    });
        
         $("#starts-at, #ends-at").datetimepicker();

    
        // Whenever the user clicks on the "save" button om the dialog
        $('#save-event').on('click', function() {
            var title = $('#title').val();
           
            if (title) {

                var eventData = {
                    title: title,
                    project_id: $('#projects').val(),
                    start: $('#starts-at').val(),
                    end: $('#ends-at').val(),
                    _token: '{{ csrf_token() }}'
                };
             
             $.ajax
                ({
                    type: "POST",
                    url: "{{route('calendar.store')}}", 
                    data: eventData
                }).done( function(data){
                     displayMessage("Added Successfully");
                    //console.log(data)
                }).fail(function(){
                    console.log('Ajax Failed')
                });

               // $('#fullCalendar').fullCalendar('renderEvent', eventData, false); // stick? = true
            }
            
            // $('#fullCalendar').fullCalendar('unselect');

            //Clear modal inputs
            $('.modal').find('input').val('');

            // hide modal
            $('.modal').modal('hide');

             $('#fullCalendar').fullCalendar('refetchEvents');
        });

        $('#update-event').on('click', function() {
            var title = $('#title').val();
           
            if (title) {

                var eventData = {
                    id:  $('#delete-event').attr('event-id'),
                    title: title,
                    project_id: $('#projects').val(),
                    start: $('#starts-at').val(),
                    end: $('#ends-at').val(),
                    _token: '{{ csrf_token() }}'
                };
             
             $.ajax
                ({
                    type: "POST",
                    url: "{{route('calendar.store')}}", 
                    data: eventData
                }).done( function(data){
                     displayMessage("Updated Successfully");
                    //console.log(data)
                }).fail(function(){
                    console.log('Ajax Failed')
                });

               // $('#fullCalendar').fullCalendar('renderEvent', eventData, false); // stick? = true
            }
            
            // $('#fullCalendar').fullCalendar('unselect');

            //Clear modal inputs
            $('.modal').find('input').val('');

            // hide modal
            $('.modal').modal('hide');

             $('#fullCalendar').fullCalendar('refetchEvents');
        });

        $('#delete-event').on('click',function(){
          $('.modal').modal('hide');
          var event_id = $('#delete-event').attr('event-id'); 
          var deleteMsg = confirm("Do you really want to delete?");
            if (deleteMsg) {
                let Url = '{{ url("calendar") }}';
                $.ajax({
                    type: "DELETE",
                    url: Url +'/'+event_id,
                    data: {                       
                        id: event.id,
                        _token : '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if(response.status ==  200) {
                            $('#fullCalendar').fullCalendar('removeEvents', event.id);
                            displayMessage("Deleted Successfully");
                        }
                    }
                });
            }

 
        });
});

function displayMessage(message) {
      $(".response").html("<div class='success'>"+message+"</div>");
    setInterval(function() { $(".success").fadeOut(); }, 1000);
}

function modalOpen($event){
  
      $.ajax({
            url: "{{ route('calendar.projects')}}",
            type: "GET",
            success: function (response) {
                var html = '<option value="">Select Project</option>';
                for (let i = 0; i < response.length; i++) {
                  html += '<option value="'+response[i].id+'">'+response[i].name+'</option>';
                }
                $('#projects').html(html);

                if($event){
                  $('#projects').val($event.project_id);  
                } 
            }
        });

       if($event){
         $('.modal-title').html('Edit event');
         $('#save-event').hide();
         $('#update-event').show();    
         $('#delete-event').attr('event-id',$event.id);
         $('#delete-event').show();

         $('#title').val($event.eventTitle);
         
         $('#starts-at').val(moment($event.start).format('MM/DD/YYYY h:m A'));
         $('#ends-at').val(moment($event.end).format('MM/DD/YYYY h:m A'));

       }else{
         $('.modal-title').html('Create new event');
         $('#save-event').show();
         $('#update-event').hide();
         $('#delete-event').hide();
       }

        $('.modal').modal('show');

        

}
</script>

<style>

#calendar {
    width: 700px;
    margin: 0 auto;
}

.response {
    height: 60px;
}

.success {
    background: #cdf3cd;
    padding: 10px 60px;
    border: #c3e6c3 1px solid;
    display: inline-block;
}

#fullCalendar {
        max-width: 90%;
        margin: 0 auto;
    }

  
    .modal-dialog-slideout {
        min-height: 100%;
        margin: 0 0 0 auto;
        background: #fff;
    }

    .modal.fade .modal-dialog.modal-dialog-slideout {
        -webkit-transform: translate(100%, 0)scale(1);
        transform: translate(100%, 0)scale(1);
    }

    .modal.fade.show .modal-dialog.modal-dialog-slideout {
        -webkit-transform: translate(0, 0);
        transform: translate(0, 0);
        display: flex;
        align-items: stretch;
        -webkit-box-align: stretch;
        height: 100%;
    }

    .modal.fade.show .modal-dialog.modal-dialog-slideout .modal-body {
        overflow-y: auto;
        overflow-x: hidden;
    }

    .modal-dialog-slideout .modal-content {
        border: 0;
    }

    .modal-dialog-slideout .modal-header,
    .modal-dialog-slideout .modal-footer {
        height: 69px;
        display: block;
    }

    .modal-dialog-slideout .modal-header h5 {
        float: left;
    }
    .modal-dialog-slideout .modal-header {
        background-color: #ed0000;
        color: beige;
    }

    .bookin-status-toolbar {
        background-color: whitesmoke;
        margin-top: 20px;
        padding: 16px;
    }

    .bootstrap-datetimepicker-widget table td.day>div {
        z-index: 1 !important;
    }
    .fc-content{
        color: #fff;
        padding: 2px;
        font-size: 13px;
    }

    .fc-day-grid-event .fc-title{
        font-family: Arial;
        float: right;
        font-size: 11px;
        padding: 3px;
        color: #fff;
    }
    .fc-header.fc-widget-header .fc-title{
     background: #eee;
    }
    .fc-event.event-blue{
        background-color: #4b4be8;
    }
    .fc-event.event-grey{
        background-color: #a59b9b;
    }
    .fc-event.event-red{
        background-color: #ff5346;
    }
    .fc-event.event-orange {
    background-color: #fba931;
    }
    .fc-event.event-green {
    background-color: #5fd064;
    }
    .fc-event.event-azure {
    background-color: #56ddef;
    }
    .fc-title{
        background:black;
        font-weight:bold;
        padding:5px;
        margin:2px;
        display:block;  
        border-radius: 3px;
    }
    .fc-time{
        line-height: 30px;
    }

    a.fc-event{
         border: 0px;
    }
    .fc-time-grid-event .fc-title{
      float: left;
      padding: 5px;
    }
    .fc-time-grid-event .fc-time{
      float: left;
      width: 100%;
      line-height: 20px;
    }
    .count-booking{
      font-weight: bold;
    }
    label{
      cursor: pointer;
    }
    .card-header.card-header-primary{
       z-index: 0 !important;
    }

    .fc-button .fc-icon{
         height: 7em;
             font-size: 1em;
    }

    button.fc-today-button.fc-button.fc-state-default {
        background-color: #403D39 !important;
        background-image: none;
    }

    button.fc-today-button.fc-button.fc-state-default.fc-state-disabled{
        color: #f1e6e6;
    }

    .fc-button {
        display: inline-block;
        font-weight: 400;
        color: rgb(33, 37, 41);
        text-align: center;
        vertical-align: middle;
        user-select: none;
        background-color: transparent;
        font-size: 1em;
        line-height: 1.5;
        border-width: 1px;
        border-style: solid;
        border-color: transparent;
        border-image: initial;
        padding: 0.4em 0.65em;
        border-radius: 0.25em;
            text-transform: none;
        font-family: inherit;
        font-size: inherit;
        line-height: inherit;
        border-radius: 0px;
        overflow: visible;
        margin: 0px;
    }

    .fc-button-group > .fc-button {
        position: relative;
        -webkit-box-flex: 1;
        flex: 1 1 auto;
        margin: 0 0 0 2px;
    }

    .fc-button:not(:disabled) {
        cursor: pointer;
    }

    .fc-state-default {
        color: rgb(255, 255, 255);
        background-color: rgb(44, 62, 80);
        border-color: rgb(44, 62, 80);
    }

    

    
</style>
@endsection
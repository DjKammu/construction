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
        header: {
            left: "BackwardButton, ForwardButton",
            center: "title"
        },
        editable: true,
        events: "{{ route('calendar.create')}}",
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

              $.ajax({
                    url: "{{ route('calendar.projects')}}",
                    type: "GET",
                    success: function (response) {
                        var html = '<option value="">Select Project</option>';
                        for (let i = 0; i < response.length; i++) {
                          html += '<option value="'+response[i].id+'">'+response[i].name+'</option>';
                        }
                        $('#projects').html(html);
                    }
                });

                $('.modal').modal('show');
        },
        
        editable: true,
        eventDrop: function (event, delta) {
                    var start = $.fullCalendar.formatDate(event.start, "Y-MM-DD HH:mm:ss");
                    var end = $.fullCalendar.formatDate(event.end, "Y-MM-DD HH:mm:ss");
                    $.ajax({
                        url: 'edit-event.php',
                        data: 'title=' + event.title + '&start=' + start + '&end=' + end + '&id=' + event.id,
                        type: "POST",
                        success: function (response) {
                            displayMessage("Updated Successfully");
                        }
                    });
                },
        eventClick: function (event) {
            var deleteMsg = confirm("Do you really want to delete?");
            if (deleteMsg) {
                let Url = '{{ url("calendar") }}';
                $.ajax({
                    type: "DELETE",
                    url: Url +'/'+event.id,
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
        },
        viewRender: function (view, element)
        {
            intervalStart = view.intervalStart;
            intervalEnd = view.intervalEnd;

        }
    });
        
         $("#starts-at, #ends-at").datetimepicker( );

    
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
});

function displayMessage(message) {
      $(".response").html("<div class='success'>"+message+"</div>");
    setInterval(function() { $(".success").fadeOut(); }, 1000);
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

    #loading {
        display: none;
        position: absolute;
        top: 10px;
        right: 10px;
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

    .badge {
        padding: 5px 12px;
        text-transform: uppercase;
        font-size: 10px;
        color: #fff;
        display: inline-block;
        white-space: normal;
    }
    .badge-pill {
        padding-right: .6em;
        padding-left: .6em;
        border-radius: 10rem;
    }
    .badge.badge-default {
        background-color: #999;
    }
    .bootstrap-datetimepicker-widget table td.day>div {
        z-index: 1 !important;
    }
    .fc-content{
        color: #fff;
        padding: 2px;
        font-size: 13px;
    }
    .event-green .fc-event-dot{
          background-color: green;
    }.event-orange .fc-event-dot{
          background-color: #ff9800;
    }.event-azure .fc-event-dot{
          background-color: #69e6e6;
    }.event-blue .fc-event-dot{
          background-color: blue;
    }.event-red .fc-event-dot{
          background-color: red;
    }.event-grey .fc-event-dot{
          background-color: grey;
    }
    .fc-day-grid-event .fc-title{
         float:right;
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
        color: #f1e6e6;
        background-color: #403D39 !important;
        background-image: linear-gradient(to bottom,#fff,#403D39);
    }
    button.fc-today-button.fc-button.fc-state-default.fc-corner-left.fc-corner-right{

    }

    button.fc-today-button.fc-button.fc-state-default.fc-corner-left.fc-corner-right.fc-state-disabled{

    }
</style>
@endsection
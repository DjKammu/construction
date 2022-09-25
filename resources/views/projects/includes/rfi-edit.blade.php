@extends('layouts.admin-app')

@section('title', 'Payment')

@section('content')

@include('includes.back')

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
                        <h4 class="mt-0 text-left"> {{ @$project->name }} -  Edit RFI </h4>
                    </div>
                </div>

               <div class="row">
                        <div class="col-md-12">
                            <div class="card-body">
                                <form   method="post" 
                              action="{{ route('projects.rfi.update',['id' => (@$rfi->id) ? @$rfi->id : 0 ]) }}"
                               enctype="multipart/form-data">
                                  @csrf
                                     <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Number 
                                                </label>
                                                 <input value="{{ @$rfi->number }}"class="form-control" readonly="">
                                            </div>
                                        </div>
                                    </div>  

                                     <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Name 
                                                </label>
                                                 <input  name="name" value="{{ @$rfi->name }}" class="form-control">
                                            </div>
                                        </div>
                                    </div>  
                                    
                                     <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Created By
                                                </label>
                                                <select class="form-control" name="user_id"> 
                                                  <option value="">Select User</option>
                                                  @foreach($users as $user)
                                                     <option value="{{ $user->id }}" {{ (@$rfi->user_id == $user->id) ? 'selected' : ''}}> {{ $user->name }}</option>
                                                  @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                      <div class="row">
                                     <div class="col-lg-5 col-md-6 mx-auto">
                                        <div class="form-group">
                                            <label class="text-dark" for="password">Date Sent
                                            </label>
                                            <input  name="date_sent" value="{{ @$rfi->date_sent }}" type="text" class="form-control date" placeholder="Date ">
                                        </div>
                                     </div>
                                    </div>
                                   
                                   <div class="row">
                                     <div class="col-lg-5 col-md-6 mx-auto">
                                        <div class="form-group">
                                            <label class="text-dark" for="password">Date Recieved
                                            </label>
                                            <input  name="date_recieved" value="{{ @$rfi->date_recieved }}" type="text" class="form-control date" placeholder="Date Recieved">
                                        </div>
                                     </div>
                                    </div>

                                      <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Assign To
                                                </label>
                                                <select class="form-control" name="assign_to_id"> 
                                                  <option value="">Select Assignee</option>
                                                  @foreach($assignees as $assignee)
                                                     <option value="{{ $assignee->id }}" {{ ( @$rfi->assign_to_id == $assignee->id) ? 'selected' : ''}}> {{ $assignee->name }}</option>
                                                  @endforeach
                                                </select>
                                            </div>
                                        </div>
                                       </div>
                                        

                                     <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Subject 
                                                </label>
                                                 <input  name="subject" value="{{ @$rfi->subject }}" class="form-control">
                                            </div>
                                        </div>
                                    </div>  


                                      <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Subcontractor
                                                </label>
                                                <select class="form-control" name="subcontractor_id"> 
                                                  <option value="">Select Subcontractor</option>
                                                  @foreach($subcontractors as $subcontractor)
                                                     <option value="{{ $subcontractor->id }}" {{ (@$rfi->subcontractor_id == $subcontractor->id) ? 'selected' : ''}}> {{ $subcontractor->name }}</option>
                                                  @endforeach
                                                </select>
                                            </div>
                                        </div>
                                       </div>
                                        
      
                                   
                                   <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Sent File
                                                </label>
                                                <input  name="sent_file"  type="file" >
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Recieved File
                                                </label>
                                                <input  name="recieved_file"  type="file" >
                                            </div>
                                        </div>
                                    </div>

                                     <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Ball In Court
                                                </label>
                                                <select class="form-control" name="ball_in_court_id"> 
                                                  <option value="">Select Ball In Court</option>
                                                  @foreach($ballInCourts as $ballInCourt)
                                                   <option value="{{ $ballInCourt->id }}" {{ (@$rfi->ball_in_court_id == $ballInCourt->id) ? 'selected' : ''}}> {{ $ballInCourt->name }}</option>
                                                @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                   
                                   <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Status
                                                </label>
                                                <select class="form-control" name="status_id"> 
                                                  <option value="">Select Status</option>
                                                  @foreach($statuses as $status)
                                                   <option value="{{ $status->id }}" {{ ( @$rfi->status_id  == $status->id) ? 'selected' : ''}}> {{ $status->name }}</option>
                                                @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                         <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Notes
                                                </label>
                                                <textarea  name="notes"  type="text" class="form-control" placeholder="Notes" >
                                                 {{ @$rfi->notes }}</textarea>
                                            </div>
                                        </div>
                                    </div> 

                                    

                                    <!-- Submit Button -->
                                    <div class="col-12 text-center">
                                        <button id="change-password-button" type="submit" class="btn btn-danger">Update RIF
                                        </button>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pagescript')

<script type="text/javascript">

$( document ).ready(function() {
    $('.date').datetimepicker({
      format: 'M-D-Y'
  });

$("input[name='type']").click(function() {
      var id = $(this).val();
      $(".subcontractor-vendor").hide();
      $("#" + id).show();
  });



 $('select[name="vendor_id"]').change(function(){
      let vendorId = $(this).val();
      $.ajax({
            url: "{{ route('vendor.materials')}}"+'?vendor_id='+vendorId,
            type: "GET",
            success: function (response) {
                var html = '<option value="">Select Material</option>';
                for (let i = 0; i < response.length; i++) {
                  html += '<option value="'+response[i].id+'">'+response[i].name+'</option>';
                }
                $('#materials').html(html);
            }
        });
    
 });

});

</script>
@endsection

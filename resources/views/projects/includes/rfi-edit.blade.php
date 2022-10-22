@extends('layouts.admin-app')

@section('title', 'RFI')

@section('content')

@include('includes.back', 
['url' => route("projects.show", ['project' => @$rfi->project_id]),
'to' => 'to Project'])


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
                  
                     <div class="col-6 text-right">
                        <button type="button" class="btn btn-danger mt-0"  onclick="sendEmailPopup()">
                          Send Email
                        </button>
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
                                                 <input name="number" value="{{ @$rfi->number }}"class="form-control" required="">
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
                                        <button id="change-password-button" type="submit" class="btn btn-danger">Update RFI
                                        </button>
                                    </div>

                                </form>
                            </div>

                            <div class="table-responsive">           
                                <table id="subcontractors-table" class="table card-table dataTable no-footer" role="grid" aria-describedby="subcontractors-table_info">
                                 <thead class="d-none">
                                    <tr role="row">
                                       <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 0px;"></th>
                                    </tr>
                                 </thead>
                                 <tbody class="row">
                                
                                  @if($rfi->sent_file)
                                   @php
                                     $fileInfo = pathinfo($rfi->sent_file);
                                     $extension = @$fileInfo['extension'];
                                    
                                  if(in_array($extension,['doc','docx','docm','dot',
                                  'dotm','dotx'])){
                                      $extension = 'word'; 
                                   }
                                   else if(in_array($extension,['csv','dbf','dif','xla',
                                  'xls','xlsb','xlsm','xlsx','xlt','xltm','xltx'])){
                                      $extension = 'excel'; 
                                   }

                                   @endphp

                                    <tr class="text-center col-lg-2 col-sm-3 odd" style="display: flex; flex-wrap: wrap;" role="row">
                                       <td>
                                            <span class="cross"> 
                                             <form 
                                                method="post" 
                                                action="{{route('projects.rfi.file.destroy', $rfi->id)}}?path={{$rfi->sent_file}}"> 
                                                 @csrf
                                                {{ method_field('DELETE') }}

                                                <button 
                                                  type="submit"
                                                  onclick="return confirm('Are you sure?')"
                                                  class="btn btn-neutral bg-transparent btn-icon" data-original-title="Delete File" title="Delete File"><i class="fa fa-trash text-danger"></i> </button>
                                              </form>
                                            </span>
                                             <div class="card card-table-item" 
                                             style="width: 100%;">
                                                <div class="card-body pb-0">
                                                   <div class="author mt-1">
                                                    <!-- <span class="doc_type_m">
                                                      {{ @$proposal->subcontractor->name }} 
                                                    </span></br> -->
                                                    <a href="{{ asset($rfi->sent_file) }}" target="_blank">
                                                      <p> {{ @$file->name }} </p>
                                                      <img class="avatar border-gray" src="{{ asset('img/'.@$extension.'.png') }}">
                                                      </a> 
                                                      <!--  <span class="doc-type"> 
                                                      {{  @$file->document->document_type->name }}</span>  -->             
                                                   </div>
                                                </div>
                                             </div>
                                       </td>
                                    </tr>

                                    @endif

                                  @if($rfi->recieved_file)
                                   @php
                                     $fileInfo = pathinfo($rfi->recieved_file);
                                     $extension = @$fileInfo['extension'];
                                    
                                  if(in_array($extension,['doc','docx','docm','dot',
                                  'dotm','dotx'])){
                                      $extension = 'word'; 
                                   }
                                   else if(in_array($extension,['csv','dbf','dif','xla',
                                  'xls','xlsb','xlsm','xlsx','xlt','xltm','xltx'])){
                                      $extension = 'excel'; 
                                   }

                                   @endphp

                                    <tr class="text-center col-lg-2 col-sm-3 odd" style="display: flex; flex-wrap: wrap;" role="row">
                                       <td>
                                            <span class="cross"> 
                                             <form 
                                                method="post" 
                                                action="{{route('projects.rfi.file.destroy', $rfi->id)}}?path={{$rfi->recieved_file}}"> 
                                                 @csrf
                                                {{ method_field('DELETE') }}

                                                <button 
                                                  type="submit"
                                                  onclick="return confirm('Are you sure?')"
                                                  class="btn btn-neutral bg-transparent btn-icon" data-original-title="Delete File" title="Delete File"><i class="fa fa-trash text-danger"></i> </button>
                                              </form>
                                            </span>
                                             <div class="card card-table-item" 
                                             style="width: 100%;">
                                                <div class="card-body pb-0">
                                                   <div class="author mt-1">
                                                    <!-- <span class="doc_type_m">
                                                      {{ @$proposal->subcontractor->name }} 
                                                    </span></br> -->
                                                    <a href="{{ asset($rfi->recieved_file) }}" target="_blank">
                                                      <p> {{ @$file->name }} </p>
                                                      <img class="avatar border-gray" src="{{ asset('img/'.@$extension.'.png') }}">
                                                      </a> 
                                                      <!--  <span class="doc-type"> 
                                                      {{  @$file->document->document_type->name }}</span>  -->             
                                                   </div>
                                                </div>
                                             </div>
                                       </td>
                                    </tr>

                                    @endif

                                 </tbody>
                              </table>
                                </div>

                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>

 <div id="myModal" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
    <div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Send Mail</h3>
    </div>
    <div class="modal-body">
     
          <div class="form-group">
            <label for="recipient-name" class="col-form-label">Recipient:</label>
            <input type="email" class="form-control" id="recipient">
          </div>

          <div class="form-group">
            <label for="recipient-name" class="col-form-label">CC: <small>email with comma seperated
            </small></label>
            <input type="text" class="form-control" id="cc">
          </div>

          <div class="form-group">
            <label for="recipient-name" class="col-form-label">BCC: <small>email with comma seperated
            </small></label>
            <input type="text" class="form-control" id="bcc">
          </div>

          <div class="form-group">
            <label for="recipient-name" class="col-form-label">Subject:</label>
            <input type="text" class="form-control" id="subject">
          </div>
          <div class="form-group">
            <label for="message-text" class="col-form-label">Message:</label>
            <textarea class="form-control" id="message"></textarea>
          </div>
           <div class="form-group">
            <label for="message-text" class="col-form-label">Files:</label>
            <input type="file" id="files" name="files" multiple/>
          </div>
          
    
    </div>
    <div class="modal-footer">
        <button class="btn btn-close" data-dismiss="modal" aria-hidden="true">Close</button>
        <button class="btn btn-primary" onclick="sendMail()">Send</button>
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


   function sendEmailPopup(){   
      $("#myModal").modal('show');
   }

   function sendMail(){
   
    var recipient = $('#recipient').val();
    var subject = $('#subject').val();
    var message = $('#message').val();
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
    
    let rfiId = '{{ @$rfi->id }}';

    let _token   =   "{{ csrf_token() }}";

    let url =  rfiId+'/send-mail'

    var formData = new FormData();

    formData.append('recipient', recipient);
    formData.append('subject', subject);
    formData.append('message', message);
    formData.append('cc', cc);
    formData.append('bcc', bcc);
    formData.append('_token', _token);

    var files = $("#files").prop("files");

    for(var i = 0; i < files.length; i++) {
         formData.append('files[]', files[i]);
    }
    
   $.ajax({
        url: url,
        type:"POST",
        processData: false, // important
        contentType: false, // important
        data: formData,

        // data:{
        //   recipient:recipient,
        //   subject:subject,
        //   message:message,
        //   cc:cc,
        //   bcc:bcc,
        //   file:file,
        //   files:formData,
        //   _token: _token
        // },
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
@endsection

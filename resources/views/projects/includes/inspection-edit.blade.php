@extends('layouts.admin-app')

@section('title', 'Inspection')

@section('content')

@include('includes.back', 
['url' => route("projects.show", ['project' => $inspection->project_id]),
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
                        <h4 class="mt-0 text-left"> {{ @$project->name }} -  Edit Inspection </h4>
                    </div>
                  
                </div>

               <div class="row">
                        <div class="col-md-12">
                            <div class="card-body">
                                <form   method="post" 
                              action="{{ route('projects.inspection.update',['id' => (@$inspection->id) ? @$inspection->id : 0 ]) }}"
                               enctype="multipart/form-data">
                                  @csrf

                                        <div class="row">
                                         <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="Date">Date
                                                </label>
                                                <input  name="date" value="{{ @$inspection->date }}" type="text" class="form-control date" placeholder="Date ">
                                            </div>
                                         </div>
                                        </div>
                                        <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Inspection Category
                                                </label>
                                                <select class="form-control" name="inspection_category_id"> 
                                                  <option value="">Select Inspection Category</option>
                                                  @foreach($inspectionCategories as $category)
                                                     <option value="{{ $category->id }}" {{ (@$inspection->inspection_category_id == $category->id) ? 'selected' : ''}}> {{ $category->name }}</option>
                                                  @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        </div>

                                        <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Inspection Type
                                                </label>
                                                <select class="form-control" name="inspection_type_id"> 
                                                  <option value="">Select Inspection Type</option>
                                                  @foreach($inspectionTypes as $inspectionType)
                                                     <option value="{{ $inspectionType->id }}" {{ (@$inspection->inspection_type_id == $inspectionType->id) ? 'selected' : ''}}> {{ $inspectionType->name }}</option>
                                                  @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        </div>


                                        <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Files
                                                </label>
                                                <input  name="files[]"  type="file" multiple="">
                                            </div>
                                        </div>
                                        </div>

                                        <div class="row">
                                         <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Notes
                                                </label>
                                                <textarea  name="notes"  type="text" class="form-control" placeholder="Notes" >
                                                 {{ @$inspection->notes }}</textarea>
                                            </div>
                                        </div>
                                        </div> 

                                        <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <input type="checkbox"
                                                 name="passed" {{ $inspection->passed  ? 'checked' : ''}} value="1">
                                                <label class="text-dark" for="Passed/Failed">
                                                  <b>Passed/Failed</b>
                                                </label>
                                            </div>
                                        </div>
                                        </div>
                                
                                    <!-- Submit Button -->
                                    <div class="col-12 text-center">
                                        <button id="change-password-button" type="submit" class="btn btn-danger">Update Inspection
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
                                
                               
                                  @if($inspection->files)
                                  @foreach(@explode(',',$inspection->files) as $file)

                                   @php
                                     $fileInfo = pathinfo($file);
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
                                                action="{{route('projects.inspection.file.destroy', $inspection->id)}}?path={{$file}}"> 
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
                                                    <a href="{{ asset($file) }}" target="_blank">
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
                                     @endforeach
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
    
    let submittalId = '{{ @$submittal->id }}';

    let _token   =   "{{ csrf_token() }}";

    let url =  submittalId+'/send-mail'

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

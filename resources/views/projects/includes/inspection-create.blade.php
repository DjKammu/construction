@extends('layouts.admin-app')

@section('title', 'Inspection')

@section('content')

@include('includes.back', 
['url' => route("projects.show", ['project' => request()->id]),
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
                        <h4 class="mt-0 text-left"> {{ @$project->name }} -  Add Inspection </h4>
                    </div>
                </div>

               <div class="row">
                        <div class="col-md-12">
                            <div class="card-body">
                            <form   method="post" 
                              action="{{ route('projects.inspection',['id' => (@$project->id) ? @$project->id : 0 ]) }}"
                               enctype="multipart/form-data">
                                  @csrf
                                      <div class="row">
                                         <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Date
                                                </label>
                                                <input  name="date" value="{{ old('date')}}" type="text" class="form-control date" placeholder="Date ">
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
                                                     <option value="{{ $category->id }}" {{ (@request()->st == $category->id) ? 'selected' : ''}}> {{ $category->name }}</option>
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
                                                     <option value="{{ $inspectionType->id }}" {{ (@request()->st == $inspectionType->id) ? 'selected' : ''}}> {{ $inspectionType->name }}</option>
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
                                                 {{ old('notes')}}</textarea>
                                            </div>
                                        </div>
                                    </div> 

                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <input type="checkbox"
                                                 name="passed" value="1">
                                                <label class="text-dark" for="Passed/Failed">
                                                  <b>Passed/Failed</b>
                                                </label>
                                            </div>
                                        </div>


                                    </div>

                                    <!-- Submit Button -->
                                    <div class="col-12 text-center">
                                        <button id="change-password-button" type="submit" class="btn btn-danger">Create Inspection
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

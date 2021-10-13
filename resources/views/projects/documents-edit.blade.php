@extends('layouts.admin-app')

@section('title', 'Document')

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
                        <h4 class="mt-0 text-left">Edit Document</h4>
                    </div>
                </div>

               <div class="row">
                        <div class="col-md-12">
                            <div class="card-body">
                                <form   method="post" 
                              action="{{ route('documents.update',[ 'document' => request()->document ]) }}"
                               enctype="multipart/form-data">
                                <input type="hidden" name="_method" value="PUT">
                                  @csrf

                                    <!-- Current Password -->
                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Name 
                                                </label>
                                                <input  name="name" value="{{ @$document->name }}" type="text" class="form-control" placeholder="Document Name" required="">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Document Type
                                                </label>
                                                <select class="form-control" name="document_type_id"> 
                                                  <option value=""> Select Document Type</option>
                                                  @foreach($documentsTypes as $type)
                                                   <option value="{{ $type->id }}" {{ 
                                                      (@$document->document_type_id == $type->id) ? 'selected=""' : ''}} >{{ $type->name}}
                                                   </option>
                                                  @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div> 
                                    
                                     <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">
                                                  Tenant
                                                </label>
                                                <select class="form-control" id="year" 
                                                name="tenant_id"> 
                                                  <option value=""> Select Tenant</option>
                                                    @foreach($tenants as $tenant)
                                                     <option value="{{ $tenant->id }}" {{ 
                                                     ($document->tenant_id == $tenant->id ) ? 'selected' : ''
                                                     }}> {{ $tenant->name }}</option>
                                                  @endforeach

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">
                                                  File Name
                                                </label>
                                                <input  name="dname[]" class="form-control"  
                                                type="text" >
                                            </div>
                                        </div>
                                    </div>

                                   

                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Year
                                                </label>
                                                <select class="form-control" id="year" 
                                                name="year"> 
                                                  <option value=""> Select Year</option>

                                                  @for($i = date('Y'); $i >= date('Y') - 50; $i--)
                                                    <option value="{{ $i }}" 
                                                    {{ ($i == date('Y') ? 'selected' : '')}}
                                                    >{{ $i }}
                                                   </option>
                                                  @endfor

                                                </select>
                                            </div>

                                             <div class="form-group">
                                                <label class="text-dark" for="password">Month
                                                </label>
                                                <select class="form-control" id="month" name="month"> 
                                                  <option value=""> Select Month</option>
                                                  @for ($i=1; $i<=12; $i++)
                                                    <option value="{{  $i }}"  {{ ( date('F') ==  date('F', mktime(0, 0, 0, $i, 1)) ? 'selected' : '')}} >{{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                                   </option>
                                                  @endfor

                                                </select>
                                            </div>

                                            @php
                                            
                                            $days = \Carbon\Carbon::now()->daysInMonth;

                                            @endphp

                                             <div class="form-group">
                                                <label class="text-dark" for="password">Date
                                                </label>
                                                <select class="form-control" id="date" name="date"> 
                                                  <option value=""> Select Date</option>

                                                  @for ($i=1; $i<=$days; $i++)
                                                    <option value="{{ $i }}" >{{ sprintf("%02d", $i) }}
                                                   </option>
                                                  @endfor

                                                </select>
                                            </div>
                                        </div>
                                    </div> 

                                    <div class="row">
                                       <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">File 
                                                </label>
                                                <input  name="file[]"  type="file" required="">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" id="add_button">
                                       <div class="col-lg-5 col-md-6 mx-auto">
                                       <a href="javascript:void(0);" class="add_button" title="Add field">+</a>
                                     </div>
                                    </div>
                             
                              <!-- Submit Button -->
                              <div class="col-12 text-center">
                                  <button type="submit" class="btn btn-danger">Update Document
                                  </button>
                              </div>

                                </form>
                            </div>
                             
                             <div class="row mb-2">
                                <div class="col-6">
                                    <h4 class="mt-0 text-left">Files</h4>
                                </div>
                            </div>

                              <div class="table-responsive">           
                                <table id="subcontractors-table" class="table card-table dataTable no-footer" role="grid" aria-describedby="subcontractors-table_info">
                                 <thead class="d-none">
                                    <tr role="row">
                                       <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 0px;"></th>
                                    </tr>
                                 </thead>
                                 <tbody class="row">
                                  @foreach($document->files as $file)

                                   @php
                                     $fileInfo = pathinfo($file->file);
                                     $extension = $fileInfo['extension'];
                                    
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
                                                action="{{route('documents.file.destroy',$file->id)}}?path={{$file->file}}"> 
                                                 @csrf
                                                {{ method_field('DELETE') }}

                                                <button 
                                                  type="submit"
                                                  onclick="return confirm('Are you sure?')"
                                                  class="btn btn-neutral bg-transparent btn-icon" data-original-title="Delete Property Type" title="Delete Property Type"><i class="fa fa-trash text-danger"></i> </button>
                                              </form>
                                            </span>
                                             <div class="card card-table-item" 
                                             style="width: 100%;">
                                                <div class="card-body pb-0">
                                                   <div class="author mt-1">
                                                    <span class="doc_type_m">
                                                      {{ @$file->document->property->property_name }} 
                                                    </span></br>
                                                    <a href="{{ url($file->file) }}" target="_blank">
                                                      <p> {{ @$file->name }} </p>
                                                      <img class="avatar border-gray" src="{{ asset('img/'.$extension.'.png') }}">
                                                      </a> 
                                                       <span class="doc-type"> 
                                                      {{  @$file->document->document_type->name }}</span>              
                                                   </div>
                                                </div>
                                             </div>
                                       </td>
                                    </tr>

                                    @endforeach
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
$(document).ready(function(){
    var addButton = $('.add_button'); //Add button selector
    var wrapper = $('#add_button'); //Input field wrapper
    var fieldHTML = '<div style="position:relative;"> <a href="javascript:void(0);" class="remove_button">X</a>  <div class="row"> <div class="col-lg-5 col-md-6 mx-auto"> <div class="form-group"> <label class="text-dark" for="password"> File Name </label> <input name="dname[]" class="form-control" type="text" required> </div> </div> </div> <div class="row"> <div class="col-lg-5 col-md-6 mx-auto"> <div class="form-group"> <label class="text-dark" for="password">File </label> <input name="file[]" type="file" required=""> </div> </div> </div></div>'; //New input field html 
    var x = 1; //Initial field counter is 1
    
    //Once add button is clicked
    $(addButton).click(function(){
        //Check maximum number of input fields
            x++; //Increment field counter
            $(wrapper).before(fieldHTML); //Add field html
    });
    
    //Once remove button is clicked
    $(document).on('click', '.remove_button', function(e){
        e.preventDefault();
        $(this).parent('div').remove(); //Remove field html
        x--; //Decrement field counter
    });

     $('#month').click(function(){
        var month = $(this).val();
        var year = $('#year').val();
        var days =  new Date(year, month, 0).getDate();
        var Html = '';

       if(days){
        Html +='<option>Select Date</option>';
        for (let i = 1; i <= days; i++) {
          Html += '<option value="'+i+'" >'+minTwoDigits(i)+'</option>';
        }
        $('#date').html('');   
        $('#date').html(Html);  
       }

  });

  function minTwoDigits(n) {
    return (n < 10 ? '0' : '') + n;
  }

});
</script>

<style type="text/css">
  
span.cross{
    position: absolute;
    z-index: 10;
    right: 30px;
    display: none;
}
span.doc-type{
 font-size: 12px;
padding: 8px 0px;
 display: block;
}
tr:hover span.cross{
  display: block;
}
button.btn.btn-neutral.bg-transparent.btn-icon{
  background-color: transparent !important;
}
td{
  width: 100%;
}
span.doc_type_m{
 font-size: 10px;
 padding-top: 3px;
 display: block;
}


.add_button {
    height: 35px;
    width: 30px;
    border: 2px solid;
    text-align: center;
    font-size: 23px;
    display: block;
    font-weight: 900;
}.remove_button{
    position: absolute;
    right: 49px;
    font-weight: 900;
    height: 20px;
    width: 20px;
    border: 1px solid;
    text-align: center;
}
</style>
@endsection
@extends('layouts.admin-app')

@section('title', 'Proposal')

@section('content')

@include('includes.back', 
['url' => route("projects.show", ['project' => request()->project]),
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
                        <h4 class="mt-0 text-left"> {{ @$proposal->project->name }} - Update Proposal</h4>
                    </div>
                </div>

               <div class="row">
                        <div class="col-md-12">
                            <div class="card-body">
                                <form   method="post" 
                              action="{{ route('projects.proposals.update',['id' => request()->id]) }}" enctype="multipart/form-data">
                                  @csrf

                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Subcontractors
                                                </label>
                                                <select class="form-control"> 
                                                  <option>
                                                      {{ @$subcontractor->name }}
                                                  </option>
                                                 
                                                </select>
                                            </div>
                                        </div>
                                    </div> 
                                    
                                    <div class="row">
                                         <div class="col-lg-5 col-md-6 mx-auto">

                                           <div class="form-group">
                                                <label class="text-dark" for="password">
                                                  Labor Cost 
                                                </label>
                                                <input  name="labour_cost" value="{{ $proposal->labour_cost }}" type="number" class="form-control" placeholder="Labor Cost" step="any">
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="row">
                                         <div class="col-lg-5 col-md-6 mx-auto">

                                           <div class="form-group">
                                                <label class="text-dark" for="password">Material  
                                                </label>
                                                <input  name="material" value="{{ $proposal->material }}" type="number" class="form-control" placeholder="Material" step="any">
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="row">
                                         <div class="col-lg-5 col-md-6 mx-auto">

                                           <div class="form-group">
                                                <label class="text-dark" for="password">Subcontractor Price 
                                                </label>
                                                <input  name="subcontractor_price" value="{{ $proposal->subcontractor_price }}" type="number" class="form-control" placeholder="Subcontractor Price Cost" step="any">
                                            </div>
                                        </div>
                                    </div>

                                     <div class="row">
                                         <div class="col-lg-5 col-md-6 mx-auto">

                                           <div class="form-group">
                                                <label class="text-dark" for="password">Trade Budget</label>
                                                <input  name="trade_budget" value="{{ @$proposal->trade_budget }}" type="number" class="form-control" placeholder="Trade Budget" step="any">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                         <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Notes
                                                </label>
                                                <textarea  name="notes"  type="text" class="form-control" placeholder="Notes" >
                                                 {{ $proposal->notes }}</textarea>
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
                                            <h6>Change Orders</h6>
                                        </div>
                                   </div>
                                  
                                   @if($proposal->changeOrders)

                                     @foreach($proposal->changeOrders as $k => $order )
                                     <div >
                                     <a href="javascript:void(0);" class="remove_button_2">X</a>  
                                     <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                           <p> Change Order {{ $k+1}} </p>
                                           <div class="form-group">
                                              <label class="text-dark" for="password"> Type </label>
                                              <select class="form-control" name="change_orders[type][]">
                                                 <option value="add" {{ $order->type == \App\Models\ChangeOrder::ADD ? 'selected' : '' }}>ADD (+)</option>
                                                 <option value="sub" {{ $order->type == \App\Models\ChangeOrder::SUB ? 'selected' : '' }}>MINUS (-)</option>
                                              </select>
                                           </div>
                                        </div>
                                     </div>
                                     <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                           <div class="form-group"> <label class="text-dark" for="password">Price </label> <input class="form-control" name="change_orders[subcontractor_price][]" 
                                            value="{{ $order->subcontractor_price }}" type="number" required="" step="any"> </div>
                                        </div>
                                     </div>
                                     <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                           <div class="form-group"> <label class="text-dark" for="password">Notes </label> <textarea class="form-control" name="change_orders[notes][]">{{ $order->notes }}</textarea> </div>
                                        </div>
                                        <input type="hidden"  value="{{ $order->id }}" name="change_orders[id][]">
                                     </div>
                                   </div>

                                     @endforeach
                                   @endif
                                
                                   <div class="row" id="add_button">
                                       <div class="col-lg-5 col-md-6 mx-auto">
                                       <a href="javascript:void(0);" class="add_button" title="Add field">+</a>
                                     </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="col-12 text-center">
                                        <button id="change-password-button" type="submit" class="btn btn-danger">Update Proposal
                                        </button>
                                    </div>

                                </form>
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
                                
                                  @if($proposal->files)
                                  @foreach(@explode(',',$proposal->files) as $file)

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
                                                action="{{route('projects.proposals.file.destroy', $proposal->id)}}?path={{$file}}"> 
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

@endsection


@section('pagescript')
<script type="text/javascript">
$(document).ready(function(){
    var addButton = $('.add_button'); //Add button selector
    var wrapper = $('#add_button'); //Input field wrapper
    var fieldHTML = '<div style="position:relative;"> <a href="javascript:void(0);" class="remove_button">X</a>  <div class="row"> <div class="col-lg-5 col-md-6 mx-auto"> <div class="form-group"> <label class="text-dark" for="password"> Type </label><select class="form-control" name="change_orders[type][]"><option value="add">ADD (+)</option><option value="sub">MINUS (-)</option></select></div> </div> </div> <div class="row"> <div class="col-lg-5 col-md-6 mx-auto"> <div class="form-group"> <label class="text-dark" for="password">Price </label> <input class="form-control" name="change_orders[subcontractor_price][]" type="number" required="" step="any"> </div> </div> </div><div class="row"> <div class="col-lg-5 col-md-6 mx-auto"> <div class="form-group"> <label class="text-dark" for="password">Notes </label> <textarea class="form-control" name="change_orders[notes][]"></textarea> </div> </div> </div></div>'; //New input field html 
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

    //Once remove button is clicked
    $(document).on('click', '.remove_button_2', function(e){
        e.preventDefault();
        $(this).parent('div').remove(); //Remove field html
        x--; //Decrement field counter
    });


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
}
.remove_button{
    position: absolute;
    right: 49px;
    font-weight: 900;
    height: 20px;
    width: 20px;
    border: 1px solid;
    text-align: center;
}

.remove_button_2{
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
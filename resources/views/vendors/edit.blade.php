@extends('layouts.admin-app')

@section('title', 'Vendor')

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
                        <h4 class="mt-0 text-left">Edit Vendor</h4>
                    </div>
                </div>

               <div class="row">
                        <div class="col-md-12">
                            <div class="card-body">
                                <form   method="post" 
                              action="{{ route('vendors.update',$vendor->id) }}" enctype="multipart/form-data">
                                  @csrf
                                  @method('PUT')

                                    <!-- Current Password -->
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Name 
                                                </label>
                                                <input  name="name" value="{{ $vendor->name}}" type="text" class="form-control" placeholder="Vendor Name" required="">
                                            </div>
                                        </div>
                                 
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">City 
                                                </label>
                                                <input  name="city" value="{{ $vendor->city }}" type="text" class="form-control" placeholder="City">
                                            </div>
                                        </div>
                                    
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">State
                                                </label>
                                                <input  name="state"  value="{{ $vendor->state }}" type="text" class="form-control" placeholder="State" >
                                            </div>
                                        </div>
                                  
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Zip Code 
                                                </label>
                                                <input  name="zip_code"  value="{{ $vendor->zip_code }}" type="text" class="form-control" placeholder="Zip Code" >
                                            </div>
                                        </div> 
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Email 1
                                                </label>
                                                <input  name="email_1"  value="{{ $vendor->email_1 }}" type="email" class="form-control" placeholder="Email 1" >
                                            </div>
                                        </div> 
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Email 2
                                                </label>
                                                <input  name="email_2"  value="{{ $vendor->email_2 }}" type="email" class="form-control" placeholder="Email 2" >
                                            </div>
                                        </div> 
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Email 3  
                                                </label>
                                                <input  name="email_3"  value="{{ $vendor->email_3 }}" type="email" class="form-control" placeholder="Email 3" >
                                            </div>
                                        </div>
                                       
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Contact Name 
                                                </label>
                                                <input  name="contact_name"  value="{{ $vendor->contact_name }}" type="text" class="form-control" placeholder="Contact Name" >
                                            </div>
                                        </div>
                                       
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Office Phone  
                                                </label>
                                                <input  name="office_phone"  value="{{ $vendor->office_phone }}" type="text" class="form-control" placeholder="Office Phone" >
                                            </div>
                                        </div>

                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Mobile 
                                                </label>
                                                <input  name="mobile"  value="{{ $vendor->mobile }}" type="text" class="form-control" placeholder="Mobile" >
                                            </div>
                                        </div>
                                   
                                        
                                         <div class="col-lg-12 col-md-12">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Notes 
                                                </label>
                                                <textarea  style="min-height: 95px;" name="notes"  type="text" class="form-control" placeholder="Notes" >
                                                 {{ $vendor->notes }}</textarea>
                                            </div>
                                        </div>
                                

                                    </div>
                                    
                                    
                                 
                                   <div class="row mb-2">
                                        <div class="col-6">
                                            <h4 class="mt-0 text-left">Materials</h4>
                                        </div>
                                    </div>
                                     @foreach($vendor->materials as $material)
                                     <div class="row">

                                        <div class="col-lg-5 col-md-5">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Account Number 
                                                </label>
                                                <input  name="materials[account_number][]"  value="{{ @$material->account_number}}" type="text" class="form-control" placeholder="Account Number" >
                                            </div>
                                        </div>
                                        <div class="col-lg-5 col-md-5">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Material Name 
                                                </label>
                                                <input  name="materials[name][]"  value="{{ @$material->name }}" type="text" class="form-control" placeholder="Material Name" >
                                            </div>
                                            <input type="hidden"  name="materials[id][]" value="{{@$material->id }}">
                                        </div>
                                       <a href="javascript:void(0);" class="remove_button" title="Remove Material">X</a>
                                     
                                    </div>
                                     @endforeach
                                    
                                    <div class="row" id="add_button">

                                        <div class="col-lg-5 col-md-5">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Account Number 
                                                </label>
                                                <input  name="materials[account_number][]"  value="" type="text" class="form-control" placeholder="Account Number" >
                                            </div>
                                        </div>

                                        <div class="col-lg-5 col-md-5">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Material Name 
                                                </label>
                                                <input  name="materials[name][]"  value="" type="text" class="form-control" placeholder="Material Name" >
                                            </div>
                                        </div>

                                       <div class="" style="margin-top: 25px;">
                                       <a href="javascript:void(0);" class="add_button" title="Add Material">+</a>
                                     </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="col-12 text-center">
                                        <button id="change-password-button" type="submit" class="btn btn-danger">Update Vendor
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
$(document).ready(function(){
    var addButton = $('.add_button'); //Add button selector
    var wrapper = $('#add_button'); //Input field wrapper
    var fieldHTML = '<div class="row"><div class="col-lg-5 col-md-5"> <div class="form-group"> <label class="text-dark" for="password">Account Number </label> <input name="materials[account_number][]" value="" type="text" class="form-control" placeholder="Account Number" > </div></div><div class="col-lg-5 col-md-5"> <div class="form-group"> <label class="text-dark" for="password">Material Name </label> <input name="materials[name][]" value="" type="text" class="form-control" placeholder="Material Name" > </div></div><a href="javascript:void(0);" class="remove_button" title="Remove Material">X</a> </div>'; //New input field html 
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
    font-weight: 900;
    height: 35px;
    width: 30px;
    border: 2px solid;
    display: block;
    text-align: center;
    padding-top: 5px;
    margin-top: 25px;
    text-decoration: none;
}
</style>
@endsection

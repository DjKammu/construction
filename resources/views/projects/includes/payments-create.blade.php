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
                        <h4 class="mt-0 text-left"> {{ @$proposal->project->name }} -  Make Payment </h4>
                    </div>
                </div>

               <div class="row">
                        <div class="col-md-12">
                            <div class="card-body">
                                <form   method="post" 
                              action="{{ route('projects.payments',['id' => (@$proposal->id) ? @$proposal->id : 0 ]) }}"
                               enctype="multipart/form-data">
                                  @csrf
                                   @if(@$proposal)
                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Trades
                                                </label>
                                                <select onchange="return window.location.href ='?trade='+this.value" class="form-control" name="trade_id"> 
                                                  @foreach($trades as $trade)
                                                   <option value="{{ $trade->id }}" {{ 
                                                   $proposal->trade_id == $trade->id ? 'selected' : '' 
                                                   }}>{{ $trade->name}}
                                                   </option>
                                                  @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div> 
                                    
                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                              <input type="radio" name="type" checked="checked" value="subcontractor" />
                                              <label class="text-dark" for="password">Subcontractor
                                              </label>
                                              
                                              <input type="radio" name="type" value="venodr" />
                                              <label class="text-dark" for="password">Vendor
                                              </label>
                                              
                                         </div>
                                         </div>
                                        </div>
                                   
                                    <div class="subcontractor-vendor" id="subcontractor">
                                          <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Subcontractor
                                                </label>
                                                <select class="form-control" name="subcontractor_id"> 
                                                   <option value="{{ @$proposal->subcontractor_id }}" >{{ @$proposal->subcontractor->name}}
                                                   </option>
                                                </select>
                                            </div>
                                        </div>
                                       </div>
                                           <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Contract Amount 
                                                </label>
                                                 <input   value="${{ $totalAmount }}"class="form-control" readonly="">
                                            </div>
                                        </div>
                                    </div>  

                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password"> Due Payment
                                                </label>
                                                 <input   value="${{ $dueAmount }}"class="form-control" readonly="">
                                            </div>
                                        </div>
                                    </div> 
      
                                    </div> 

                                    @else
                                    
                                      <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Trades
                                                </label>
                                                <select class="form-control" name="trade_id"> 
                                                  @foreach($trades as $trade)
                                                   <option value="{{ $trade->id }}" >{{ $trade->name}}
                                                   </option>
                                                  @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div> 

                                    @endif
                                     <div class="row subcontractor-vendor" id="venodr" 
                                         {{ (@!$proposal->id  && @$proposal->subcontractor_id) ? 'style="display: none;"' : ''}}>
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Vendor
                                                </label>
                                                <select class="form-control" name="vendor_id"> 
                                                  <option value="">Select Vendor</option>
                                                  @foreach(@$vendors as $vendor)
                                                   <option value="{{ $vendor->id }}">{{ $vendor->name}}
                                                   </option>
                                                  @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div> 

                                    <div class="row">
                                         <div class="col-lg-5 col-md-6 mx-auto">

                                           <div class="form-group">
                                                <label class="text-dark" for="password"> Invoice Number
                                                </label>
                                                <input  name="invoice_number" value="{{ old('invoice_number')}}" type="text" class="form-control" placeholder="Invoice Number" >
                                            </div>
                                        </div>
                                    </div> 

                                    <div class="row">
                                         <div class="col-lg-5 col-md-6 mx-auto">

                                           <div class="form-group">
                                                <label class="text-dark" for="password"> Payment
                                                </label>
                                                <input  name="payment_amount" value="{{ old('payment_amount')}}" type="number" class="form-control" placeholder="Payment Amount" step="any"step="any">
                                            </div>
                                        </div>
                                    </div> 
                                      <div class="row">
                                     <div class="col-lg-5 col-md-6 mx-auto">
                                        <div class="form-group">
                                            <label class="text-dark" for="password">Date 
                                            </label>
                                            <input  name="date" value="{{ old('date')}}" type="text" class="form-control date" placeholder="Date">
                                        </div>
                                     </div>
                                    </div>

                                     <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Status
                                                </label>
                                                <select class="form-control" name="status"> 
                                                  <option value="">Select Status</option>
                                                  <option value="{{\App\Models\Payment::DEPOSIT_PAID_STATUS }}">{{\App\Models\Payment::DEPOSIT_PAID_TEXT  }}</option>
                                                  <option value="{{ \App\Models\Payment::PROGRESS_PAYMENT_STATUS }}">{{\App\Models\Payment::PROGRESS_PAYMENT_TEXT  }}</option>
                                                  <option value="{{ \App\Models\Payment::RETAINAGE_PAID_STATUS }}">{{\App\Models\Payment::RETAINAGE_PAID_TEXT  }}</option>
                                                  <option value="{{ \App\Models\Payment::FINAL_PAYMENT_STATUS }}">{{\App\Models\Payment::FINAL_PAYMENT_TEXT  }}</option>
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
                                                 {{ old('notes')}}</textarea>
                                            </div>
                                        </div>
                                    </div> 

                                   <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">File
                                                </label>
                                                <input  name="file"  type="file" >
                                            </div>
                                        </div>
                                    </div>


                                    <!-- Submit Button -->
                                    <div class="col-12 text-center">
                                      <input type="hidden" name="project_id" value="{{ $id }}" />
                                        <button id="change-password-button" type="submit" class="btn btn-danger">Create Payment
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

});

</script>
@endsection

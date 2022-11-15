@extends('layouts.admin-app')

@section('title', 'FFE Payment')

@section('content')

@include('includes.back',['url' => route('ffe.index',['project' => request()->project ]) , 'to' => 'To FFE'])


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
                        <h4 class="mt-0 text-left"> {{ @$proposal->project->name }} -  Make FEE Payment </h4>
                    </div>
                </div>

               <div class="row">
                        <div class="col-md-12">
                            <div class="card-body">
                                <form   method="post" 
                              action="{{ route('projects.ffe.payments.store',['id' => (@$proposal->id) ? @$proposal->id : 0 , 'project' => request()->project ]) }}"
                               enctype="multipart/form-data">
                                  @csrf

                                   @if(@$proposal)

                                   <div class="row">
                                    <div class="col-lg-5 col-md-6 mx-auto">
                                        <div class="form-group">
                                          <input type="radio" name="non_contract" checked="checked" 
                                          div="subcontractor" value="0" />
                                          <label class="text-dark" for="password">Contract Vendor
                                          </label>
                                          
                                          <input type="radio" name="non_contract"   div="vendor" value="1" />
                                          <label class="text-dark" for="password">Non Contract Vendor
                                          </label>
                                          
                                     </div>
                                     </div>
                                    </div>
                                    
                                    <div class="subcontractor-vendor" id="subcontractor">
                                      <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Trades
                                                </label>
                                                <select onchange="return window.location.href ='?trade='+this.value" class="form-control" name="0_f_f_e_trade_id"> 
                                                  @foreach($trades as $trade)
                                                   <option value="{{ $trade->id }}" {{ 
                                                   $proposal->f_f_e_trade_id == $trade->id ? 'selected' : '' 
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
                                                  <label class="text-dark" for="password">Vendor
                                                  </label>
                                                   <select class="form-control" name="0_f_f_e_vendor_id"> 
                                                     <option value="{{ @$proposal->f_f_e_vendor_id }}" >{{ @$proposal->vendor->name}}
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

                                     <div class="subcontractor-vendor" id="vendor" 
                                     style="display: none;">
                                          <div class="row">
                                           <div class="col-lg-5 col-md-6 mx-auto">
                                              <div class="form-group">
                                                  <label class="text-dark" for="password">Trades
                                                  </label>
                                                  <select class="form-control" name="1_f_f_e_trade_id"> 
                                                    @foreach($allTrades as $trade)
                                                     <option value="{{ $trade->id }}" >{{ $trade->name}}
                                                     </option>
                                                    @endforeach
                                                  </select>
                                              </div>
                                          </div>
                                        </div>
                                         
                                          <div class="row">
                                          <div class="col-lg-5 col-md-6 mx-auto">
                                              <div class="form-group">
                                                  <label class="text-dark" for="password">Vendor
                                                  </label>
                                                  <select class="form-control" name="1_f_f_e_vendor_id"> 
                                                    <option value="">Select Vendor</option>
                                                    @foreach(@$vendors as $vendor)
                                                     <option value="{{ $vendor->id }}">{{ $vendor->name}}
                                                     </option>
                                                    @endforeach
                                                  </select>
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
                                                <select class="form-control" name="1_f_f_e_trade_id"> 
                                                  @foreach($allTrades as $trade)
                                                   <option value="{{ $trade->id }}" >{{ $trade->name}}
                                                   </option>
                                                  @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div> 

                                    <div class="row">
                                          <div class="col-lg-5 col-md-6 mx-auto">
                                              <div class="form-group">
                                                  <label class="text-dark" for="password">Vendor
                                                  </label>
                                                  <select class="form-control" name="1_f_f_e_vendor_id"> 
                                                    <option value="">Select Vendor</option>
                                                    @foreach(@$vendors as $vendor)
                                                     <option value="{{ $vendor->id }}">{{ $vendor->name}}
                                                     </option>
                                                    @endforeach
                                                  </select>
                                              </div>
                                          </div>
                                        </div>

                                    @endif

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

                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Unconditional Lien Release File
                                                </label>
                                                <input  name="unconditional_lien_release_file"  type="file" >
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Conditional Lien Release File
                                                </label>
                                                <input  name="conditional_lien_release_file"  type="file" >
                                            </div>
                                        </div>
                                    </div>


                                    <!-- Submit Button -->
                                    <div class="col-12 text-center">
                                      <input type="hidden" name="project_id" value="{{ $id }}" />
                                        <button id="change-password-button" type="submit" class="btn btn-danger">Create FFE Payment
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

$("input[name='non_contract']").click(function() {
      var id = $(this).attr('div');
      $(".subcontractor-vendor").hide();
      $("#" + id).show();
  });


});

</script>
@endsection

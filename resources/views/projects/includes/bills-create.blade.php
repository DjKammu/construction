@extends('layouts.admin-app')

@section('title', 'Bill')

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
                        <h4 class="mt-0 text-left"> {{ @$proposal->project->name }} -  Make Bill </h4>
                    </div>
                </div>

               <div class="row">
                        <div class="col-md-12">
                            <div class="card-body">
                                <form   method="post" 
                              action="{{ route('projects.bills',['id' => (@$proposal->id) ? @$proposal->id : 0 ]) }}"
                               enctype="multipart/form-data">
                                  @csrf

                                   @if(@$proposal)

                                   <div class="row">
                                    <div class="col-lg-5 col-md-6 mx-auto">
                                        <div class="form-group">
                                          <input type="radio" name="type" checked="checked" value="subcontractor" />
                                          <label class="text-dark" for="password">Subcontractor
                                          </label>
                                          
                                          <input type="radio" name="type" value="vendor" />
                                          <label class="text-dark" for="password">Vendor
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
                                                <select onchange="return window.location.href ='?trade='+this.value" class="form-control" name="subcontractor_trade_id"> 
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

                                     <div class="subcontractor-vendor" id="vendor" 
                                     style="display: none;">
                                          <div class="row">
                                           <div class="col-lg-5 col-md-6 mx-auto">
                                              <div class="form-group">
                                                  <label class="text-dark" for="password">Trades
                                                  </label>
                                                  <select class="form-control" name="vendor_trade_id"> 
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
                                                  <label class="text-dark" for="password">Vendor Material
                                                  </label>
                                                  <select id="materials" class="form-control" name="material_id"> 
                                                    <option value="">Select Material</option>
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
                                                <select class="form-control" name="vendor_trade_id"> 
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
                                                <label class="text-dark" for="password"> Total Subcontractor Payment 
                                                </label>
                                                <input  name="total_subcontractor_payment" id="total_subcontractor_payment" value="{{ old('total_subcontractor_payment')}}" type="number" class="form-control" placeholder="Total Subcontractor Payment" step="any"step="any">
                                            </div>
                                        </div>
                                    </div>  

                                     <div class="row">
                                         <div class="col-lg-5 col-md-6 mx-auto">

                                           <div class="form-group">
                                                <label class="text-dark" for="password"> Retainage Percentage
                                                </label>
                                                <input  name="retainage_percentage" id="retainage_percentage" value="{{ @$proposal->project->subcontractor_retainage }}" type="number" class="form-control" placeholder="Retainage Percentage" step="any"step="any">
                                            </div>
                                        </div>
                                    </div>  
                                     <div class="row">
                                         <div class="col-lg-5 col-md-6 mx-auto">

                                           <div class="form-group">
                                                <label class="text-dark" for="password"> Retainage Held 
                                                </label>
                                                <input  type="number" id="retainage_held" class="form-control" value=""  placeholder="Retainage Held" class="form-control" readonly="">
                                            </div>
                                        </div>
                                    </div>  

                                    <div class="row">
                                         <div class="col-lg-5 col-md-6 mx-auto">

                                           <div class="form-group">
                                                <label class="text-dark" for="password"> Subcontractor Payment minus Retainage
                                                </label>
                                                <input id="payment_amount" type="number" class="form-control" placeholder="Subcontractor Payment minus Retainage"  readonly="">
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
                                                <label class="text-dark" for="password">Assigned To
                                                </label>
                                                <select class="form-control" name="assigned_to"> 
                                                  <option value="">Select Assigned To</option>
                                                   @foreach(@$users as $user)
                                                   <option value="{{ $user->id }}">{{ $user->name}}
                                                   </option>
                                                  @endforeach
                                                </select>
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
                                                <label class="text-dark" for="password">Purchase Order
                                                </label>
                                                <input  name="purchase_order"  type="file" >
                                            </div>
                                        </div>
                                    </div>

                                         <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <input type="checkbox"
                                                 name="bill_status" value="1">
                                                <label class="text-dark" for="password">
                                                  <b>Mark as Paid</b>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- <div class="row">
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
 -->

                                    <!-- Submit Button -->
                                    <div class="col-12 text-center">
                                      <input type="hidden" name="project_id" value="{{ $id }}" />
                                        <button id="change-password-button" type="submit" class="btn btn-danger">Create Bill
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


  var retainage_percentage = '{{ @$proposal->project->subcontractor_retainage }}'

$('#total_subcontractor_payment').on('input', function (e) {
      let total_subcontractor_payment = $(this).val();
      let retainage_held = total_subcontractor_payment*retainage_percentage/100;
      $('#retainage_held').val(retainage_held);
      let payment_amount = parseFloat(total_subcontractor_payment) - parseFloat(retainage_held);
      $('#payment_amount').val(payment_amount);
    
 });

$('#retainage_percentage').on('input', function (e) {
       retainage_percentage = $(this).val();
       if(retainage_percentage > 100){
         alert("Can't be exceed 100");
         retainage_percentage = '{{ @$proposal->project->subcontractor_retainage }}'
         $(this).val(retainage_percentage)
       }

      let total_subcontractor_payment = $('#total_subcontractor_payment').val();
      let retainage_held = total_subcontractor_payment*retainage_percentage/100;
      $('#retainage_held').val(retainage_held);
      let payment_amount = parseFloat(total_subcontractor_payment) - parseFloat(retainage_held);
      $('#payment_amount').val(payment_amount);
    
 });

});

</script>
@endsection

@extends('layouts.admin-app')

@section('title', 'FFE Bill')

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
                        <h4 class="mt-0 text-left"> {{ @$bill->project->name }} -  Make Bill </h4>
                    </div>
                </div>

               <div class="row">
                        <div class="col-md-12">
                            <div class="card-body">
                                <form   method="post" 
                              action="{{ route('projects.ffe.bills.update',['project' => request()->project,
                              'id' => $bill->id]) }}" enctype="multipart/form-data">
                                  @csrf

                                  @if(@$bill->proposal()->exists())
                                  
                                   <div class="row">
                                    <div class="col-lg-5 col-md-6 mx-auto">
                                        <div class="form-group">
                                          <input type="radio" name="non_contract" 
                                          div="subcontractor" value="0"  {{ (@$bill->non_contract == '0' ) ? 'checked="checked"' : '' }}  />
                                          <label class="text-dark" for="password">Contract Vendor
                                          </label>
                                          
                                          <input type="radio" name="non_contract" name="non_contract" 
                                          div="vendor" {{ (@$bill->non_contract == '1') ? 'checked="checked"' : '' }} value="1" />
                                          <label class="text-dark" for="password">Non Contract Vendor
                                          </label>
                                     </div>
                                     </div>
                                    </div>

                                    <div class="subcontractor-vendor" id="subcontractor" style="display: {{  (@$bill->non_contract == '0' ) ? 'block' : 'none' }};" >
                                      <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password"> Trades
                                                </label>
                                                <select onchange="return window.location.href ='?trade='+this.value" class="form-control" name="0_ffe_trade_id"> 
                                                  @foreach($trades as $trade)
                                                   <option value="{{ $trade->id }}" {{ 
                                                   @$bill->proposal->f_f_e_trade_id == $trade->id ? 'selected' : '' 
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
                                                   <select class="form-control" name="0_ffe_vendor_id"> 
                                                     <option value="{{ @$bill->proposal->f_f_e_vendor_id }}" >{{ @$bill->proposal->vendor->name}}
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
                                                 <input   value="${{ \App\Models\Payment::format($totalAmount) }}"class="form-control" readonly="">
                                            </div>
                                        </div>
                                    </div>  

                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password"> Due Payment
                                                </label>
                                                 <input   value="${{ \App\Models\Payment::format($dueAmount) }}"class="form-control" readonly="">
                                            </div>
                                        </div>
                                    </div>

                                    </div> 

                                     <div class="subcontractor-vendor" id="vendor" 
                                    style="display: {{  (@$bill->non_contract == '1') ? 'block' : 'none' }};"   >
                                          <div class="row">
                                           <div class="col-lg-5 col-md-6 mx-auto">
                                              <div class="form-group">
                                                  <label class="text-dark" for="password">Trades
                                                  </label>
                                                  <select class="form-control" name="1_ffe_trade_id"> 
                                                    @foreach($allTrades as $trade)
                                                     <option value="{{ $trade->id }}" {{ 
                                                   $bill->ffe_trade_id == $trade->id ? 'selected' : '' 
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
                                                  <select class="form-control" name="1_ffe_vendor_id"> 
                                                    <option value="">Select Vendor</option>
                                                    @foreach(@$vendors as $vendor)
                                                     <option value="{{ $vendor->id }}" {{ 
                                                      ($bill->ffe_vendor_id == $vendor->id ) ? 'selected' : ''}} >{{ $vendor->name}}
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
                                                <select class="form-control" name="1_ffe_trade_id"> 
                                                  @foreach($allTrades as $trade)
                                                   <option value="{{ $trade->id }}" {{ 
                                                   $bill->ffe_trade_id == $trade->id ? 'selected' : '' 
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
                                                <select class="form-control" name="1_ffe_vendor_id"> 
                                                  <option value="">Select Vendor</option>
                                                  @foreach(@$vendors as $vendor)
                                                   <option value="{{ $vendor->id }}" {{ ($bill->ffe_vendor_id == $vendor->id ) ? 'selected' : ''}}>{{ $vendor->name}}
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
                                                <input  name="invoice_number" value="{{ @$bill->invoice_number }}" type="text" class="form-control" placeholder="Invoice Number" >
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="row">
                                         <div class="col-lg-5 col-md-6 mx-auto">

                                           <div class="form-group">
                                                <label class="text-dark" for="password"> Payment
                                                </label>
                                                <input  name="payment_amount" value="{{ $bill->payment_amount }}" type="number" class="form-control" placeholder="Payment Amount" step="any">
                                            </div>
                                        </div>
                                    </div> 
                                    

                                      <div class="row">
                                     <div class="col-lg-5 col-md-6 mx-auto">
                                        <div class="form-group">
                                            <label class="text-dark" for="password">Date 
                                            </label>
                                            <input  name="date" value="{{ $bill->date }}" type="text" class="form-control date" placeholder="Date">
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
                                                  <option value="{{\App\Models\Payment::DEPOSIT_PAID_STATUS }}" {{ $bill->status == \App\Models\Payment::DEPOSIT_PAID_STATUS ? 'selected' : ''}}>{{\App\Models\Payment::DEPOSIT_PAID_TEXT  }}</option>
                                                  <option value="{{ \App\Models\Payment::PROGRESS_PAYMENT_STATUS }}" {{ $bill->status == \App\Models\Payment::PROGRESS_PAYMENT_STATUS ? 'selected' : ''}}>{{\App\Models\Payment::PROGRESS_PAYMENT_TEXT  }}</option>
                                                  <option value="{{ \App\Models\Payment::RETAINAGE_PAID_STATUS }}" {{ $bill->status == \App\Models\Payment::RETAINAGE_PAID_STATUS ? 'selected' : ''}}>{{\App\Models\Payment::RETAINAGE_PAID_TEXT  }}</option>
                                                  <option value="{{ \App\Models\Payment::FINAL_PAYMENT_STATUS }}" {{ $bill->status == \App\Models\Payment::FINAL_PAYMENT_STATUS ? 'selected' : ''}}>{{\App\Models\Payment::FINAL_PAYMENT_TEXT  }}</option>
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
                                                 {{ $bill->notes }}</textarea>
                                            </div>
                                        </div>
                                    </div> 
                                    
    
                                   <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Invoice
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
                                                {{ $bill->bill_status == \App\Models\Bill::PAID_BILL_STATUS ? 'checked' : ''}} 
                                                 name="bill_status" 
                                                onclick="return window.location.href='{{ $bill->id }}/bill-stattus?bill_status='+this.checked">
                                                <label class="text-dark" for="password">
                                                  <b>Mark as Paid</b>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Submit Button -->
                                    <div class="col-12 text-center">
                                        <button id="change-password-button" type="submit" class="btn btn-danger">Update Bill
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
                                
                                  @if($bill->file)
                                   @php
                                     $fileInfo = pathinfo($bill->file);
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
                                                action="{{route('projects.ffe.bills.file.destroy',['project' => request()->project, 'id' => $bill->id ])}}?path={{$bill->file}}"> 
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
                                                    <a href="{{ asset($bill->file) }}" target="_blank">
                                                      <p> {{ @$file->name }} </p>
                                                      <img class="avatar border-gray" src="{{ asset('img/'.@$extension.'.png') }}">
                                                      </a>            
                                                   </div>
                                                </div>
                                             </div>
                                       </td>
                                    </tr>

                                    @endif


                                    @if($bill->purchase_order)
                                   @php
                                     $fileInfo = pathinfo($bill->purchase_order);
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
                                                action="{{route('projects.ffe.bills.file.destroy', ['project' => request()->project, 'id' => $bill->id ])}}?path={{$bill->purchase_order}}"> 
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
                                                    <a href="{{ asset($bill->purchase_order) }}" target="_blank">
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

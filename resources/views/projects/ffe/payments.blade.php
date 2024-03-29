<div class="tab-pane" id="payments" role="tabpanel" aria-expanded="true">
   <div class="row mb-2">
         <div class="col-6">
            <h4 class="mt-0 text-left">{{ @$project->name }} - FFE Payments List </h4>
        </div>
        <div class="col-6 text-right">
            <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='{{ route("projects.ffe.payments",['project' => request()->project ])  }}'">Add Payment
            </button>
        </div>

    </div>

     <div class="row mb-2">
        <div class="col-12">
            <form>
            <select style="height: 26px;" name="payment_vendor" onchange="return window.location.href = '?payment_vendor='+this.value+'#payments'"> 
              <option value="">Select Vendor</option>
              @foreach($vendors as $vendor)
                 <option value="{{ $vendor->id }}" {{ (@request()->payment_vendor == $vendor->id) ? 'selected' : ''}}> {{ $vendor->name }}</option>
              @endforeach
            </select> 
           
            <select style="height: 26px;" name="payment_trade" onchange="return window.location.href = '?payment_trade='+this.value+'#payments'"> 
              <option value="">Select Trade</option>
              @foreach($paymentTrades as $trade)
                 <option value="{{ $trade->id }}" {{ (@request()->payment_trade == $trade->id) ? 'selected' : ''}}> {{ $trade->name }}</option>
              @endforeach
            </select>
            <select style="height: 26px;"  name="payment_status" onchange="return window.location.href = '?payment_status='+this.value+'#payments'"> 
              <option value="">Select Status</option>
              <option value="{{\App\Models\Payment::DEPOSIT_PAID_STATUS }}" {{ @request()->payment_status == \App\Models\Payment::DEPOSIT_PAID_STATUS ? 'selected' : ''}}>{{\App\Models\Payment::DEPOSIT_PAID_TEXT  }}</option>
              <option value="{{ \App\Models\Payment::PROGRESS_PAYMENT_STATUS }}" {{ @request()->payment_status == \App\Models\Payment::PROGRESS_PAYMENT_STATUS ? 'selected' : ''}}>{{\App\Models\Payment::PROGRESS_PAYMENT_TEXT  }}</option>
              <option value="{{ \App\Models\Payment::RETAINAGE_PAID_STATUS }}" {{ @request()->payment_status == \App\Models\Payment::RETAINAGE_PAID_STATUS ? 'selected' : ''}}>{{\App\Models\Payment::RETAINAGE_PAID_TEXT  }}</option>
              <option value="{{ \App\Models\Payment::FINAL_PAYMENT_STATUS }}" {{ @request()->payment_status == \App\Models\Payment::FINAL_PAYMENT_STATUS ? 'selected' : ''}}>{{\App\Models\Payment::FINAL_PAYMENT_TEXT  }}</option>
            </select>
          </form>
        </div>
    </div>


 <div class="table-responsive table-payments">
       <div class="row">
            <div class="col-8"></div>
            <div class="col-4 text-right" >
              <p> <b>ULR</b>  - Unconditional Lien Release</br>
               <b>CLR</b>  - Conditional Lien Release</p>
            </div>
       </div>
       <table id="project-types-table" class="table table-hover text-center payments-table">
            <thead>
            <tr class="text-danger">
                <th >Date 
                  <span class="sorting-outer">
                  <a href="javascript:void(0)" onclick="sortOrderBy('date', 'ASC')"><i class="fa fa-sort-asc" o ></i></a>
                  <a href="javascript:void(0)" onclick="sortOrderBy('date', 'DESC')"><i class="fa fa-sort-desc"></i> </a>
                </span></th>

                <th >Inserted 
                  <span class="sorting-outer">
                  <a href="javascript:void(0)" onclick="sortOrderBy('created_at', 'ASC')"><i class="fa fa-sort-asc" o ></i></a>
                  <a href="javascript:void(0)" onclick="sortOrderBy('created_at', 'DESC')"><i class="fa fa-sort-desc"></i> </a>
                </span></th>
                <th >Invoice Number <span class="sorting-outer">
                  <a href="javascript:void(0)" onclick="sortOrderBy('invoice_number', 'ASC')">
                    <i class="fa fa-sort-asc" o ></i></a>
                  <a href="javascript:void(0)" onclick="sortOrderBy('invoice_number', 'DESC')">
                    <i class="fa fa-sort-desc"></i> </a>
                </span></th>

                <th>Trade</th>
                <th>Subcontractor/Vendor</th>
                <th>Amount Paid</th>
                <th>Contract Amount </th>
                <th>Remaining Amount </th>
                <th>Invoice</th>
                <th>ULR </th>
                <th>CLR </th>
                <th>Status</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
            </thead>
            <tbody>
              @foreach($payments as $payment)

               @if($payment->file)
               @php
                 $fileInfo = pathinfo($payment->file);
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
               @endif
               
               @if($payment->unconditional_lien_release_file)
               @php
                 $fileInfo = pathinfo($payment->unconditional_lien_release_file);
                 $extension2 = @$fileInfo['extension'];
                
              if(in_array($extension2,['doc','docx','docm','dot',
              'dotm','dotx'])){
                  $extension2 = 'word'; 
               }
               else if(in_array($extension2,['csv','dbf','dif','xla',
              'xls','xlsb','xlsm','xlsx','xlt','xltm','xltx'])){
                  $extension2 = 'excel'; 
               }
               @endphp
                @endif

               @if($payment->conditional_lien_release_file)
               @php
                 $fileInfo = pathinfo($payment->conditional_lien_release_file);
                 $extension3 = @$fileInfo['extension'];
                
              if(in_array($extension3,['doc','docx','docm','dot',
              'dotm','dotx'])){
                  $extension3 = 'word'; 
               }
               else if(in_array($extension3,['csv','dbf','dif','xla',
              'xls','xlsb','xlsm','xlsx','xlt','xltm','xltx'])){
                  $extension3 = 'excel'; 
               }
               @endphp
             @endif
             <tr>
               <td> {{ @$payment->date }}</td>
               <td> {{ @$payment->updated_at }}</td>
               <td> {{ @$payment->invoice_number }}</td>
               <td> {{ @$payment->trade->name }}</td>
               <td> {{ (@$payment->vendor ) ? @$payment->vendor->name. 
                ' (Vendor) '. ( (@$payment->material) ? '('.@$payment->material->name .')' : "" ) :  @$payment->subcontractor->name }}</td>
               <td> ${{ \App\Models\Payment::format($payment->payment_amount) }}</td>

               <td> {{ (@!$payment->ffe_proposal_id ) ? '-' :  '$'.\App\Models\Payment::format($payment->total_amount) }}</td>

               <td>  {{ (@!$payment->ffe_proposal_id ) ? '-' :  '$'.\App\Models\Payment::format($payment->remaining) }} </td>
               <td>
                @if($payment->file)
                <a href="{{ asset($payment->file) }}" target="_blank">
              <img class="avatar border-gray" src="{{ asset('img/'.@$extension.'.png') }}">
              </a> 
              @else
                -
              @endif
            </td>  
            <td>
                @if($payment->unconditional_lien_release_file)
                <a href="{{ asset($payment->unconditional_lien_release_file) }}" target="_blank">
              <img class="avatar border-gray" src="{{ asset('img/'.@$extension2.'.png') }}">
              </a> 
              @else
                -
              @endif
            </td>  
            <td>
                @if($payment->conditional_lien_release_file)
                <a href="{{ asset($payment->conditional_lien_release_file) }}" target="_blank">
              <img class="avatar border-gray" src="{{ asset('img/'.@$extension3.'.png') }}">
              </a> 
              @else
                -
              @endif
            </td>
               <td>{{ @\App\Models\Payment::$statusArr[$payment->status] }}</td>
                  <td>        
                    <button onclick="return window.location.href='ffe/payments/{{$payment->id}}'" rel="tooltip" class="btn btn-neutral bg-transparent btn-icon" data-original-title="Edit Project Type" title="Edit Project Type">            <i class="fa fa-edit text-success"></i>        
                    </button> 
                  </td>
              <td>
                 <form 
                  method="post" 
                  action="{{route('projects.ffe.payments.destroy',[ 'project' => request()->project ,
                  'id' => $payment->id]).'#payments'}}"> 
                   @csrf
                  {{ method_field('DELETE') }}

                  <button 
                    type="submit"
                    onclick="return confirm('Are you sure?')"
                    class="btn btn-neutral bg-transparent btn-icon" data-original-title="Delete Trade" title="Delete Bussiness Type"><i class="fa fa-trash text-danger"></i> </button>
                </form>
               </td>
             </tr> 
             @endforeach
            <!-- Project Types Go Here -->
            </tbody>
        </table>

</div>
</div>



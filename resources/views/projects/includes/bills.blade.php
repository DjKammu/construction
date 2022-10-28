<div class="tab-pane" id="bills" role="tabpanel" aria-expanded="true">
   <div class="row mb-2">
         <div class="col-6">
            <h4 class="mt-0 text-left">{{ @$project->name }} - Bills List </h4>
        </div>
        <div class="col-6 text-right">
            <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='{{ route("projects.bills",['id' => request()->project ])  }}'">Add Bill
            </button>
        </div>

    </div>

     <div class="row mb-2">
        <div class="col-12">
            <form>
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
                <!-- <th>Remaining Amount </th> -->
                <th>Invoice</th>
                <th>Status</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
            </thead>
            <tbody>
              @foreach($bills as $bill)

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
               @endif
               

             <tr>
               <td> {{ @$bill->date }}</td>
               <td> {{ @$bill->updated_at }}</td>
               <td> {{ @$bill->invoice_number }}</td>
               <td> {{ @$bill->trade->name }}</td>
               <td> {{ (@$bill->vendor ) ? @$bill->vendor->name. 
                ' (Vendor) '. ( (@$bill->material) ? '('.@$bill->material->name .')' : "" ) :  @$bill->subcontractor->name }}</td>
               <td> ${{ \App\Models\Payment::format($bill->payment_amount) }}</td>

               <td> {{ (@$bill->vendor ) ? '-' :  '$'.\App\Models\Payment::format($bill->total_amount) }}</td>
               <!-- <td>  {{ (@$bill->vendor ) ? '-' :  '$'.\App\Models\Payment::format($bill->remaining) }} </td> -->
               <td>
                @if($bill->file)
                <a href="{{ asset($bill->file) }}" target="_blank">
              <img class="avatar border-gray" src="{{ asset('img/'.@$extension.'.png') }}">
              </a> 
              @else
                -
              @endif
            </td>  
            </td>
               <td>{{ @\App\Models\Payment::$statusArr[$bill->status] }}</td>
                  <td>        
                    <button onclick="return window.location.href='bills/{{$bill->id}}'" rel="tooltip" class="btn btn-neutral bg-transparent btn-icon" data-original-title="Edit Project Type" title="Edit Project Type">            <i class="fa fa-edit text-success"></i>        
                    </button> 
                  </td>
              <td>
                 <form 
                  method="post" 
                  action="{{route('projects.bills.destroy',['id' => $bill->id]).'#bills'}}"> 
                   @csrf
                  {{ method_field('DELETE') }}

                  <button 
                    type="submit"
                    onclick="return confirm('Are you sure?')"
                    class="btn btn-neutral bg-transparent btn-icon" data-original-title="Delete Bill" title="Delete Bill"><i class="fa fa-trash text-danger"></i> </button>
                </form>
               </td>
             </tr> 
             @endforeach
            <!-- Project Types Go Here -->
            </tbody>
        </table>

</div>
</div>



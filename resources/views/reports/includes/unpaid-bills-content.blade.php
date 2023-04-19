<div class="table-responsive table-payments">
       
       <table id="project-types-table" class="table table-hover text-center payments-table">
            <thead>
            <tr class="text-danger">
                <th>Date </th>

                <th>Inserted 
                  </th>
                <th >Invoice Number </th>

                <th>Trade</th>
                <th>Subcontractor/Vendor</th>
                <th>Amount Paid</th>
                <th>Contract Amount </th>
                <!-- <th>Remaining Amount </th> -->
                <th>Invoice</th>
                <th>Status</th>
                <th>Mark as Paid</th>
                <th>Edit</th>
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
               <td>{{ @\App\Models\Payment::$statusArr[$bill->status] }} </td>
               <td><input type="checkbox" 
                {{ $bill->bill_status == \App\Models\Bill::PAID_BILL_STATUS ? 'checked' : ''}} 
                 name="bill_status" 
                onclick="return window.location.href='projects/bills/{{ $bill->id }}/bill-stattus?bill_status='+this.checked+'&url={{ urlencode(url()->full().'#unpaid-bills')}}'">
                <label class="text-dark" for="password">
                  <b>Mark as Paid</b>
                </label></td>
                  <td>        
                    <button onclick="return window.location.href='projects/bills/{{$bill->id}}?to=Reports&url={{ urlencode(url()->full().'#unpaid-bills')}}'" rel="tooltip" class="btn btn-neutral bg-transparent btn-icon" data-original-title="Edit Project Type" title="Edit Project Type"> <i class="fa fa-edit text-success"></i>        
                    </button> 
                  </td>
             </tr> 
             @endforeach
            <!-- Project Types Go Here -->
            </tbody>
        </table>
</div>



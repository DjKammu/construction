<div class="tab-pane" id="payments" role="tabpanel" aria-expanded="true">
   <div class="row mb-2">
         <div class="col-6">
            <h4 class="mt-0 text-left">{{ @$project->name }} - Payments List </h4>
        </div>
        <div class="col-6 text-right">
            <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='{{ route("projects.payments",['id' => request()->project ])  }}'">Add Payment
            </button>
        </div>

    </div>


 <div class="table-responsive">

       <table id="project-types-table" class="table table-hover text-center">
            <thead>
            <tr class="text-danger">
                <th>Trade</th>
                <th>Subcontractor</th>
                <th>Amount Paid</th>
                <th>Contract Amount </th>
                <th>Remaining Amount </th>
                <th>Status</th>
                <th>Icon</th>
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
             <tr>
               <td> {{ @$payment->trade->name }}</td>
               <td> {{ @$payment->subcontractor->name }}</td>
               <td> ${{ $payment->payment_amount }}</td>
               <td>${{ $payment->total_amount }}</td>
               <td>${{ $payment->total_amount - $payment->payment_amount  }}</td>
               <td><a href="{{ asset($payment->file) }}" target="_blank">
              <p> {{ @$file->name }} </p>
              <img class="avatar border-gray" src="{{ asset('img/'.@$extension.'.png') }}">
              </a> </td>
               <td>{{ @\App\Models\Payment::$statusArr[$payment->status] }}</td>
                  <td>        
                    <button onclick="return window.location.href='payments/{{$payment->id}}'" rel="tooltip" class="btn btn-neutral bg-transparent btn-icon" data-original-title="Edit Project Type" title="Edit Project Type">            <i class="fa fa-edit text-success"></i>        
                    </button> 
                  </td>
              <td>
                 <form 
                  method="post" 
                  action="{{route('projects.payments.destroy',['id' => $payment->id]).'#payments'}}"> 
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



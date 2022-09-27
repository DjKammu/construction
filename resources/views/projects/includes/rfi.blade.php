<div class="tab-pane" id="rfi" role="tabpanel" aria-expanded="true">
   <div class="row mb-2">
         <div class="col-6">
            <h4 class="mt-0 text-left">{{ @$project->name }} - RFI List </h4>
        </div>
        <div class="col-6 text-right">
            <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='{{ route("projects.rfi",['id' => request()->project ])  }}'">Add RFI
            </button>
        </div>

    </div>

     <div class="row mb-2">
        <div class="col-12">
            <form>
         
            <select style="height: 26px;" name="rfi_subcontractor" onchange="return window.location.href = '?rfi_subcontractor='+this.value+'#rfi'"> 
              <option value="">Select Subcontractor</option>
              @foreach($paymentSubcontractors as $subcontractor)
                 <option value="{{ $subcontractor->id }}" {{ (@request()->rfi_subcontractor == $subcontractor->id) ? 'selected' : ''}}> {{ $subcontractor->name }}</option>
              @endforeach
            </select>
           
            <select style="height: 26px;"  name="rfi_status" onchange="return window.location.href = '?rfi_status='+this.value+'#rfi'"> 
              <option value="">Select Status</option>
               @foreach($statuses as $status)
                 <option value="{{ $status->id }}" {{ (@request()->rfi_status == $status->id) ? 'selected' : ''}}> {{ $status->name }}</option>
              @endforeach
            </select>
            <input type="text" name="daterange" value="" />
          </form>
        </div>
    </div>


 <div class="table-responsive table-payments">
    
       <table id="project-types-table" class="table table-hover text-center payments-table">
            <thead>
            <tr class="text-danger">
            
                <th >Number <span class="sorting-outer">
                  <a href="javascript:void(0)" onclick="sortOrderByRFI('number', 'ASC')">
                    <i class="fa fa-sort-asc"></i></a>
                  <a href="javascript:void(0)" onclick="sortOrderByRFI('number', 'DESC')">
                    <i class="fa fa-sort-desc"></i> </a>
                </span></th>

                <th>Name</th>
                <th>Created By</th>
                <th>Date Sent <span class="sorting-outer">
                  <a href="javascript:void(0)" onclick="sortOrderByRFI('date_sent', 'ASC')">
                    <i class="fa fa-sort-asc"></i></a>
                  <a href="javascript:void(0)" onclick="sortOrderByRFI('date_sent', 'DESC')">
                    <i class="fa fa-sort-desc"></i> </a>
                </span> </th>
                <th>Date Recieved <span class="sorting-outer">
                  <a href="javascript:void(0)" onclick="sortOrderByRFI('date_recieved', 'ASC')">
                    <i class="fa fa-sort-asc"></i></a>
                  <a href="javascript:void(0)" onclick="sortOrderByRFI('date_recieved', 'DESC')">
                    <i class="fa fa-sort-desc"></i> </a>
                </span> </th>
                <th>Assign To</th>
                <th>Sent File </th>
                <th>Recieved File </th>
                <th>Status</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
            </thead>
            <tbody>
              @foreach($rfis as $rfi)

               @if($rfi->sent_file)
               @php
                 $fileInfo = pathinfo($rfi->sent_file);
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
               
               @if($rfi->recieved_file)
               @php
                 $fileInfo = pathinfo($rfi->recieved_file);
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

             <tr>
               <td> {{ @$rfi->number }}</td>
               <td> {{ @$rfi->name }}</td>
               <td> {{ @$rfi->user->name }}</td>
               <td> {{ @$rfi->date_sent }}</td>
               <td> {{ @$rfi->date_recieved }}</td>
               <td> {{ @$rfi->assign->name }}</td>
               
               <td>
                @if($rfi->sent_file)
                  <a href="{{ asset($rfi->sent_file) }}" target="_blank">
                <img class="avatar border-gray" src="{{ asset('img/'.@$extension.'.png') }}">
                </a> 
                @else
                  -
                @endif
            </td>  
            <td>
                @if($rfi->recieved_file)
                <a href="{{ asset($rfi->recieved_file) }}" target="_blank">
              <img class="avatar border-gray" src="{{ asset('img/'.@$extension2.'.png') }}">
              </a> 
              @else
                -
              @endif
            </td>  
            </td>
               <td>{{@$rfi->status->name }}</td>
                  <td>        
                    <button onclick="return window.location.href='rfi/{{$rfi->id}}'" rel="tooltip" class="btn btn-neutral bg-transparent btn-icon" data-original-title="Edit Project Type" title="Edit Project Type">            <i class="fa fa-edit text-success"></i>        
                    </button> 
                  </td>
              <td>
                 <form 
                  method="post" 
                  action="{{route('projects.rfi.destroy',['id' => $rfi->id]).'#rfi'}}"> 
                   @csrf
                  {{ method_field('DELETE') }}
                  <button 
                    type="submit"
                    onclick="return confirm('Are you sure?')"
                    class="btn btn-neutral bg-transparent btn-icon" data-original-title="Delete RFI" title="Delete RFI"><i class="fa fa-trash text-danger"></i> </button>
                </form>
               </td>
             </tr> 
             @endforeach
            <!-- Project Types Go Here -->
            </tbody>
        </table>

</div>
</div>



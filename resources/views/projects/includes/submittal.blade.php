<div class="tab-pane" id="submittal" role="tabpanel" aria-expanded="true">
   <div class="row mb-2">
         <div class="col-6">
            <h4 class="mt-0 text-left">{{ @$project->name }} - Submittal List </h4>
        </div>
        <div class="col-6 text-right">
            <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='{{ route("projects.submittal",['id' => request()->project ])  }}'">Add Submittal
            </button>
        </div>

    </div>

     <div class="row mb-2">
        <div class="col-12">
            <form>
         
            <select style="height: 26px;" name="submittal_subcontractor" onchange="return window.location.href = '?submittal_subcontractor='+this.value+'#submittal'"> 
              <option value="">Select Subcontractor</option>
              @foreach($paymentSubcontractors as $subcontractor)
                 <option value="{{ $subcontractor->id }}" {{ (@request()->submittal_subcontractor == $subcontractor->id) ? 'selected' : ''}}> {{ $subcontractor->name }}</option>
              @endforeach
            </select>
           
            <select style="height: 26px;"  name="submittal_status" onchange="return window.location.href = '?submittal_status='+this.value+'#submittal'"> 
              <option value="">Select Status</option>
               @foreach($statuses as $status)
                 <option value="{{ $status->id }}" {{ (@request()->submittal_status == $status->id) ? 'selected' : ''}}> {{ $status->name }}</option>
              @endforeach
            </select>
            <input type="text" name="daterange-submittal" value="" />
          </form>
        </div>
    </div>


 <div class="table-responsive table-payments">
    
       <table id="project-types-table" class="table table-hover text-center payments-table">
            <thead>
            <tr class="text-danger">
            
                <th >Number <span class="sorting-outer">
                  <a href="javascript:void(0)" onclick="sortOrderBySubmittal('number', 'ASC')">
                    <i class="fa fa-sort-asc"></i></a>
                  <a href="javascript:void(0)" onclick="sortOrderBySubmittal('number', 'DESC')">
                    <i class="fa fa-sort-desc"></i> </a>
                </span></th>

                <th>Name</th>
                <th>Created By</th>
                <th>Date Sent <span class="sorting-outer">
                  <a href="javascript:void(0)" onclick="sortOrderBySubmittal('date_sent', 'ASC')">
                    <i class="fa fa-sort-asc"></i></a>
                  <a href="javascript:void(0)" onclick="sortOrderBySubmittal('date_sent', 'DESC')">
                    <i class="fa fa-sort-desc"></i> </a>
                </span> </th>
                <th>Date Recieved <span class="sorting-outer">
                  <a href="javascript:void(0)" onclick="sortOrderBySubmittal('date_recieved', 'ASC')">
                    <i class="fa fa-sort-asc"></i></a>
                  <a href="javascript:void(0)" onclick="sortOrderBySubmittal('date_recieved', 'DESC')">
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
              @foreach($submittals as $submittal)

               @if($submittal->sent_file)
               @php
                 $fileInfo = pathinfo($submittal->sent_file);
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
               
               @if($submittal->recieved_file)
               @php
                 $fileInfo = pathinfo($submittal->recieved_file);
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
               <td> {{ @$submittal->number }}</td>
               <td> {{ @$submittal->name }}</td>
               <td> {{ @$submittal->user->name }}</td>
               <td> {{ @$submittal->date_sent }}</td>
               <td> {{ @$submittal->date_recieved }}</td>
               <td> {{ @$submittal->assign->name }}</td>
               
               <td>
                @if($submittal->sent_file)
                  <a href="{{ asset($submittal->sent_file) }}" target="_blank">
                <img class="avatar border-gray" src="{{ asset('img/'.@$extension.'.png') }}">
                </a> 
                @else
                  -
                @endif
            </td>  
            <td>
                @if($submittal->recieved_file)
                <a href="{{ asset($submittal->recieved_file) }}" target="_blank">
              <img class="avatar border-gray" src="{{ asset('img/'.@$extension2.'.png') }}">
              </a> 
              @else
                -
              @endif
            </td>  
            </td>
               <td>{{@$submittal->status->name }}</td>
                  <td>        
                    <button onclick="return window.location.href='submittal/{{$submittal->id}}'" rel="tooltip" class="btn btn-neutral bg-transparent btn-icon" data-original-title="Edit Project Type" title="Edit Project Type">            <i class="fa fa-edit text-success"></i>        
                    </button> 
                  </td>
              <td>
                 <form 
                  method="post" 
                  action="{{route('projects.submittal.destroy',['id' => $submittal->id]).'#submittal'}}"> 
                   @csrf
                  {{ method_field('DELETE') }}
                  <button 
                    type="submit"
                    onclick="return confirm('Are you sure?')"
                    class="btn btn-neutral bg-transparent btn-icon" data-original-title="Delete Submittal" title="Delete Submittal"><i class="fa fa-trash text-danger"></i> </button>
                </form>
               </td>
             </tr> 
             @endforeach
            <!-- Project Types Go Here -->
            </tbody>
        </table>

</div>
</div>



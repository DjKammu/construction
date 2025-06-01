<div class="tab-pane" id="inspection" role="tabpanel" aria-expanded="true">
   <div class="row mb-2">
         <div class="col-6">
            <h4 class="mt-0 text-left">{{ @$project->name }} - Inspection List </h4>
        </div>
        <div class="col-6 text-right">
            <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='{{ route("projects.inspection",['id' => request()->project ])  }}'">Add Inspection
            </button>
        </div>

    </div>

     <div class="row mb-2">
       <!--  <div class="col-12">
            <form>
         
            <select style="height: 26px;" name="submittal_subcontractor" onchange="return window.location.href = '?submittal_subcontractor='+this.value+'#submittal'"> 
              <option value="">Select Subcontractor</option>
              @foreach($paymentSubcontractors as $subcontractor)
                 <option value="{{ $subcontractor->id }}" {{ (@request()->submittal_subcontractor == $subcontractor->id) ? 'selected' : ''}}> {{ $subcontractor->name }}</option>
              @endforeach
            </select>
           
            <select style="height: 26px;"  name="submittal_status" onchange="return window.location.href = '?submittal_status='+this.value+'#submittal'"> 
              <option value="">Select Status</option>
               @foreach($rfi_statuses as $status)
                 <option value="{{ $status->id }}" {{ (@request()->submittal_status == $status->id) ? 'selected' : ''}}> {{ $status->name }}</option>
              @endforeach
            </select>
            <input type="text" name="daterange-submittal" value="" />
          </form>
        </div> -->
    </div>


 <div class="table-responsive table-payments">
    
       <table id="project-types-table" class="table table-hover text-center payments-table">
            <thead>
            <tr class="text-danger">
                <th>Date <span class="sorting-outer">
                  <a href="javascript:void(0)" onclick="sortOrderByInspection('date', 'ASC')">
                    <i class="fa fa-sort-asc"></i></a>
                  <a href="javascript:void(0)" onclick="sortOrderByInspection('date', 'DESC')">
                    <i class="fa fa-sort-desc"></i> </a>
                </span> </th>
               
                <th>Inspection Category</th>
                <th>Inspection Type</th>
                <th>Files </th>
                <th>Passed/Failed</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
            </thead>
            <tbody>
              @foreach($inspections as $inspection)

             <tr>
               <td> {{ @$inspection->date }}</td>
               <td> {{ @$inspection->inspection_category->name }}</td>
               <td> {{ @$inspection->inspection_type->name }}</td>
               <td>
                @if(!empty($inspection->files))
                 @foreach(@explode(',',$inspection->files) as $file)
   
                        @php
                           $fileInfo = pathinfo($file); 
                             $extension = @$fileInfo['extension'];
                          
                              if(in_array($extension,['doc','docx','docm','dot',
                            'dotm','dotx'])){
                                $extension = 'word'; 
                             }
                             else if(in_array($extension,['csv','dbf','dif','xla',
                            'xls','xlsb','xlsm','xlsx','xlt','xltm','xltx'])){
                                $extension = 'excel'; 
                             }
                           
                            if(!$extension){
                              $extension = 'pdf';
                            }

                        @endphp
                        <a href="{{ url($file) }}" target="_blank">
                      <img class="avatar border-gray proposal_file" 
                      src="{{ asset('img/'.$extension.'.png') }}">
                      </a>

                 @endforeach

                 @endif
            </td>  
            
               <td>{{ @$inspection->passed ? App\Models\Inspection::PASSED : App\Models\Inspection::FAILED }}</td>
                  <td>  
                    <button onclick="return window.location.href='inspection/{{$inspection->id}}'" rel="tooltip" class="btn btn-neutral bg-transparent btn-icon" data-original-title="Edit Project Type" title="Edit Project Type">            
                      <i class="fa fa-edit text-success"></i>        
                    </button> 
                  </td>
              <td>
                 <form 
                  method="post" 
                  action="{{route('projects.inspection.destroy',['id' => $inspection->id]).'#inspection'}}"> 
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



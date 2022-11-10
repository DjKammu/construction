<div class="tab-pane" id="logs" role="tabpanel" aria-expanded="true">
   <div class="row mb-2">
         <div class="col-6">
            <h4 class="mt-0 text-left">{{ @$project->name }} - FFE Procurement Log </h4>
        </div>
        <div class="col-6 text-right">
            <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='{{ route("projects.ffe.logs",['project' => request()->project ])  }}'">Add Procurement Log
            </button>
        </div>
        <div class="col-6"> 
         <form>
            <select style="height: 26px;" name="log_vendor" onchange="return window.location.href = '?log_vendor='+this.value+'#logs'"> 
              <option value="">Select Vendor</option>
              @foreach($vendors as $vendor)
                 <option value="{{ $vendor->id }}" {{ (@request()->log_vendor == $vendor->id) ? 'selected' : ''}}> {{ $vendor->name }}</option>
              @endforeach
            </select> 
           
            <!-- <select style="height: 26px;" name="log_trade" onchange="return window.location.href = '?log_trade='+this.value+'#logs'"> 
              <option value="">Select Trade</option>
              @foreach($paymentTrades as $trade)
                 <option value="{{ $trade->id }}" {{ (@request()->log_trade == $trade->id) ? 'selected' : ''}}> {{ $trade->name }}</option>
              @endforeach
            </select> -->
            <select style="height: 26px;"  name="log_status" onchange="return window.location.href = '?log_status='+this.value+'#logs'"> 
              <option value="">Select Status</option>
              @foreach($paymentStatuses as $status)
                 <option value="{{ $status->id }}" {{ (@$log->log_status == $status->id) ? 'selected' : ''}}> {{ $status->name }}</option>
              @endforeach
            </select>
          </form>
        </div>
        <div class="col-6 text-right">
           <button type="button" class="btn btn-danger mt-0"  onclick="sendEmailPopup()">
              Send Email
            </button>

            <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='ffe/{{ @$project->id }}/download'">Download
            </button>
            <input type="hidden" id="file" value="{{ route('projects.download',@$project->id ) .'?v=1' }}">
        </div>

    </div>

     <div class="row mb-2">
        <div class="col-12">
           
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

                <th >Item 
                  <span class="sorting-outer">
                  <a href="javascript:void(0)" onclick="sortOrderByLog('item', 'ASC')"><i class="fa fa-sort-asc" o ></i></a>
                  <a href="javascript:void(0)" onclick="sortOrderByLog('item', 'DESC')"><i class="fa fa-sort-desc"></i> </a>
                </span></th>
                <th >PO Sent <span class="sorting-outer">
                  <a href="javascript:void(0)" onclick="sortOrderByLog('po_sent', 'ASC')">
                    <i class="fa fa-sort-asc" o ></i></a>
                  <a href="javascript:void(0)" onclick="sortOrderByLog('po_sent', 'DESC')">
                    <i class="fa fa-sort-desc"></i> </a>
                </span></th> 
                <th >Date Shipped <span class="sorting-outer">
                  <a href="javascript:void(0)" onclick="sortOrderByLog('date_shipped', 'ASC')">
                    <i class="fa fa-sort-asc" o ></i></a>
                  <a href="javascript:void(0)" onclick="sortOrderByLog('date_shipped', 'DESC')">
                    <i class="fa fa-sort-desc"></i> </a>
                </span></th>
                 <th >Date Received <span class="sorting-outer">
                  <a href="javascript:void(0)" onclick="sortOrderByLog('date_received', 'ASC')">
                    <i class="fa fa-sort-asc" o ></i></a>
                  <a href="javascript:void(0)" onclick="sortOrderByLog('date_received', 'DESC')">
                    <i class="fa fa-sort-desc"></i> </a>
                </span></th>

                <th>Vendor</th>
                <!-- <th>Subcontractor</th> -->
                <th>Lead Time</th>
                <th>Tentative Date Delivery</th>
                <!-- <th>Store Place</th> -->
                <th>Received Shipment Attachment</th>
                <th>Status</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
            </thead>
            <tbody>
              @foreach($logs as $log)

             <tr>
               <td> {{ @$log->date }}</td>
               <td> {{ @$log->item }}</td>
               <td> {{ @$log->po_sent }}</td>
               <td> {{ @$log->date_shipped }}</td>
               <td> {{ @$log->date_received }}</td>
               <td> {{ @$log->vendor->name }}</td>
               <td> {{ @$log->lead_time }}</td>
               <td> {{ @$log->tentative_date_delivery }}</td>
               <td>
                @if(!empty($log->received_shipment_attachment))
                 @foreach(@explode(',',$log->received_shipment_attachment) as $file)
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
            <td>{{ @$log->status->name }}</td>
            <td>        
                    <button onclick="return window.location.href='ffe/logs/{{$log->id}}'" rel="tooltip" class="btn btn-neutral bg-transparent btn-icon" data-original-title="Edit Project Type" title="Edit Project Type">            <i class="fa fa-edit text-success"></i>        
                    </button> 
                  </td>
              <td>
                 <form 
                  method="post" 
                  action="{{route('projects.ffe.logs.destroy',[ 'project' => request()->project ,
                  'id' => $log->id]).'#logs'}}"> 
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



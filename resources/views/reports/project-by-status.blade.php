 <!-- Category Details -->
<div class="tab-pane" id="project-by-status" role="tabpanel" 
aria-expanded="true">

<div class="row mb-2">
    <div class="col-9">
      <select class="st" id="status"  style="height: 26px;" onchange="return window.location.href = '?st='+this.value+'&t=project-by-status#project-by-status'"> 
       <option value="">Select Status</option>
       @foreach($statuses as $status)
           <option value="{{ $status->id }}" {{ (@request()->st == $status->id) ? 'selected' : ''}}> {{ $status->name }}</option>
       @endforeach
      </select>
       
      <select class="pr" id="property" style="height: 26px;" onchange="selectPropertyByStatus(this.value+'&t=project-by-status#project-by-status','st')"> 
       <option value="">Select Property</option>
       @foreach($propertyTypes as $type)
           <option value="{{ $type->id }}" {{ (@request()->pr == $type->id) ? 'selected' : ''}}> {{ $type->name }}</option>
       @endforeach
      </select>

      <select id="project" style="height: 26px;" onchange="selectProjectByStatus(this.value+'&t=project-by-status#project-by-status','st','pr')"> 
      <option value="">Select Project</option>
      @foreach($projects as $pr)
         <option value="{{ $pr->id }}" {{ (@request()->p == $pr->id) ? 'selected' : ''}}> {{ $pr->name }}</option>
      @endforeach
      </select> 

      <select id="manage-by" style="height: 26px;" onchange="selectByStatus('&t=project-by-status#project-by-status')"> 
      <option value="">Select Managed By</option>
      @foreach($users as $pr)
         <option value="{{ $pr->id }}" {{ (@request()->u == $pr->id) ? 'selected' : ''}}> {{ $pr->name }}</option>
      @endforeach
      </select> 

      <select id="property-group" style="height: 26px;" onchange="selectByStatus('&t=project-by-status#project-by-status')"> 
      <option value="">Select Property Group</option>
      @foreach($propertyGroups as $pr)
         <option value="{{ $pr->id }}" {{ (@request()->pg == $pr->id) ? 'selected' : ''}}> {{ $pr->name }}</option>
      @endforeach
      </select>

     

    </div>
    @if((@request()->st || @request()->pr || @request()->p || @request()->u || @request()->pg ) && @request()->t == 'project-by-status' )
       
       </br> 
       </br> 
        <div class="col-6" >
            <h4 class="mt-0 text-left"> Projects By Status</h4>
        </div>
     
        <div class="col-6 text-right">
        <!-- <button type="button" class="btn btn-danger mt-0"  onclick="sendEmailPopup()">
          Send Email
        </button> -->

          <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='reports/0/project-by-status?{{ http_build_query(request()->query())}}'" rel="tooltip" data-original-title="Project By Status" title="Project By Status">Download
          </button>
         </div>
        
        @include('reports.includes.project-by-status-content')

    @else
   <div class="col-12">
   </br>
   <h5>No Project Selected </h5>
   </div>
  @endif

    

</div>
</div>
 <!-- Category Details -->
<div class="tab-pane" id="subcontractor-payment" role="tabpanel" 
aria-expanded="true">

<div class="row mb-2">
    <div class="col-9">
      <select class="pt2" style="height: 26px;" onchange="return window.location.href = '?pt='+this.value+'#subcontractor-payment'"> 
       <option value="">Select Project Type</option>
       @foreach($projectTypes as $type)
           <option value="{{ $type->slug }}" {{ (@request()->pt == $type->slug) ? 'selected' : ''}}> {{ $type->name }}</option>
       @endforeach
      </select>

       <select style="height: 26px;" onchange="selectProject(this.value+'&t=subcontractor-payment#subcontractor-payment','pt2')" > 
      <option value="">Select Project</option>
      @foreach($projects as $pr)
         <option value="{{ $pr->id }}" {{ (@request()->p == $pr->id) ? 'selected' : ''}}> {{ $pr->name }}</option>
      @endforeach
      </select>
    </div>
    @if(@request()->p && @request()->t == 'subcontractor-payment' )
      
      <div class="col-3 text-right">
        <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='reports/{{$project->id}}/subcontractor-payment'" rel="tooltip" data-original-title="Sub Contractor Payment" title="Sub Contractor Payment">Download
        </button>
    </div>

    @else
   <div class="col-12">
   </br>
   <h5>No Project Selected </h5>
   </div>
  @endif

  
</div>
</div>
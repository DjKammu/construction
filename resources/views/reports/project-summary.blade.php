 <!-- Category Details -->
<div class="tab-pane active" id="details" role="tabpanel" 
aria-expanded="true">

<div class="row mb-2">
    <div class="col-9">
       <select style="height: 26px;" onchange="return window.location.href = '?p='+this.value+'&t=project-summary'"> 
      <option value="">Select Project</option>
      @foreach($projects as $pr)
         <option value="{{ $pr->id }}" {{ (@request()->p == $pr->id) ? 'selected' : ''}}> {{ $pr->name }}</option>
      @endforeach
      </select>
    </div>
    @if(@request()->p && @request()->t == 'project-summary' )
      
      <div class="col-3 text-right">
        <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='reports/{{$project->id}}/project-summary'" rel="tooltip" data-original-title="Sub Contractor Payment" title="Sub Contractor Payment">Download
        </button>
    </div>

      @include('reports.includes.project-summary-content')
    @else
   <div class="col-12">
   </br>
   <h5>No Project Selected </h5>
   </div>
  @endif

    

</div>
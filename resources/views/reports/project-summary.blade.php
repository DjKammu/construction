 <!-- Category Details -->
<div class="tab-pane active" id="project-summary" role="tabpanel" 
aria-expanded="true">

<div class="row mb-2">
    <div class="col-9">
      <select class="pt" style="height: 26px;" onchange="return window.location.href = '?pt='+this.value"> 
       <option value="">Select Project Type</option>
       @foreach($projectTypes as $type)
           <option value="{{ $type->slug }}" {{ (@request()->pt == $type->slug) ? 'selected' : ''}}> {{ $type->name }}</option>
       @endforeach
      </select>

       <select style="height: 26px;" onchange="selectProject(this.value+'&t=project-summary#project-summary','pt')"> 
      <option value="">Select Project</option>
      @foreach($projects as $pr)
         <option value="{{ $pr->id }}" {{ (@request()->p == $pr->id) ? 'selected' : ''}}> {{ $pr->name }}</option>
      @endforeach
      </select>
    </div>
    @if(@request()->p && @request()->t == 'project-summary' )
       

      <div class="col-3 text-right">
        <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='reports/{{$project->id}}/project-summary'" rel="tooltip" data-original-title="Project Summary" title="Project Summary">Download
        </button>
       </div>
      <div class="col-6" >
          <h4 class="mt-0 text-left"> {{ $project->name }} Summary</h4>
      </div>
      @include('reports.includes.project-summary-content')
    @else
   <div class="col-12">
   </br>
   <h5>No Project Selected </h5>
   </div>
  @endif

    

</div>
</div>
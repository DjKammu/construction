 <!-- Category Details -->
<div class="tab-pane" id="awarded-contracts" role="tabpanel" 
aria-expanded="true">

<div class="row mb-2">
    <div class="col-9">
      <select class="pt" style="height: 26px;" onchange="return window.location.href = '?pt='+this.value+'&t=awarded-contracts#awarded-contracts'"> 
       <option value="">Select Project Type</option>
       @foreach($projectTypes as $type)
           <option value="{{ $type->slug }}" {{ (@request()->pt == $type->slug) ? 'selected' : ''}}> {{ $type->name }}</option>
       @endforeach
      </select>
       
      <select class="pr" style="height: 26px;" onchange="selectProperty(this.value+'&t=awarded-contracts#awarded-contracts','pt')"> 
       <option value="">Select Property</option>
       @foreach($propertyTypes as $type)
           <option value="{{ $type->id }}" {{ (@request()->pr == $type->id) ? 'selected' : ''}}> {{ $type->name }}</option>
       @endforeach
      </select>

      <select class="p" style="height: 26px;" onchange="selectProject(this.value+'&t=awarded-contracts#awarded-contracts','pt','pr')"> 
      <option value="">Select Project</option>
      @foreach($projects as $pr)
         <option value="{{ $pr->id }}" {{ (@request()->p == $pr->id) ? 'selected' : ''}}> {{ $pr->name }}</option>
      @endforeach
      </select>

      <select class="st" id="status"  style="height: 26px;" onchange="selectAwardedContracts('st='+this.value+'&t=awarded-contracts#awarded-contracts','pt','pr','p')"> 
       <option value="">Select Status</option>
       @foreach($statuses as $status)
           <option value="{{ $status->id }}" {{ (@request()->st == $status->id) ? 'selected' : ''}}> {{ $status->name }}</option>
       @endforeach
      </select>



     

    </div>
    @if(@request()->p && @request()->t == 'awarded-contracts' )
       
       </br> 
        <div class="col-12" >
            <h4 class="mt-0 text-left"> {{ $project->name }}  Awarded / Pending Contracts</h4>
        </div>
        @include('reports.includes.awarded-contracts-content')
    @else
   <div class="col-12">
   </br>
   <h5>No Project Selected </h5>
   </div>
  @endif

    

</div>
</div>
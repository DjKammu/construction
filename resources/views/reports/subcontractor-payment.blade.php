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
       
      <select class="pr2" style="height: 26px;" onchange="selectProperty(this.value+'&t=subcontractor-payment#subcontractor-payment','pt2')"> 
       <option value="">Select Property</option>
       @foreach($propertyTypes as $type)
           <option value="{{ $type->id }}" {{ (@request()->pr == $type->id) ? 'selected' : ''}}> {{ $type->name }}</option>
       @endforeach
      </select>

       <select class="p" style="height: 26px;" onchange="selectProject(this.value+'&t=subcontractor-payment#subcontractor-payment','pt2','pr2')" > 
      <option value="">Select Project</option>
      @foreach($projects as $pr)
         <option value="{{ $pr->id }}" {{ (@request()->p == $pr->id) ? 'selected' : ''}}> {{ $pr->name }}</option>
      @endforeach
      </select>

      <select style="height: 26px;" onchange="selectSubcontractor(this.value+'&t=subcontractor-payment#subcontractor-payment','pt2','pr2','p')" > 
      <option value="">Select Subcontractor</option>
      @foreach($project_subcontractors as $sc)
         <option value="{{ @$sc->id }}" {{ (@request()->sc == $sc->id) ? 'selected' : ''}}> {{ $sc->name }}</option>
      @endforeach
      </select>

      <select style="height: 26px;" onchange="selectVendor(this.value+'&t=subcontractor-payment#subcontractor-payment','pt2','pr2','p')" > 
      <option value="">Select Vendor</option>
      @foreach($project_vendors as $vendor)
         <option value="{{ $vendor->id }}" {{ (@request()->v == $vendor->id) ? 'selected' : ''}}> {{ $vendor->name }}</option>
      @endforeach
      </select>

    </div>
    @if(@request()->p && ( @request()->sc || @request()->v ) && @request()->t == 'subcontractor-payment' )
    
       <div class="col-8" >
          <h5 class="mt-0 text-left"> {{ $project->name }} {{ (@request()->sc) ? @$subcontractor->name  : @$vendor->name }} Payment Summary</h5>
      </div>

      <div class="col-4 text-right">
       
        <button type="button" class="btn btn-danger mt-0"  onclick="sendEmailPopup()">
          Send Email
        </button>

        <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='reports/{{$project->id}}/subcontractor-payment/{{@request()->sc}}?v={{@request()->v }}'" rel="tooltip" data-original-title="Sub Contractor Payment" title="Sub Contractor Payment">Download
        </button>
    </div>


        @include('reports.includes.subcontractor-payment-content')
   
    @else
   <div class="col-12">
   </br>
   <h5>No Subcontractor/Vendor Selected </h5>
   </div>
  @endif

  
</div>
</div>
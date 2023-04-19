 <!-- Category Details -->
<div class="tab-pane" id="unpaid-bills" role="tabpanel" 
aria-expanded="true">

<div class="row mb-2">
    <div class="col-9">
      <select class="at" id="status"  style="height: 26px;" onchange="return window.location.href = '?at='+this.value+'&t=unpaid-bills#unpaid-bills'"> 
       <option value="">Select Assigned To</option>
       @foreach($users as $user)
           <option value="{{ $user->id }}" {{ (@request()->at == $user->id) ? 'selected' : ''}}> {{ $user->name }}</option>
       @endforeach
      </select>
       
      <select class="ps" id="property" style="height: 26px;" onchange="selectUnbilledStatus('ps='+this.value+'&t=unpaid-bills#unpaid-bills','at')"> 
       <option value="">Select Paid Status</option>
        <option value="{{\App\Models\Bill::PAID_BILL_STATUS }}" {{ @request()->ps == \App\Models\Bill::PAID_BILL_STATUS ? 'selected' : ''}}>
        {{\App\Models\Bill::PAID_BILL_TEXT  }}</option>
        <option value="{{ \App\Models\Bill::UNPAID_BILL_STATUS }}" 
        {{ @request()->ps == \App\Models\Bill::UNPAID_BILL_STATUS ? 'selected' : ''}}>{{\App\Models\Bill::UNPAID_BILL_TEXT  }}</option>
      </select>

      <select class="pt" style="height: 26px;" onchange="selectUnbilledStatus('pt='+this.value+'&t=unpaid-bills#unpaid-bills','at','ps')"> 
      <option value="">Select Project Type</option>
       @foreach($projectTypes as $type)
           <option value="{{ $type->slug }}" {{ (@request()->pt == $type->slug) ? 'selected' : ''}}> {{ $type->name }}</option>
       @endforeach
      </select> 
       
      <select class="pr" style="height: 26px;" onchange="selectUnbilledStatus('pr='+this.value+'&t=unpaid-bills#unpaid-bills','at','ps','pt')"> 
       <option value="">Select Property</option>
       @foreach($propertyTypes as $type)
           <option value="{{ $type->id }}" {{ (@request()->pr == $type->id) ? 'selected' : ''}}> {{ $type->name }}</option>
       @endforeach
      </select>

      <select style="height: 26px;" onchange="selectUnbilledStatus('p='+this.value+'&t=unpaid-bills#unpaid-bills','at','ps','pt','pr')"> 
      <option value="">Select Project</option>
      @foreach($projects as $pr)
         <option value="{{ $pr->id }}" {{ (@request()->p == $pr->id) ? 'selected' : ''}}> {{ $pr->name }}</option>
      @endforeach
      </select>

   

     

    </div>
    @if((@request()->at || @request()->ps || @request()->p || @request()->u || @request()->pg ) && @request()->t == 'unpaid-bills' )
       
       </br> 
       </br> 
        <div class="col-6" >
            <h4 class="mt-0 text-left"> Unpaid Bills</h4>
        </div>
     
        <div class="col-6 text-right">
        <!-- <button type="button" class="btn btn-danger mt-0"  onclick="sendEmailPopup()">
          Send Email
        </button> -->

          <!-- <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='reports/0/project-by-status?{{ http_build_query(request()->query())}}'" rel="tooltip" data-original-title="Project By Status" title="Project By Status">Download
          </button> -->
         </div>
        
        @include('reports.includes.unpaid-bills-content')

    @else
   <div class="col-12">
   </br>
   <h5>No Bills Selected </h5>
   </div>
  @endif

    

</div>
</div>
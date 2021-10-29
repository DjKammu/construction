 <div class="tab-pane" id="bids" role="tabpanel" aria-expanded="true">
   <div class="row mb-2">
        <div class="col-12">
            <h4 class="mt-0 text-left">{{ @$project->name }} - Bid Tabulation List </h4>
        </div>
      

    </div>

<div id="proposals-list" class="row py-3">

	<div class="table-responsive">
    <ul class="list"> 
         @foreach($categories as $cat)

         @php   
         $catTrades = @$trades->where('category_id', $cat->id);
         @endphp

            <li class="text-danger h6 text-center single-line">
               <p><b>{{ $cat->name }}</b></p>
            </li>

         @foreach($catTrades as $trd)

            <li class="multi-line">
                <span></span>
                <span><b>{{ $trd->name  }}</b></span>
              @php
                
                 $subcontractorsBids = \App\Models\Subcontractor::whereHas('trades', function($q) use ($trd){
                      $q->where('trade_id', $trd->id);
                  })->get();
                 
                  $bidCount = @$subcontractorsBids->count();
                  $noBids  = $subcontractorsCount - $bidCount;

              @endphp
             
              @foreach($subcontractorsBids as $subc)
                @php
                  $bid = @$project->proposals()->trade($trd->id)
                           ->where('subcontractor_id', $subc->id)->first();         
                  $bidTotal =  (int) @$bid->material + (int) @$bid->labour_cost + (int) @$bid->subcontractor_price ;       
                @endphp
                <span  class="text-center {{ (@$bid->awarded) ? 'awarded-green' : '' }}">{{ $subc->name }} <br><b> {{ ($bidTotal) ? '$'.$bidTotal 
                  : "No Bid" }} </b></span>
              @endforeach

              @for($i=0; $i < $noBids; $i++)
                <span class="text-center"> <b> No Bid</b> </span>
              @endfor 

             </li>
         @endforeach

        @endforeach

        </ul>
</div>

</div>
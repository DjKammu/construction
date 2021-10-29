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
                <span style="width: 50px;"></span>
                <span style="width: 150px;"><b>{{ $trd->name  }}</b></span>
              @php
                  $bids = @$project->proposals()->trade($trd->id)->get();
                  $bidCount = @$bids->count();
                  $noBids  = $subcontractorsCount - $bidCount;

              @endphp
             
              @foreach($bids as $bid)
                @php    
                  $bidTotal =  (int) @$bid->material + (int) @$bid->labour_cost + (int) @$bid->subcontractor_price ;       
                @endphp
                <span  class="text-center {{ (@$bid->awarded) ? 'awarded-green' : '' }}">{{ $bid->subcontractor->name }} <br><b> {{ ($bidTotal) ? '$'.$bidTotal 
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
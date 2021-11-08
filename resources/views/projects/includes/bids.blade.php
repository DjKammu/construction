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
                <span style="width:5%;"></span>
                <span class="text-center" style="width: 15%;"><b>{{ $trd->name  }}</b></span>
              @php
                  $bids = @$project->proposals()->trade($trd->id)->get();
                  $bidCount = @$bids->count();
                  $noBids  = $subcontractorsCount - $bidCount;
                  $spanWidth = 80/(($bidCount) ? $bidCount : 1) ;
              @endphp
             
              @foreach($bids as $bid)
                @php    
                  $bidTotal =  (int) @$bid->material + (int) @$bid->labour_cost + (int) @$bid->subcontractor_price;   
  
                     foreach(@$bid->changeOrders as $k => $order){
                       if($order->type == \App\Models\ChangeOrder::ADD ){
                         $bidTotal += $order->subcontractor_price;
                       }
                       else{
                         $bidTotal -= $order->subcontractor_price;
                       }

                     }
                @endphp
                <span  style="width:{{$spanWidth}}%;" class="text-center bid-text {{ (@$bid->awarded) ? 'awarded-green' : '' }}">{{ $bid->subcontractor->name }} <br><b> {{ ($bidTotal) ? '$'.\App\Models\Payment::format($bidTotal)
                  : "No Bid" }} </b></span>
              @endforeach

              @for($i=0; $i < $noBids; $i++)
                <span class="text-center bid-text"> <b> No Bid</b> </span>
              @endfor 

             </li>
         @endforeach



        @endforeach

        </ul>
</div>

</div>
</div>
 <div class="tab-pane" id="budget" role="tabpanel" aria-expanded="true">
   <div class="row mb-2">
        <div class="col-6">
            <h4 class="mt-0 text-left">{{ @$project->name }} - Budget </h4>
        </div>
      
         <div class="col-6 text-right">
            <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='{{ @$project->id }}/download'">Download
            </button>
        </div>
    </div>

<div id="proposals-list" class="row py-3">

	<div class="table-responsive">

    <table id="project-types-table" class="table table-hover text-center payments-table">
            <thead>
            <tr class="text-danger">
                <th>Item No.</th>
                <th>Category & Trade</th>
                <th>Material</th>
                <th>Labor</th>
                <th>Subcontractor</th>
                <!-- <th>Vendors</th> -->
                <th>Total </th>
                <th>Total Paid</th>
                <th>Remaining Payment </th>
                <th> % Complete </th>
                <!-- <th> Notes </th> -->
            </tr>
            </thead>
            <tbody>
         @php   
         $materialTotal = 0;
         $labourTotal = 0;
         $subcontractorTotal = 0;
         $grandTotal = 0;
         $paidTotal = 0;
         $dueTotal = 0;
         $vendors = [];
         @endphp

        @foreach($categories as $cat)

         @php   

          $catGrandTotal = 0;
          $catPaidTotal = 0;
          $catDueTotal = 0;

         $catTrades = @$trades->where('category_id', $cat->id);
         @endphp
            <tr >
              <td>{{ $cat->account_number }}</td>
              <td class="text-danger h6 text-center">
                 <b>{{ $cat->name }}</b>
              </td>
              <td  colspan="7"></td>
            </tr>
         @foreach($catTrades as $trd)

              @php
                  $bids = @$project->proposals()->trade($trd->id)->IsAwarded()
                         ->get();
                   if($bids->count() == 0){
                     continue;
                   }      
              @endphp

            <tr >
              <td>{{ $trd->account_number }}</td>
              <td >
                 <span class="text-center" style="width: 15%;">{{ $trd->name  }}</span>
              </td>
             
              @foreach($bids as $bid)
                @php    
                  $bidTotal =  (float) @$bid->material + (float) @$bid->labour_cost + (float) @$bid->subcontractor_price;   

                     foreach(@$bid->changeOrders as $k => $order){
                       if($order->type == \App\Models\ChangeOrder::ADD ){
                         $bidTotal += $order->subcontractor_price;
                       }
                       else{
                         $bidTotal -= $order->subcontractor_price;
                       }
                     }
                     echo $bidTotal;
                  
                       $bidPayments =   $bid->payment;

                       $payment_vendors  = [];

                       $payment_vendors = @collect($bidPayments)->map(function($p) {
                              if($p->vendor){
                                return $p->vendor->name;
                              }
                       })->unique()->join(',');

                       @collect($bidPayments)->each(function($p) use (&$vendors){
                           if($p->vendor){
                              $amount = (double) \App\Models\Payment::format($p->payment_amount);
                              if(isset( $vendors[$p->vendor->name])){
                                   $amount = $vendors[$p->vendor->name] +
                                     \App\Models\Payment::format($p->payment_amount);   
                              }
                              $vendors[$p->vendor->name] = (double) $amount;  
                             }
                       });
                        
                          
                       $notes = @$bid->payment()->whereNotNull('notes')->pluck('notes')->join(',');
                      
                        
                      $paid =  (float) @$bid->payment()->whereNull('vendor_id')->sum('payment_amount');
                      $due =  (float) @$bidTotal  - (float) $paid;

                      $materialTotal = (float) @$bid->material + $materialTotal;
                      $labourTotal = (float) @$bid->labour_cost + $labourTotal;
                      $subcontractorTotal = (float) @$bid->subcontractor_price + $subcontractorTotal;
                      $grandTotal = (float) @$bidTotal + $grandTotal;
                      $paidTotal = (float) @$paid + $paidTotal;
                      $dueTotal = (float) @$due + $dueTotal;

                      $catGrandTotal = (float) @$bidTotal + $catGrandTotal;
                      $catPaidTotal = (float) @$paid + $catPaidTotal;
                      $catDueTotal = (float) @$due + $catDueTotal;

                @endphp

                  <td>${{ (float) @\App\Models\Payment::format($bid->material)  }}</td>
                  <td>${{ (float) @\App\Models\Payment::format($bid->labour_cost)  }}</td>
                  <td>${{ (float) @\App\Models\Payment::format($bid->subcontractor_price)  }}</td>
                  <!-- <td><span class="doc_type_m">{{  @implode(',',$vendors) }}</span></td> -->
                  <td>${{  \App\Models\Payment::format($bidTotal)  }}</td>
                  <td>${{ \App\Models\Payment::format($paid) }}</td>
                  <td>${{ \App\Models\Payment::format($due) }} </td> 
                  <td>{{ sprintf('%0.2f', $paid /@$bidTotal * 100) }} % </td> 
                  <!-- <td>{{ trim(@$notes) }}</td>  -->
                </tr>

                <tr>
                  <td colspan="2" style="padding:20px;"></td>
                  <td></td>
                  <td></td>
                  <td><span class="doc_type_m">{{ @$bid->subcontractor->name }}</span></td>
                  <!-- <td><span class="doc_type_m">{{ @trim($payment_vendors,',') }}</span></td> -->
                  <td colspan="4" style="padding:20px;"></td>
                  <!-- <td colspan="4" style="padding:20px;"></td> -->
                </tr>

              @endforeach

         @endforeach

            <tr>
                 <td class="text-danger h6 text-center" colspan="2">
                 <b>{{ $cat->name }} Total </b>
                 </td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <!-- <td></td> -->
                  <td><b>${{ \App\Models\Payment::format($catGrandTotal ) }}</b></td>
                  <td><b>${{ \App\Models\Payment::format($catPaidTotal) }}</b></td>
                  <td><b>${{ \App\Models\Payment::format($catDueTotal) }} </b></td> 
                  <td><b>{{ ($catGrandTotal && $catPaidTotal) ? sprintf('%0.2f', @$catPaidTotal / @$catGrandTotal * 100) : 0 }} % </b></td>
                  <!-- <td></td> -->
           </tr>

           <tr>
            <td colspan="10" style="padding:20px;"></td>
           </tr>

        @endforeach

        @php
         $extraTotal = 0;
         @endphp
          
         @if($vendors) 

          
           <tr>
               <td colspan="2"><b>Extra</b></td>
               <td colspan="8" style="padding:20px;"></td>
               <!-- <td></td> -->
               <!-- <td></td> -->
           </tr>

           @foreach($vendors as $k => $vndr)
              @php
                $extraTotal = $extraTotal + $vndr;
              @endphp
            <tr>
               <td></td>
               <td>{{ $k}}</td>
               <td>${{ (float) @\App\Models\Payment::format($vndr)}}</td>
               <td colspan="7" style="padding:20px;"></td>
           </tr>

           @endforeach
         
         <tr>
               <td class="text-danger h6 text-center" colspan="2">
               <b>Extra Total </b>
               </td>
               <td><b>${{ \App\Models\Payment::format($extraTotal )}}</b></td>
               <td colspan="7" style="padding:20px;"></td>
         </tr>

         <tr>
            <td colspan="10" style="padding:20px;"></td>
           </tr>

         @endif

            <tr>
               <td>Total</td>
               <td></td>
               <td> Material</td>
               <td> Labor</td>
               <td> Subcontractor</td>
               <td></td>
               <td></td>
               <td></td>
               <td></td>
               <!-- <td></td> -->
               <!-- <td></td> -->
           </tr>

           <tr>
               <td><b>Project Total</b></td>
               <td></td>
               <td><b>${{ \App\Models\Payment::format($materialTotal + $extraTotal )}}</b></td>
               <td><b>${{ \App\Models\Payment::format($labourTotal) }}</b></td>
               <td><b>${{ \App\Models\Payment::format($subcontractorTotal) }}</b></td>
               <!-- <td></td> -->
               <td><b>${{ \App\Models\Payment::format($grandTotal) }}</b></td>
               <td><b>${{ \App\Models\Payment::format($paidTotal) }}</b></td>
               <td><b>${{ \App\Models\Payment::format($dueTotal) }}</b></td>
               <td><b>{{ ($paidTotal && $grandTotal) ? sprintf('%0.2f', @$paidTotal / @$grandTotal * 100) : 0 }} % </b></td>
               <!-- <td></td> -->
            
           </tr>

            </tbody>
        </table>
</div>

</div>
</div>
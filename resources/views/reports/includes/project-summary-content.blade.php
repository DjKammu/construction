<style type="text/css">
span.doc-type{
 font-size: 12px;
 padding-top: 8px;
 display: block;
}
span.doc_type_m{
 font-size: 10px;
 padding-top: 3px;
 display: block;
}
table.payments-table{
      font-size: 12px;
      font-family: Arial;
}

table.payments-table thead>tr>th{
   font-size: 12px;
}
</style>

<div class="table-responsive">
   <!-- <h4 class="mt-0 text-center">  </h4> -->
    <table id="project-types-table" class="table table-hover text-center payments-table">
            <thead>
            <tr class="text-danger">
                <th>Item No.</th>
                <th >Category&Trade</th>
                <th>Material</th>
                <th>Labor</th>
                <th>Subcontractor</th>
                <!-- <th>Vendors</th> -->
                <th>Total </th>
                <th>Paid</th>
                <th >Remaining  </th>
                <th> %Complete </th>
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
         $vendorsTotal = 0;
          $vendors = [];
         @endphp

        @foreach($categories as $cat)

         @php   
          
          $catMaterialTotal = 0;
          $catLabourTotal = 0;
          $catSubcontractorTotal2 = 0;
          $catGrandTotal = 0;
          $catPaidTotal = 0;
          $catDueTotal = 0;
          $catSubcontractorTotal = 0;
          $catVendorsTotal = 0;

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
              $changeOrderTotal = 0;              
              $bids = @$project->proposals()->trade($trd->id)->IsAwarded()
                         ->get();     
              $tradePayments = @$project->payments()->whereNotNull('vendor_id')
                               ->selectRaw('sum(payment_amount) as payment_amount_total, vendor_id,material_id')
                               ->where('trade_id',$trd->id)
                               ->groupBy('vendor_id','material_id')
                               ->get();                 
               
              @endphp
            
             @if($bids->count() > 0)
            <tr>
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
                         $catSubcontractorTotal += $order->subcontractor_price;
                         $catSubcontractorTotal2 += $order->subcontractor_price;
                         $changeOrderTotal += $order->subcontractor_price;
                       }
                       else{
                         $bidTotal -= $order->subcontractor_price;
                         $catSubcontractorTotal -= $order->subcontractor_price;
                         $catSubcontractorTotal2 -= $order->subcontractor_price;
                         $changeOrderTotal -= $order->subcontractor_price;
                       }
                     }
                     
                       $bidPayments =   $bid->payment;

                       $payment_vendors = [];

                       $payment_vendors = @collect($bidPayments)->map(function($p) {
                              if($p->vendor){
                                return $p->vendor->name;
                              }
                       })->unique()->join(',');

                       @collect($bidPayments)->each(function($p) use (&$vendors){
                           if($p->vendor){
                              $amount =  $p->payment_amount;
                              if(isset( $vendors[$p->vendor->name])){
                                   $amount = $vendors[$p->vendor->name] +
                                    $p->payment_amount;   
                              }
                              $vendors[$p->vendor->name] =  $amount;  
                             }
                       });


                       $notes = @$bid->payment()->whereNotNull('notes')->pluck('notes')->join(',');
                      
                        
                      $paid =  (float) @$bid->payment()->whereNull('vendor_id')->sum('payment_amount');
                      $due =  (float) @$bidTotal  - (float) $paid;

                      $catSubcontractorTotal +=  @$bid->subcontractor_price;

                      $materialTotal = (float) @$bid->material + $materialTotal;
                      $labourTotal = (float) @$bid->labour_cost + $labourTotal;
                      $subcontractorTotal = (float) $catSubcontractorTotal + $subcontractorTotal;
                      $grandTotal = (float) @$bidTotal + $grandTotal;
                      $paidTotal = (float) @$paid + $paidTotal;
                      $dueTotal = (float) @$due + $dueTotal;
                      
                      $catSubcontractorTotal2 = (float) @$bid->subcontractor_price + $catSubcontractorTotal2;
                      $catMaterialTotal = (float) @$bid->material + $catMaterialTotal;
                      $catLabourTotal = (float) @$bid->labour_cost + $catLabourTotal;
                      $catGrandTotal = (float) @$bidTotal + $catGrandTotal;
                      $catPaidTotal = (float) @$paid + $catPaidTotal;
                      $catDueTotal = (float) @$due + $catDueTotal;

                @endphp

                  <td>${{  @\App\Models\Payment::format($bid->material)  }}</td>
                  <td>${{  @\App\Models\Payment::format($bid->labour_cost)  }}</td>
                  <td>${{  @\App\Models\Payment::format($catSubcontractorTotal) }}
                  </br> <span class="doc_type_m">{{ ($changeOrderTotal > 0) ? 'Change Orders - $'. @\App\Models\Payment::format($changeOrderTotal) : ''  }}</span></td>
                  <!-- <td><span class="doc_type_m">{{  @implode(',',$vendors) }}</span></td> -->
                  <td>${{  \App\Models\Payment::format($bidTotal)  }}</td>
                  <td>${{ \App\Models\Payment::format($paid) }}</td>
                  <td>${{ \App\Models\Payment::format($due) }} </td> 
                  <td>{{ ($paid && $bidTotal) ?  sprintf('%0.2f', @$paid /@$bidTotal * 100)  : 0 }}
                   % </td> 
                  <!-- <td>{{ trim(@$notes) }}</td>  -->
                </tr>

                <tr>
                  <td colspan="2" style="padding:10px;"></td>
                  <td></td>
                  <td></td>
                  <td><span class="doc_type_m">{{ @$bid->subcontractor->name }}</span></td>
                  <!-- <td><span class="doc_type_m">{{ @trim($payment_vendors,',') }}</span></td> -->
                  <td colspan="4" style="padding:10px;"></td>
                  <!-- <td colspan="4" style="padding:10px;"></td> -->
                </tr>
              @endforeach

            @endif

            @if($tradePayments->count() > 0)
            
              @foreach($tradePayments as $tPay)

               @php
                $vendorsTotal = $vendorsTotal + $tPay->payment_amount_total;
                $catVendorsTotal = $catVendorsTotal + $tPay->payment_amount_total;

               @endphp

                  <tr>
                    <td>{{ $trd->account_number }}</td>
                    <td >
                       <span class="text-center" style="width: 15%;">{{ $trd->name  }}</span>
                    </td>
                   
                  <td>${{  @\App\Models\Payment::format(@$tPay->payment_amount_total)  }}</td>
                  <td>${{  @\App\Models\Payment::format(0.00)  }}</td>
                  <td>${{  @\App\Models\Payment::format(0.00)  }}</td>
                  <!-- <td><span class="doc_type_m">{{  @implode(',',$vendors) }}</span></td> -->
                  <td>${{  \App\Models\Payment::format(@@$tPay->payment_amount_total)  }}</td>
                  <td>${{ \App\Models\Payment::format(@@$tPay->payment_amount_total) }}</td>
                  <td>${{ \App\Models\Payment::format(0.00) }} </td> 
                  <td> 100 % </td> 
                  <!-- <td>{{ trim(@$notes) }}</td>  -->
                </tr>

                <tr>
                  <td colspan="2" style="padding:10px;"></td>
                  <td><span class="doc_type_m">{{ @$tPay->vendor->name }} {{ 
                (@$tPay->material) ? '('.@$tPay->material->name .')' : ""}}</span></td>
                  <td></td>
                  <td></td>
                  <!-- <td><span class="doc_type_m">{{ @trim($payment_vendors,',') }}</span></td> -->
                  <td colspan="4" style="padding:10px;"></td>
                  <!-- <td colspan="4" style="padding:10px;"></td> -->
                </tr>
              @endforeach

            @endif

         @endforeach

            <tr>
                 <td class="text-danger h6 text-center" colspan="2">
                 <b>{{ $cat->name }} Total </b>
                 </td>
                  <td><b>${{ \App\Models\Payment::format($catMaterialTotal + $catVendorsTotal) }}</b></td>
                  <td><b>${{ \App\Models\Payment::format($catLabourTotal) }}</b></td>
                  <td><b>${{ \App\Models\Payment::format($catSubcontractorTotal2) }}</b></td>
                  <!-- <td></td> -->
                  <td><b>${{ \App\Models\Payment::format($catGrandTotal + $catVendorsTotal) }}</b></td>
                  <td><b>${{ \App\Models\Payment::format($catPaidTotal + $catVendorsTotal) }}</b></td>
                  <td><b>${{ \App\Models\Payment::format($catDueTotal) }} </b></td> 
                  <td><b>{{ ($catGrandTotal && $catPaidTotal) || ($catVendorsTotal) ? sprintf('%0.2f', (@$catPaidTotal + $catVendorsTotal) / (@$catGrandTotal + $catVendorsTotal) * 100) : 0 }} % </b></td>
                  <!-- <td></td> -->
           </tr>

           <tr>
            <td colspan="10" style="padding:10px;"></td>
           </tr>

        @endforeach

         @php
         $extraTotal = 0;
         @endphp

        @if($vendors) 

        
          
         <!--   <tr>
               <td colspan="2"><b>Extra Material</b></td>
               <td colspan="8" style="padding:10px;"></td>
               <!-- <td></td> -->
               <!-- <td></td>
           </tr> -->

           @foreach($vendors as $k => $vndr)
              @php
                $extraTotal = $extraTotal + $vndr;
              @endphp
            <!-- <tr>
               <td></td>
               <td>{{ $k}}</td>
               <td>${{ @\App\Models\Payment::format($vndr)}}</td>
               <td colspan="7" style="padding:10px;"></td>
           </tr> -->

           @endforeach
         
         <!-- <tr>
               <td class="text-danger h6 text-center" colspan="2">
               <b>Extra Material Total </b>
               </td>
               <td><b>${{ \App\Models\Payment::format($extraTotal )}}</b></td>
               <td colspan="7" style="padding:10px;"></td>
         </tr>

         <tr>
            <td colspan="10" style="padding:20px;"></td>
           </tr> -->

         @endif

           <tr>
               <td>Total</td>
               <td></td>
               <td> Material</td>
               <td> Labor</td>
               <td> Subcontractor</td>
               <td>Total </td>
                <td>Total Paid</td>
                <td>Remaining Payment </td>
                <td> % Complete </td>
               <!-- <td></td> -->
               <!-- <td></td> -->
           </tr>

           <tr>
               <td><b>Project Total</b></td>
               <td></td>
               <td><b>${{ \App\Models\Payment::format($materialTotal + $vendorsTotal)}}</b></td>
               <td><b>${{ \App\Models\Payment::format($labourTotal) }}</b></td>
               <td><b>${{ \App\Models\Payment::format($subcontractorTotal) }}</b></td>
               <!-- <td></td> -->
               <td><b>${{ \App\Models\Payment::format($grandTotal + $vendorsTotal) }}</b></td>
               <td><b>${{ \App\Models\Payment::format($paidTotal + $vendorsTotal) }}</b></td>
               <td><b>${{ \App\Models\Payment::format($dueTotal) }}</b></td>
                <td><b>{{ ($paidTotal && $grandTotal)  || ($vendorsTotal) ? sprintf('%0.2f', (@$paidTotal + $vendorsTotal)/ (@$grandTotal + $vendorsTotal) * 100) : 0 }} % </b></td>

               <!-- <td ></td> -->
            
           </tr>

            </tbody>
        </table>
</div>

<!----

<div class="table-responsive">
   <h4 class="mt-0 text-center">  </h4>
    <table id="project-types-table" class="table table-hover text-center payments-table">
            <thead>
            <tr class="text-danger">
                <th colspan=""> </th>
                <th colspan="2">Project Name</th>
                <th ></th>
                <th colspan="2"> Today Date </th>
                <th ></th>
                <th></th>
                <th></th>
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
          
         @foreach($catTrades as $trd)

              @php
                 
                  $bids = @$project->proposals()->trade($trd->id)->IsAwarded()
                         ->get();
                   if($bids->count() == 0){
                     continue;
                   }     
              @endphp

             

              @foreach($bids as $bid)
                @php    
                  $bidTotal =  (float) @$bid->material + (float) @$bid->labour_cost + (float) @$bid->subcontractor_price;   
                    
                     foreach(@$bid->changeOrders as $k => $order){
                       if($order->type == \App\Models\ChangeOrder::ADD ){
                         $bidTotal += $order->subcontractor_price;
                         $subcontractorTotal += $order->subcontractor_price;
                       }
                       else{
                         $bidTotal -= $order->subcontractor_price;
                         $subcontractorTotal -= $order->subcontractor_price;
                       }
                     }
                     
                       $bidPayments =   $bid->payment;

                       $payment_vendors = [];

                       $payment_vendors = @collect($bidPayments)->map(function($p) {
                              if($p->vendor){
                                return $p->vendor->name;
                              }
                       })->unique()->join(',');

                       @collect($bidPayments)->each(function($p) use (&$vendors){
                           if($p->vendor){
                              $amount =  $p->payment_amount;
                              if(isset( $vendors[$p->vendor->name])){
                                   $amount = $vendors[$p->vendor->name] +
                                     $p->payment_amount;   
                              }
                              $vendors[$p->vendor->name] =  $amount;  
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



              @endforeach

         @endforeach

    
        @endforeach

         @php
         $extraTotal = 0;
         @endphp

        @if($vendors) 

          
           @foreach($vendors as $k => $vndr)
              @php
                $extraTotal = $extraTotal + $vndr;
              @endphp
           
           @endforeach
         
         @endif


              <tr>
                <td colspan=""></td>
                <td colspan="2">{{ @$project->name }}</td>
                <td></td>
                <td colspan="2"> {{ \Carbon\Carbon::now()->format('m-d-Y') }} </td>
                <td></td>
                <td></td>
                <td></td>
              </tr> 

              <tr>
                <td colspan="2"></td>
                <td></td>
                <td><b>Budget</b></td>
                <td></td>
                <td colspan="2"><b>Actual Paid</b></td>
                <td></td>
              </tr> 

            <tr>
              <td colspan="2" class="text-left">Total Material Paid</td>
              <td></td>
              <td >${{ \App\Models\Payment::format($materialTotal + $extraTotal)}}</td>
              <td></td>
              <td colspan="2"> ${{ \App\Models\Payment::format( $extraTotal) }} </td>
              <td></td>
            </tr>

            <tr>
              <td colspan="2" class="text-left">Total Labour</td>
              <td></td>
              <td >${{ \App\Models\Payment::format($labourTotal) }}</td>
              <td></td>
              <td colspan="2"> </td>
              <td></td>
            </tr>

            <tr>
              <td colspan="2" class="text-left">Total Subcontractor</td>
              <td></td>
              <td >${{ \App\Models\Payment::format($subcontractorTotal) }}</td>
              <td></td>
              <td colspan="2"> ${{ \App\Models\Payment::format($paidTotal) }} </td>
              <td></td>
            </tr> 

             <tr>
              <td colspan="2"></td>
              <td><b>Total</b></td>
              <td ><b>${{ \App\Models\Payment::format($grandTotal + $extraTotal) }}</b></td>
              <td></td>
              <td colspan="2"> <b>${{ \App\Models\Payment::format($paidTotal + $extraTotal) }} </b></td>
              <td></td>
            </tr>


            </tbody>
        </table>
</div>-->
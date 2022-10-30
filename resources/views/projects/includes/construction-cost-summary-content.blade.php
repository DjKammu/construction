<div class="table-responsive">
   <!-- <h4 class="mt-0 text-center">  </h4> -->
    <table id="project-types-table" class="table table-hover text-center payments-table">
            <thead>
            <tr class="text-danger">
                <th>Item No.</th>
                <th >Category&Trade</th>
                <th >Trade Budget</th>
                <th>Material</th>
                <th>Labor</th>
                <th>Subcontractor</th>
                <!-- <th>Vendors</th> -->
                <th>Total </th>
                <th>Paid</th>
                <th >Remaining  </th>
                <th >Budget Diff  </th>

                <th> %Complete </th>
                <!-- <th> Notes </th> -->
            </tr>
            </thead>
            <tbody>
         @php   
         $materialTotal = 0;
         $tradeTotal = 0;
         $budgetDiff = 0;
         $labourTotal = 0;
         $subcontractorTotal = 0;
         $grandTotal = 0;
         $paidTotal = 0;
         $dueTotal = 0;
         $vendorsTotal = 0;
         $changeOrderTotal = 0;
         $vendors = [];
         @endphp

        @foreach($paymentCategories as $cat)

         @php   
          $catMaterialTotal = 0;
          $catLabourTotal = 0;
          $catSubcontractorTotal = 0;
          $catGrandTotal = 0;
          $catPaidTotal = 0;
          $catDueTotal = 0;
          $catVendorsTotal = 0;
          $catTradeTotal = 0;
          $catBudgetDiff = 0;

         $catTrades = @$trades->where('category_id', $cat->id);
         
         @endphp
            <!-- <tr >
              <td>{{ $cat->account_number }}</td>
              <td class="text-danger h6 text-center">
                 <b>{{ $cat->name }}</b>
              </td>
              <td  colspan="8"></td>
            </tr> -->
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

            <!-- <tr >
              <td>{{ $trd->account_number }}</td>
              <td >
                 <span class="text-center" style="width: 15%;">{{ $trd->name  }}</span>
              </td> -->
             
              @foreach($bids as $bid)
                @php    
                  $bidTotal =  (float) @$bid->material + (float) @$bid->labour_cost + (float) @$bid->subcontractor_price;   
                     foreach(@$bid->changeOrders as $k => $order){
                       if($order->type == \App\Models\ChangeOrder::ADD ){
                         $bidTotal += $order->subcontractor_price;
                         $changeOrderTotal += $order->subcontractor_price;
                         $catSubcontractorTotal += $order->subcontractor_price;
                       }
                       else{
                         $bidTotal -= $order->subcontractor_price;
                         $changeOrderTotal -= $order->subcontractor_price;
                         $catSubcontractorTotal -= $order->subcontractor_price;
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
                      $tradeTotal = (float) @$bid->trade_budget + $tradeTotal;
                      $labourTotal = (float) @$bid->labour_cost + $labourTotal;
                      $subcontractorTotal = (float) @$bid->subcontractor_price + $subcontractorTotal + $changeOrderTotal;
                      $grandTotal = (float) @$bidTotal + $grandTotal;
                      $paidTotal = (float) @$paid + $paidTotal;
                      $dueTotal = (float) @$due + $dueTotal;
                      //$budgetDiff = (float) @$bid->trade_budget - $bidTotal + $budgetDiff;
                      
                      $catSubcontractorTotal = (float) @$bid->subcontractor_price + $catSubcontractorTotal;
                      $catMaterialTotal = (float) @$bid->material + $catMaterialTotal;
                      $catTradeTotal = (float) @$bid->trade_budget + $catTradeTotal;
                      $catLabourTotal = (float) @$bid->labour_cost + $catLabourTotal;
                      $catGrandTotal = (float) @$bidTotal + $catGrandTotal;
                      $catPaidTotal = (float) @$paid + $catPaidTotal;
                      $catDueTotal = (float) @$due + $catDueTotal;
                      // $catbudgetDiff = (float) @$bid->trade_budget - @$bidTotal +  @$catbudgetDiff;

                @endphp

                 <!--  <td>${{  @\App\Models\Payment::format(@$bid->trade_budget)  }}</td>
                  <td>${{  @\App\Models\Payment::format($bid->material)  }}</td>
                  <td>${{  @\App\Models\Payment::format($bid->labour_cost)  }}</td>
                  <td>${{  @\App\Models\Payment::format($bid->subcontractor_price)  }}</br> <span class="doc_type_m">{{ ($changeOrderTotal > 0) ? 'Change Orders - $'. @\App\Models\Payment::format($changeOrderTotal) : ''  }}</span></td>
                  <td><span class="doc_type_m">{{  @implode(',',$vendors) }}</span></td>
                  <td>${{  \App\Models\Payment::format($bidTotal)  }}</td>
                  <td>${{ \App\Models\Payment::format($paid) }}</td>
                  <td>${{ \App\Models\Payment::format($due) }} </td> 
                  <td>${{ \App\Models\Payment::format((float) @$bid->trade_budget - $bidTotal) }} </td> 
                  <td>{{ ($paid && $bidTotal) ?  sprintf('%0.2f', @$paid /@$bidTotal * 100)  : 0 }}
                   % </td> 
                  <!-- <td>{{ trim(@$notes) }}</td>
                </tr> -->
                <!-- 
                <tr>
                  <td colspan="2" style="padding:10px;"></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td><span class="doc_type_m">{{ @$bid->subcontractor->name }}</span></td>
                  <!-- <td><span class="doc_type_m">{{ @trim($payment_vendors,',') }}</span></td>
                  <td colspan="5" style="padding:10px;"></td>
                  <!-- <td colspan="4" style="padding:10px;"></td>
                </tr> -->


              @endforeach

              @endif

            @if($tradePayments->count() > 0)
            
              @foreach($tradePayments as $tPay)

               @php
                $vendorsTotal = $vendorsTotal + $tPay->payment_amount_total;
                $catVendorsTotal = $catVendorsTotal + $tPay->payment_amount_total;
               @endphp

                  <!-- <tr>
                    <td>{{ $trd->account_number }}</td>
                    <td >
                       <span class="text-center" style="width: 15%;">{{ $trd->name  }}</span>
                    </td>
                    <td ></td>
                   
                  <td>${{  @\App\Models\Payment::format(@$tPay->payment_amount_total)  }}</td>
                  <td>${{  @\App\Models\Payment::format(0.00)  }}</td>
                  <td>${{  @\App\Models\Payment::format(0.00)  }}</td>
                  <!-- <td><span class="doc_type_m">{{  @implode(',',$vendors) }}</span></td>
                  <td>${{  \App\Models\Payment::format(@@$tPay->payment_amount_total)  }}</td>
                  <td>${{ \App\Models\Payment::format(@@$tPay->payment_amount_total) }}</td>
                  <td>${{ \App\Models\Payment::format(0.00) }} </td> 
                  <td>${{ \App\Models\Payment::format(0.00) }} </td> 
                  <td> 100 % </td> 
                  <!-- <td>{{ trim(@$notes) }}</td> 
                </tr>

                <tr>
                  <td colspan="3" style="padding:10px;"></td>
                  <td><span class="doc_type_m">{{ @$tPay->vendor->name }} {{ 
                (@$tPay->material) ? '('.@$tPay->material->name .')' : ""}}</span></td>
                  <td></td>
                  <td></td>
                  <!-- <td><span class="doc_type_m">{{ @trim($payment_vendors,',') }}</span></td> 
                  <td colspan="5" style="padding:10px;"></td>
                  <!-- <td colspan="4" style="padding:10px;"></td> 
                </tr> -->
              @endforeach

            @endif

         @endforeach

         <!--    <tr>
                 <td class="text-danger h6 text-center" colspan="2">
                 <b>{{ $cat->name }} Total </b>
                 </td>
                  <td><b>${{ \App\Models\Payment::format($catTradeTotal) }}</b></td>
                  <td><b>${{ \App\Models\Payment::format($catMaterialTotal + $catVendorsTotal) }}</b></td>
                  <td><b>${{ \App\Models\Payment::format($catLabourTotal) }}</b></td>
                  <td><b>${{ \App\Models\Payment::format($catSubcontractorTotal) }}</b></td>
                  <!-- <td></td> 
                  <td><b>${{ \App\Models\Payment::format($catGrandTotal + $catVendorsTotal) }}</b></td>
                  <td><b>${{ \App\Models\Payment::format($catPaidTotal + $catVendorsTotal) }}</b></td>
                  <td><b>${{ \App\Models\Payment::format($catDueTotal) }} </b></td> 
                  <td><b>${{ \App\Models\Payment::format($catTradeTotal - $catGrandTotal) }} </b></td> 
                  <td><b>{{ ($catGrandTotal && $catPaidTotal) || ($catVendorsTotal) ? sprintf('%0.2f', (@$catPaidTotal + $catVendorsTotal) / (@$catGrandTotal + $catVendorsTotal) * 100) : 0 }} % </b></td>
                  <!-- <td></td> 
           </tr>

           <tr>
            <td colspan="11" style="padding:10px;"></td>
           </tr> -->

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
               <td><b>Project Total</b></td>
               <td></td>
               <td><b>${{ \App\Models\Payment::format($tradeTotal)}}</b></td>
               <td><b>${{ \App\Models\Payment::format($materialTotal + $vendorsTotal)}}</b></td>
               <td><b>${{ \App\Models\Payment::format($labourTotal) }}</b></td>
               <td><b>${{ \App\Models\Payment::format($subcontractorTotal) }}</b></td>
               <!-- <td></td> -->
               <td><b>${{ \App\Models\Payment::format($grandTotal + $vendorsTotal) }}</b></td>
               <td><b>${{ \App\Models\Payment::format($paidTotal + $vendorsTotal) }}</b></td>
               <td><b>${{ \App\Models\Payment::format($dueTotal) }}</b></td>
               <td><b>${{ \App\Models\Payment::format($tradeTotal - $grandTotal) }}</b></td>

               <td><b>{{ ($paidTotal && $grandTotal)  || ($vendorsTotal) ? sprintf('%0.2f', (@$paidTotal + $vendorsTotal)/ (@$grandTotal + $vendorsTotal) * 100) : 0 }} % </b></td>
               <!-- <td ></td> -->
            
           </tr>

        
        <tr>
          <td colspan="11" class="h4 text-center" style="padding:10px;">
            <b> FFE Budget </b> 
          </td>
         </tr>

        

         @php   

         $materialTotal = 0;
         $tradeTotal = 0;
         $budgetDiff = 0;
         $labourTotal = 0;
         $subcontractorTotal = 0;
         $grandTotal = 0;
         $paidTotal = 0;
         $dueTotal = 0;
         $vendorsTotal = 0;
         $changeOrderTotal = 0;

         $vendors = [];
         @endphp

        @foreach($ffePaymentCategories as $cat)

         @php   
          $catMaterialTotal = 0;
          $catLabourTotal = 0;
          $catSubcontractorTotal = 0;
          $catGrandTotal = 0;
          $catPaidTotal = 0;
          $catDueTotal = 0;
          $catVendorsTotal = 0;
          $catTradeTotal = 0;
          $catBudgetDiff = 0;

         $catTrades = @$ffe_pTrades->where('category_id', $cat->id);

         @endphp
            <!-- <tr >
              <td>{{ $cat->account_number }}</td>
              <td class="text-danger h6 text-center">
                 <b>{{ $cat->name }}</b>
              </td>
              <td  colspan="9"></td>
            </tr> -->
         @foreach($catTrades as $trd)

              @php
              $changeOrderTotal = 0;
              $bids = @$project->ffe_proposals()->trade($trd->id)->IsAwarded()
                     ->get();
              $tradePayments = @$project->ffe_payments()->where('non_contract','1')
              ->selectRaw('sum(payment_amount) as payment_amount_total, f_f_e_vendor_id')
               ->where('f_f_e_trade_id',$trd->id)
               ->groupBy('f_f_e_vendor_id')
             ->get();


              @endphp
            
             @if($bids->count() > 0)
<!-- 
            <tr >
              <td>{{ $trd->account_number }}</td>
              <td >
                 <span class="text-center" style="width: 15%;">{{ $trd->name  }}</span>
              </td> -->
             
              @foreach($bids as $bid)
                @php    
                  $bidTotal =  (float) @$bid->material + (float) @$bid->labour_cost + (float) @$bid->subcontractor_price;   
                     foreach(@$bid->changeOrders as $k => $order){
                       if($order->type == \App\Models\FFEChangeOrder::ADD ){
                         $bidTotal += $order->subcontractor_price;
                         $changeOrderTotal += $order->subcontractor_price;
                         $catSubcontractorTotal += $order->subcontractor_price;
                       }
                       else{
                         $bidTotal -= $order->subcontractor_price;
                         $changeOrderTotal -= $order->subcontractor_price;
                         $catSubcontractorTotal -= $order->subcontractor_price;
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
                      
                        
                      $paid =  (float) @$bid->payment()->where('non_contract','0')->sum('payment_amount');
                      $due =  (float) @$bidTotal  - (float) $paid;

                      $materialTotal = (float) @$bid->material + $materialTotal;
                      $tradeTotal = (float) @$bid->trade_budget + $tradeTotal;
                      $labourTotal = (float) @$bid->labour_cost + $labourTotal;
                      $subcontractorTotal = (float) @$bid->subcontractor_price + $subcontractorTotal + $changeOrderTotal;
                      $grandTotal = (float) @$bidTotal + $grandTotal;
                      $paidTotal = (float) @$paid + $paidTotal;
                      $dueTotal = (float) @$due + $dueTotal;
                      //$budgetDiff = (float) @$bid->trade_budget - $bidTotal + $budgetDiff;
                      
                      $catSubcontractorTotal = (float) @$bid->subcontractor_price + $catSubcontractorTotal;
                      $catMaterialTotal = (float) @$bid->material + $catMaterialTotal;
                      $catTradeTotal = (float) @$bid->trade_budget + $catTradeTotal;
                      $catLabourTotal = (float) @$bid->labour_cost + $catLabourTotal;
                      $catGrandTotal = (float) @$bidTotal + $catGrandTotal;
                      $catPaidTotal = (float) @$paid + $catPaidTotal;
                      $catDueTotal = (float) @$due + $catDueTotal;
                      // $catbudgetDiff = (float) @$bid->trade_budget - @$bidTotal +  @$catbudgetDiff;

                @endphp

                 <!--  <td>${{  @\App\Models\Payment::format(@$bid->trade_budget)  }}</td>
                  <td>${{  @\App\Models\Payment::format($bid->material)  }}</td>
                  <td> </td>
                  <td>${{  @\App\Models\Payment::format($bid->subcontractor_price)  }}</br> <span class="doc_type_m">{{ ($changeOrderTotal > 0) ? 'Change Orders - $'. @\App\Models\Payment::format($changeOrderTotal) : ''  }}</span></td>
                  <!-- <td><span class="doc_type_m">{{  @implode(',',$vendors) }}</span></td>
                  <td>${{  \App\Models\Payment::format($bidTotal)  }}</td>
                  <td>${{ \App\Models\Payment::format($paid) }}</td>
                  <td>${{ \App\Models\Payment::format($due) }} </td> 
                  <td>${{ \App\Models\Payment::format((float) @$bid->trade_budget - $bidTotal) }} </td> 
                  <td>{{ ($paid && $bidTotal) ?  sprintf('%0.2f', @$paid /@$bidTotal * 100)  : 0 }}
                   % </td> 
                  <!-- <td>{{ trim(@$notes) }}</td>  
                </tr>

                <tr>
                  <td colspan="2" style="padding:10px;"></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td><span class="doc_type_m">{{ @$bid->vendor->name }}</span></td>
                  <!-- <td><span class="doc_type_m">{{ @trim($payment_vendors,',') }}</span></td>
                  <td colspan="5" style="padding:10px;"></td>
                  <!-- <td colspan="4" style="padding:10px;"></td> 
                </tr> -->


              @endforeach

              @endif

            @if($tradePayments->count() > 0)
            
              @foreach($tradePayments as $tPay)

               @php
                $vendorsTotal = $vendorsTotal + $tPay->payment_amount_total;
                $catVendorsTotal = $catVendorsTotal + $tPay->payment_amount_total;
               @endphp

               <!--    <tr>
                    <td>{{ $trd->account_number }}</td>
                    <td >
                       <span class="text-center" style="width: 15%;">{{ $trd->name  }}</span>
                    </td>
                    <td ></td>
                   
                  <td>${{  @\App\Models\Payment::format(@$tPay->payment_amount_total)  }}</td>
                  <td></td>
                  <td>${{  @\App\Models\Payment::format(0.00)  }}</td>
                  <!-- <td><span class="doc_type_m">{{  @implode(',',$vendors) }}</span></td>
                  <td>${{  \App\Models\Payment::format(@@$tPay->payment_amount_total)  }}</td>
                  <td>${{ \App\Models\Payment::format(@@$tPay->payment_amount_total) }}</td>
                  <td>${{ \App\Models\Payment::format(0.00) }} </td> 
                  <td>${{ \App\Models\Payment::format(0.00) }} </td> 
                  <td> 100 % </td> 
                  <!-- <td>{{ trim(@$notes) }}</td> 
                </tr>

                <tr>
                  <td colspan="" style="padding:10px;"></td>
                  <td colspan="" style="padding:10px;"> <span class="doc_type_m">
                  Non Contract</span> </td>
                  <td colspan="" style="padding:10px;"></td>
                  <td colspan="" style="padding:10px;"></td>
                  <td colspan="" style="padding:10px;"></td>
                  <td><span class="doc_type_m">{{ @$tPay->vendor->name }} {{ 
                (@$tPay->material) ? '('.@$tPay->material->name .')' : ""}}</span></td>
                  <td></td>
                  <td colspan="4" style="padding:10px;"></td>
                </tr> -->
              @endforeach

            @endif

         @endforeach

           <!--  <tr>
                 <td class="text-danger h6 text-center" colspan="2">
                 <b>{{ $cat->name }} Total </b>
                 </td>
                  <td><b>${{ \App\Models\Payment::format($catTradeTotal) }}</b></td>
                  <td><b>${{ \App\Models\Payment::format($catMaterialTotal + $catVendorsTotal) }}</b></td>
                  <td></td>
                  <td><b>${{ \App\Models\Payment::format($catSubcontractorTotal) }}</b></td>
                  <!-- <td></td>
                  <td><b>${{ \App\Models\Payment::format($catGrandTotal + $catVendorsTotal) }}</b></td>
                  <td><b>${{ \App\Models\Payment::format($catPaidTotal + $catVendorsTotal) }}</b></td>
                  <td><b>${{ \App\Models\Payment::format($catDueTotal) }} </b></td> 
                  <td><b>${{ \App\Models\Payment::format($catTradeTotal - $catGrandTotal) }} </b></td> 
                  <td><b>{{ ($catGrandTotal && $catPaidTotal) || ($catVendorsTotal) ? sprintf('%0.2f', (@$catPaidTotal + $catVendorsTotal) / (@$catGrandTotal + $catVendorsTotal) * 100) : 0 }} % </b></td>
                  <!-- <td></td>
           </tr> -->

           <!-- <tr>
            <td colspan="11" style="padding:10px;"></td>
           </tr> -->

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


          <!--  <tr>
               <td>Total</td>
               <td></td>
               <td>Trade Budget</td>
               <td> Material</td>
               <td> Labor</td>
               <td> Subcontractor</td>
               <td>Total </td>
                <td>Total Paid</td>
                <td>Remaining Payment </td>
                 <td> Budget Diff  </td>
                <td> % Complete </td>
           </tr> -->

           <tr>
               <td><b>FFE Total</b></td>
               <td></td>
               <td><b>${{ \App\Models\Payment::format($tradeTotal)}}</b></td>
               <td><b>${{ \App\Models\Payment::format($materialTotal + $vendorsTotal)}}</b></td>
               <td><b>${{ \App\Models\Payment::format($labourTotal) }}</b></td>
               <td><b>${{ \App\Models\Payment::format($subcontractorTotal) }}</b></td>
               <!-- <td></td> -->
               <td><b>${{ \App\Models\Payment::format($grandTotal + $vendorsTotal) }}</b></td>
               <td><b>${{ \App\Models\Payment::format($paidTotal + $vendorsTotal) }}</b></td>
               <td><b>${{ \App\Models\Payment::format($dueTotal) }}</b></td>
               <td><b>${{ \App\Models\Payment::format($tradeTotal - $grandTotal) }}</b></td>

               <td><b>{{ ($paidTotal && $grandTotal)  || ($vendorsTotal) ? sprintf('%0.2f', (@$paidTotal + $vendorsTotal)/ (@$grandTotal + $vendorsTotal) * 100) : 0 }} % </b></td>
            
           </tr>

            </tbody>
        </table>
</div>
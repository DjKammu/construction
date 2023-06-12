<div class="table-responsive">
  <!-- <h4 class="mt-0 text-center">  </h4> -->
    <table id="construction-cost-content" class="table table-hover text-center payments-table">
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
                <th>Paid - Retainage</th>
                 <th>Retainage Held </th>
                <th >Remaining - Retainage</th>
                <th>Remaining + Retainage</th>
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
         $heldTotal = 0;
         $dueTotal = 0;
         $vendorsTotal = 0;
         $vendorsHeldTotal = 0;
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
          $catVendorsHeldTotal = 0;
          $catTradeTotal = 0;
          $catBudgetDiff = 0;
          $catHeldTotal = 0;

         $catTrades = @$trades->where('category_id', $cat->id);
         
         @endphp
            <tr >
              <td>{{ $cat->account_number }}</td>
              <td class="text-danger h6 text-center">
                 <b>{{ $cat->name }}</b>
              </td>
              <td  colspan="11"></td>
            </tr>
         @foreach($catTrades as $trd)

              @php
              $changeOrderTotal = 0;
              $bids = @$project->proposals()->trade($trd->id)->IsAwarded()
                     ->get();
              $tradePayments = @$project->payments()->whereNotNull('vendor_id')
                            ->selectRaw('sum(payment_amount) as payment_amount_total,sum(total_subcontractor_payment) as total_subcontractor_payment ,sum(retainage_held) as retainage_held, vendor_id,material_id')
                             ->where('trade_id',$trd->id)
                             ->groupBy('vendor_id','material_id')
                           ->get(); 
               
              @endphp
            
             @if($bids->count() > 0)

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
                       $held =  (float) @$bid->payment()->whereNull('vendor_id')->sum('retainage_held');
                       $due =  (float) @$bidTotal  - (float) $paid;

                      $materialTotal = (float) @$bid->material + $materialTotal;
                      $tradeTotal = (float) @$bid->trade_budget + $tradeTotal;
                      $labourTotal = (float) @$bid->labour_cost + $labourTotal;
                      $subcontractorTotal = (float) @$bid->subcontractor_price + $subcontractorTotal + $changeOrderTotal;
                      $grandTotal = (float) @$bidTotal + $grandTotal;
                      $paidTotal = (float) @$paid + $paidTotal;
                      $dueTotal = (float) @$due + $dueTotal;
                       $heldTotal = (float) @$held + $heldTotal;
                      //$budgetDiff = (float) @$bid->trade_budget - $bidTotal + $budgetDiff;
                      
                      $catSubcontractorTotal = (float) @$bid->subcontractor_price + $catSubcontractorTotal;
                      $catMaterialTotal = (float) @$bid->material + $catMaterialTotal;
                      $catTradeTotal = (float) @$bid->trade_budget + $catTradeTotal;
                      $catLabourTotal = (float) @$bid->labour_cost + $catLabourTotal;
                      $catGrandTotal = (float) @$bidTotal + $catGrandTotal;
                      $catPaidTotal = (float) @$paid + $catPaidTotal;
                      $catDueTotal = (float) @$due + $catDueTotal;
                      // $catbudgetDiff = (float) @$bid->trade_budget - @$bidTotal +  @$catbudgetDiff;

                       $subcontractorId = (@$bid->subcontractor->id) ? $bid->subcontractor->id : '';

                @endphp

                  <td>${{  @\App\Models\Payment::format(@$bid->trade_budget)  }}</td>
                  <td>${{  @\App\Models\Payment::format($bid->material)  }}</td>
                  <td>${{  @\App\Models\Payment::format($bid->labour_cost)  }}</td>
                  <td>${{  @\App\Models\Payment::format($bid->subcontractor_price)  }}</br> <span class="doc_type_m">{{ ($changeOrderTotal > 0) ? 'Change Orders - $'. @\App\Models\Payment::format($changeOrderTotal) : ''  }}</span></td>
                  <!-- <td><span class="doc_type_m">{{  @implode(',',$vendors) }}</span></td> -->
                  <td>${{  \App\Models\Payment::format($bidTotal)  }}</td>
                  <td> <span class="doc_type_m">
                       @if(request()->route()->getName()  == 'projects.show')
                    <a class="disable-anchor" href="{{ url("projects/$project->id").'?to=Total Construction Cost&url='.urlencode(url()->current().'#construction-cost').'&payment_subcontractor='.$subcontractorId.'#payments'}} "> ${{ \App\Models\Payment::format($paid) }}</a>

                    @else
                    ${{ \App\Models\Payment::format($paid) }}
                    @endif

                  </span></td>
                  <td>${{ \App\Models\Payment::format($held) }} </td> 
                  <td>${{ \App\Models\Payment::format($due  - $held) }} </td> 
                  <td>${{ \App\Models\Payment::format($due) }} </td> 
                  <td>${{ \App\Models\Payment::format((float) @$bid->trade_budget - $bidTotal) }} </td> 
                  <td>{{ ($paid && $bidTotal) ?  sprintf('%0.2f', (@$paid  + @$held)/@$bidTotal * 100)  : 0 }}
                   % </td> 
                  <!-- <td>{{ trim(@$notes) }}</td>  -->
                </tr>

                <tr>
                  <td colspan="2" style="padding:10px;"></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td>
                    @if(request()->route()->getName()  == 'projects.show')
                        @if(!empty($bid->files))
                          <span class="budget-image">
                          @php

                          $project_slug = \Str::slug($project->name);

                          $trade_slug = @\Str::slug($bid->trade->name);

                          $folderPath = App\Models\Document::PROPOSALS."/";

                          $folderPath .= "$project_slug/$trade_slug/";

                          @endphp

                           @foreach(@explode(',',$bid->files) as $file)
             
                                  @php
                                     $fileInfo = pathinfo($file); 
                                       $extension = @$fileInfo['extension'];
                                    
                                        if(in_array($extension,['doc','docx','docm','dot',
                                      'dotm','dotx'])){
                                          $extension = 'word'; 
                                       }
                                       else if(in_array($extension,['csv','dbf','dif','xla',
                                      'xls','xlsb','xlsm','xlsx','xlt','xltm','xltx'])){
                                          $extension = 'excel'; 
                                       }
                                     
                                      if(!$extension){
                                        $extension = 'pdf';
                                      }

                                  @endphp
                                  <a href="{{ url("$folderPath$file") }}" target="_blank">
                                <img class="avatar border-gray proposal_file" 
                                src="{{ asset('img/'.$extension.'.png') }}">
                                </a>

                           @endforeach
                         </span>

                           @endif
                           @endif
                        <span class="doc_type_m">
                       @if(request()->route()->getName()  == 'projects.show')
                    <a class="disable-anchor" href="{{ url("projects/$project->id").'?url='.urlencode(url()->current().'#construction-cost').'&to=Total Construction Cost&trade='.$trd->id.'#proposals'}}">{{ @$bid->subcontractor->name }}</a>

                    @else
                   {{ @$bid->subcontractor->name }}
                    @endif

                  </span></td>
                  <!-- <td><span class="doc_type_m">{{ @trim($payment_vendors,',') }}</span></td> -->
                  <td colspan="7" style="padding:10px;"></td>
                  <!-- <td colspan="4" style="padding:10px;"></td> -->
                </tr>


              @endforeach

              @endif

            @if($tradePayments->count() > 0)
            
              @foreach($tradePayments as $tPay)

               @php
                  $vendorsTotal = $vendorsTotal + $tPay->payment_amount_total;
                  $catVendorsTotal = $catVendorsTotal + $tPay->payment_amount_total;
                  $vendorsHeldTotal = $vendorsHeldTotal + $tPay->retainage_held;
                  $catVendorsHeldTotal = $catVendorsHeldTotal + $tPay->retainage_held;
                  $vendorId = (@$tPay->vendor->id) ? $tPay->vendor->id : '';
               @endphp

                  <tr>
                    <td>{{ $trd->account_number }}</td>
                    <td >
                       <span class="text-center" style="width: 15%;">{{ $trd->name  }}</span>
                    </td>
                    <td ></td>
                   
                  <td>${{  @\App\Models\Payment::format(@$tPay->payment_amount_total)  }}</td>
                  <td>${{  @\App\Models\Payment::format(0.00)  }}</td>
                  <td>${{  @\App\Models\Payment::format(0.00)  }}</td>
                  <!-- <td><span class="doc_type_m">{{  @implode(',',$vendors) }}</span></td> -->
                  <td>${{  \App\Models\Payment::format(@@$tPay->payment_amount_total)  }}</td>
                  <td><span class="doc_type_m">
                       @if(request()->route()->getName()  == 'projects.show')
                    <a class="disable-anchor" href="{{ url("projects/$project->id").'?to=Total Construction Cost&url='.urlencode(url()->current().'#construction-cost').'&payment_vendor='.$vendorId.'#payments'}} "> ${{ \App\Models\Payment::format(@@$tPay->payment_amount_total) }} </a>

                    @else
                   ${{ \App\Models\Payment::format(@$tPay->payment_amount_total) }}
                    @endif

                  </span></td>
                  <td>${{ \App\Models\Payment::format(@$tPay->retainage_held) }} </td> 
                  <td>${{ \App\Models\Payment::format(0.00) }} </td> 
                  <td>${{ \App\Models\Payment::format(0.00) }} </td> 
                  <td>${{ \App\Models\Payment::format(0.00) }} </td> 
                  <td> 100 % </td> 
                  <!-- <td>{{ trim(@$notes) }}</td>  -->
                </tr>

                <tr>
                  <td colspan="3" style="padding:10px;"></td>
                  <td><span class="doc_type_m">{{ @$tPay->vendor->name }} {{ 
                (@$tPay->material) ? '('.@$tPay->material->name .')' : ""}}</span></td>
                  <td></td>
                  <td></td>
                  <!-- <td><span class="doc_type_m">{{ @trim($payment_vendors,',') }}</span></td> -->
                  <td colspan="7" style="padding:10px;"></td>
                  <!-- <td colspan="4" style="padding:10px;"></td> -->
                </tr>
              @endforeach

            @endif

         @endforeach

            <tr>
                 <td class="text-danger h6 text-center" colspan="2">
                 <b>{{ $cat->name }} Total </b>
                 </td>
                  <td><b>${{ \App\Models\Payment::format($catTradeTotal) }}</b></td>
                  <td><b>${{ \App\Models\Payment::format($catMaterialTotal + $catVendorsTotal) }}</b></td>
                  <td><b>${{ \App\Models\Payment::format($catLabourTotal) }}</b></td>
                  <td><b>${{ \App\Models\Payment::format($catSubcontractorTotal) }}</b></td>
                  <!-- <td></td> -->
                  <td><b>${{ \App\Models\Payment::format($catGrandTotal + $catVendorsTotal) }}</b></td>
                  <td><b>${{ \App\Models\Payment::format($catPaidTotal + $catVendorsTotal) }}</b></td>
                  <td><b>${{ \App\Models\Payment::format($catHeldTotal + $catVendorsHeldTotal) }}</b></td>
                  <td><b>${{ \App\Models\Payment::format( $catDueTotal - $catHeldTotal) }} </b></td> 
                  <td><b>${{ \App\Models\Payment::format($catDueTotal) }} </b></td> 
                  <td><b>${{ \App\Models\Payment::format($catTradeTotal - $catGrandTotal) }} </b></td> 
                  <td><b>{{ ($catGrandTotal && $catPaidTotal) || ($catVendorsTotal) ? sprintf('%0.2f', (@$catPaidTotal + $catVendorsTotal + @$catHeldTotal) / (@$catGrandTotal + $catVendorsTotal) * 100) : 0 }} % </b></td>
                  <!-- <td></td> -->
           </tr>

           <tr>
            <td colspan="13" style="padding:10px;"></td>
           </tr>

        @endforeach

         @php
         $extraTotal = 0;
         @endphp

        @if($vendors) 

        
          
          <!--  <tr>
               <td colspan="2"><b>Extra Material</b></td>
               <td colspan="8" style="padding:10px;"></td>
               <!-- <td></td> -->
               <!-- <td></td>
           </tr> -->

           @foreach($vendors as $k => $vndr)
              @php
                $extraTotal = $extraTotal + $vndr;
              @endphp
          <!--   <tr>
               <td></td>
               <td>{{ $k}}</td>
               <td>${{ @\App\Models\Payment::format($vndr)}}</td>
               <td colspan="7" style="padding:10px;"></td>
           </tr> -->

           @endforeach
        <!--  
         <tr>
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

             <tr class="row-border">
               <td><b> Construction Project Total</b></td>
               <td></td>
               <td><b>${{ \App\Models\Payment::format($tradeTotal)}}</b></td>
               <td><b>${{ \App\Models\Payment::format($materialTotal + $vendorsTotal)}}</b></td>
               <td><b>${{ \App\Models\Payment::format($labourTotal) }}</b></td>
               <td><b>${{ \App\Models\Payment::format($subcontractorTotal) }}</b></td>
               <!-- <td></td> -->
               <td><b>${{ \App\Models\Payment::format($grandTotal + $vendorsTotal) }}</b></td>
               <td><b>${{ \App\Models\Payment::format($paidTotal + $vendorsTotal) }}</b></td>
               <td><b>${{ \App\Models\Payment::format($heldTotal + $vendorsHeldTotal) }}</b></td>
               <td><b>${{ \App\Models\Payment::format($dueTotal - $heldTotal) }}</b></td>
               <td><b>${{ \App\Models\Payment::format($dueTotal ) }}</b></td>
               <td><b>${{ \App\Models\Payment::format($tradeTotal - $grandTotal) }}</b></td>

               <td><b>{{ ($paidTotal && $grandTotal)  || ($vendorsTotal) ? sprintf('%0.2f', (@$paidTotal + $vendorsTotal + @$heldTotal)/ (@$grandTotal + $vendorsTotal) * 100) : 0 }} % </b></td>
               <!-- <td ></td> -->
            
           </tr>

        
        <tr>
          <td colspan="11" class="h6 text-center" style="padding:10px;">
            <b> FFE Budget </b> 
          </td>
         </tr>

         @php   

         $ffeMaterialTotal = 0;
         $ffeTradeTotal = 0;
         $ffeBudgetDiff = 0;
         $ffeLabourTotal = 0;
         $ffeSubcontractorTotal = 0;
         $ffeGrandTotal = 0;
         $ffePaidTotal = 0;
         $ffeDueTotal = 0;
         $ffeVendorsTotal = 0;
         $ffeChangeOrderTotal = 0;

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
            <tr >
              <td>{{ $cat->account_number }}</td>
              <td class="text-danger h6 text-center">
                 <b>{{ $cat->name }}</b>
              </td>
              <td  colspan="11"></td>
            </tr>
         @foreach($catTrades as $trd)

              @php
               $ffeChangeOrderTotal = 0;
              $bids = @$project->ffe_proposals()->trade($trd->id)->IsAwarded()
                     ->get();
              $tradePayments = @$project->ffe_payments()->where('non_contract','1')
              ->selectRaw('sum(payment_amount) as payment_amount_total, f_f_e_vendor_id')
               ->where('f_f_e_trade_id',$trd->id)
               ->groupBy('f_f_e_vendor_id')
               ->get();


              @endphp
            
             @if($bids->count() > 0)

            <tr >
              <td>{{ $trd->account_number }}</td>
              <td >
                 <span class="text-center" style="width: 15%;">{{ $trd->name  }}</span>
              </td>
             
              @foreach($bids as $bid)
                @php    
                  $bidTotal =  (float) @$bid->material + (float) @$bid->labour_cost + (float) @$bid->subcontractor_price;   
                      foreach(@$bid->changeOrders as $k => $order){
                       if($order->type == \App\Models\FFEChangeOrder::ADD ){
                         $bidTotal += $order->subcontractor_price;
                         $ffeChangeOrderTotal += $order->subcontractor_price;
                         $catSubcontractorTotal += $order->subcontractor_price;
                       }
                       else{
                         $bidTotal -= $order->subcontractor_price;
                         $ffeChangeOrderTotal -= $order->subcontractor_price;
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

                      
                      $ffeMaterialTotal = (float) @$bid->material + $ffeMaterialTotal;
                      $ffeTradeTotal = (float) @$bid->trade_budget + $ffeTradeTotal;
                      $ffeLabourTotal = (float) @$bid->labour_cost + $ffeLabourTotal;
                      $ffeSubcontractorTotal = (float) @$bid->subcontractor_price + $ffeSubcontractorTotal + $ffeChangeOrderTotal;
                      $ffeGrandTotal = (float) @$bidTotal + $ffeGrandTotal;
                      $ffePaidTotal = (float) @$paid + $ffePaidTotal;
                      $ffeDueTotal = (float) @$due + $ffeDueTotal;
                      //$ffeBudgetDiff = (float) @$bid->trade_budget - $bidTotal + $ffeBudgetDiff;
                      
                      $catSubcontractorTotal = (float) @$bid->subcontractor_price + $catSubcontractorTotal;
                      $catMaterialTotal = (float) @$bid->material + $catMaterialTotal;
                      $catTradeTotal = (float) @$bid->trade_budget + $catTradeTotal;
                      $catLabourTotal = (float) @$bid->labour_cost + $catLabourTotal;
                      $catGrandTotal = (float) @$bidTotal + $catGrandTotal;
                      $catPaidTotal = (float) @$paid + $catPaidTotal;
                      $catDueTotal = (float) @$due + $catDueTotal;
                      // $catbudgetDiff = (float) @$bid->trade_budget - @$bidTotal +  @$catbudgetDiff;

                @endphp

                  <td>${{  @\App\Models\Payment::format(@$bid->trade_budget)  }}</td>
                  <td>${{  @\App\Models\Payment::format($bid->material)  }}</td>
                  <td> </td>
                  <td>${{  @\App\Models\Payment::format($bid->subcontractor_price)  }}</br> <span class="doc_type_m">{{ ($ffeChangeOrderTotal > 0) ? 'Change Orders - $'. @\App\Models\Payment::format($ffeChangeOrderTotal) : ''  }}</span></td>
                  <!-- <td><span class="doc_type_m">{{  @implode(',',$vendors) }}</span></td> -->
                  <td>${{  \App\Models\Payment::format($bidTotal)  }}</td>
                  <td>${{ \App\Models\Payment::format($paid) }}</td>
                  <td></td> 
                  <td> </td> 
                  <td>${{ \App\Models\Payment::format($due) }} </td> 
                  <td>${{ \App\Models\Payment::format((float) @$bid->trade_budget - $bidTotal) }} </td> 
                  <td>{{ ($paid && $bidTotal) ?  sprintf('%0.2f', @$paid /@$bidTotal * 100)  : 0 }}
                   % </td> 
                  <!-- <td>{{ trim(@$notes) }}</td>  -->
                </tr>

                <tr>
                  <td colspan="2" style="padding:10px;"></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td><span class="doc_type_m">{{ @$bid->vendor->name }}</span></td>
                  <!-- <td><span class="doc_type_m">{{ @trim($payment_vendors,',') }}</span></td> -->
                  <td colspan="7" style="padding:10px;"></td>
                  <!-- <td colspan="4" style="padding:10px;"></td> -->
                </tr>


              @endforeach

              @endif

            @if($tradePayments->count() > 0)
            
              @foreach($tradePayments as $tPay)

               @php
                $ffeVendorsTotal = $ffeVendorsTotal + $tPay->payment_amount_total;
                $catVendorsTotal = $catVendorsTotal + $tPay->payment_amount_total;
               @endphp

                  <tr>
                    <td>{{ $trd->account_number }}</td>
                    <td >
                       <span class="text-center" style="width: 15%;">{{ $trd->name  }}</span>
                    </td>
                    <td ></td>
                   
                  <td>${{  @\App\Models\Payment::format(@$tPay->payment_amount_total)  }}</td>
                  <td></td>
                  <td>${{  @\App\Models\Payment::format(0.00)  }}</td>
                  <!-- <td><span class="doc_type_m">{{  @implode(',',$vendors) }}</span></td> -->
                  <td>${{  \App\Models\Payment::format(@$tPay->payment_amount_total)  }}</td>
                  <td>${{ \App\Models\Payment::format(@$tPay->payment_amount_total) }}</td>
                  <td></td> 
                  <td> </td> 
                  <td>${{ \App\Models\Payment::format(0.00) }} </td> 
                  <td>${{ \App\Models\Payment::format(0.00) }} </td> 
                  <td> 100 % </td> 
                  <!-- <td>{{ trim(@$notes) }}</td>  -->
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
                  <td colspan="6" style="padding:10px;"></td>
                </tr>
              @endforeach

            @endif

         @endforeach

            <tr>
                 <td class="text-danger h6 text-center" colspan="2">
                 <b>{{ $cat->name }} Total </b>
                 </td>
                  <td><b>${{ \App\Models\Payment::format($catTradeTotal) }}</b></td>
                  <td><b>${{ \App\Models\Payment::format($catMaterialTotal + $catVendorsTotal) }}</b></td>
                  <td></td>
                  <td><b>${{ \App\Models\Payment::format($catSubcontractorTotal) }}</b></td>
                  <!-- <td></td> -->
                  <td><b>${{ \App\Models\Payment::format($catGrandTotal + $catVendorsTotal) }}</b></td>
                  <td><b>${{ \App\Models\Payment::format($catPaidTotal + $catVendorsTotal) }}</b></td>
                   <td></td> 
                   <td> </td> 
                   <td><b>${{ \App\Models\Payment::format($catDueTotal) }} </b></td> 
                 
                  <td><b>${{ \App\Models\Payment::format($catTradeTotal - $catGrandTotal) }} </b></td> 
                  <td><b>{{ ($catGrandTotal && $catPaidTotal) || ($catVendorsTotal) ? sprintf('%0.2f', (@$catPaidTotal + $catVendorsTotal) / (@$catGrandTotal + $catVendorsTotal) * 100) : 0 }} % </b></td>
                  <!-- <td></td> -->
           </tr>

           <tr>
            <td colspan="13" style="padding:10px;"></td>
           </tr>

        @endforeach

         @php
         $extraTotal = 0;
         @endphp

        @if($vendors) 

        
          
          <!--  <tr>
               <td colspan="2"><b>Extra Material</b></td>
               <td colspan="8" style="padding:10px;"></td>
               <!-- <td></td> -->
               <!-- <td></td>
           </tr> -->

           @foreach($vendors as $k => $vndr)
              @php
                $extraTotal = $extraTotal + $vndr;
              @endphp
          <!--   <tr>
               <td></td>
               <td>{{ $k}}</td>
               <td>${{ @\App\Models\Payment::format($vndr)}}</td>
               <td colspan="7" style="padding:10px;"></td>
           </tr> -->

           @endforeach
        <!--  
         <tr>
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

          
            <tr class="row-border">
               <td><b>FFE Total</b></td>
               <td></td>
               <td><b>${{ \App\Models\Payment::format($ffeTradeTotal)}}</b></td>
               <td><b>${{ \App\Models\Payment::format($ffeMaterialTotal + $ffeVendorsTotal)}}</b></td>
               <td></td>
               <td><b>${{ \App\Models\Payment::format($ffeSubcontractorTotal) }}</b></td>
               <!-- <td></td> -->
               <td><b>${{ \App\Models\Payment::format($ffeGrandTotal + $ffeVendorsTotal) }}</b></td>
               <td><b>${{ \App\Models\Payment::format($ffePaidTotal + $ffeVendorsTotal) }}</b></td>
               <td></td> 
                <td> </td> 
               <td><b>${{ \App\Models\Payment::format($ffeDueTotal) }}</b></td>
               
               <td><b>${{ \App\Models\Payment::format($ffeTradeTotal - $ffeGrandTotal) }}</b></td>

               <td><b>{{ ($ffePaidTotal && $ffeGrandTotal)  || ($ffeVendorsTotal) ? sprintf('%0.2f', (@$ffePaidTotal + $ffeVendorsTotal)/ (@$ffeGrandTotal + $ffeVendorsTotal) * 100) : 0 }} % </b></td>
            
           </tr>
           <tr>
             <td colspan="13"> </td>
           </tr>
           <!-- <tr>
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
               <!-- <td></td> -->
               <!-- <td></td>
           </tr> -->
            <tr class="row-border">
               <td><b>Project Grand Total</b></td>
               <td></td>
               <td><b>${{ \App\Models\Payment::format($ffeTradeTotal + $tradeTotal)}}</b></td>
               <td><b>${{ \App\Models\Payment::format($ffeMaterialTotal + $ffeVendorsTotal + $materialTotal + $vendorsTotal)}}</b></td>
               <td><b>${{ \App\Models\Payment::format($labourTotal) }}</b></td>
               <td><b>${{ \App\Models\Payment::format($ffeSubcontractorTotal + $subcontractorTotal) }}</b></td>
               <!-- <td></td> -->
               <td><b>${{ \App\Models\Payment::format($ffeGrandTotal + $ffeVendorsTotal + $grandTotal + $vendorsTotal) }}</b></td>
               <td><b>${{ \App\Models\Payment::format($ffePaidTotal + $ffeVendorsTotal + $paidTotal + $vendorsTotal) }}</b></td>
               <td><b>${{ \App\Models\Payment::format($heldTotal + $vendorsHeldTotal) }}</b></td>
               <td><b>${{ \App\Models\Payment::format($dueTotal - $heldTotal) }}</b></td>
               <td><b>${{ \App\Models\Payment::format($ffeDueTotal + $dueTotal) }}</b></td>

               <td><b>${{ \App\Models\Payment::format($ffeTradeTotal - $ffeGrandTotal + $tradeTotal - $grandTotal) }}</b></td>

               <td><b>{{ (($ffePaidTotal && $ffeGrandTotal) ||  ($paidTotal && $grandTotal))  || ($vendorsTotal || $ffeVendorsTotal) ? sprintf('%0.2f', (@$ffePaidTotal + $ffeVendorsTotal + @$paidTotal + $vendorsTotal)/ (@$ffeGrandTotal + $ffeVendorsTotal + @$grandTotal + $vendorsTotal) * 100) : 0 }} % </b></td>
           </tr>

           

         

            </tbody>
            <thead>
            <tr class="text-danger">
                <th></th>
                <th ></th>
                <th >Trade Budget</th>
                <th>Material</th>
                <th>Labor</th>
                <th>Subcontractor</th>
                <!-- <th>Vendors</th> -->
                <th>Total </th>
                <th>Paid - Retainage</th>
                 <th>Retainage Held </th>
                <th >Remaining - Retainage</th>
                <th>Remaining + Retainage</th>
                <th >Budget Diff  </th>
                <th> %Complete </th>
                <!-- <th> Notes </th> -->
            </tr>
            </thead>
        </table>
</div>
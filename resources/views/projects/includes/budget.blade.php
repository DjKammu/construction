 <div class="tab-pane" id="budget" role="tabpanel" aria-expanded="true">
   <div class="row mb-2">
        <div class="col-12">
            <h4 class="mt-0 text-left">{{ @$project->name }} - Budget </h4>
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
                <th>Total </th>
                <th>Total Paid</th>
                <th>Remaining Payment </th>
                <th> % Complete </th>
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
                         ->has('payment')->get();
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
                  $bidTotal =  (int) @$bid->material + (int) @$bid->labour_cost + (int) @$bid->subcontractor_price;   
                     foreach(@$bid->changeOrders as $k => $order){
                       if($order->type == \App\Models\ChangeOrder::ADD ){
                         $bidTotal += $order->subcontractor_price;
                       }
                       else{
                         $bidTotal -= $order->subcontractor_price;
                       }
                     }
                     
                       $bidPayments =   $bid->payment;

                       $payment_vendors = @collect($bidPayments)->map(function($p){
                           return $p->vendor->name;
                       })->unique()->join(',');
                        
                      $paid =  (int) @$bid->payment()->sum('payment_amount');
                      $due =  (int) @$bidTotal  - (int) $paid;

                      $materialTotal = (int) @$bid->material + $materialTotal;
                      $labourTotal = (int) @$bid->labour_cost + $labourTotal;
                      $subcontractorTotal = (int) @$bid->subcontractor_price + $subcontractorTotal;
                      $grandTotal = (int) @$bidTotal + $grandTotal;
                      $paidTotal = (int) @$paid + $paidTotal;
                      $dueTotal = (int) @$due + $dueTotal;

                      $catGrandTotal = (int) @$bidTotal + $catGrandTotal;
                      $catPaidTotal = (int) @$paid + $catPaidTotal;
                      $catDueTotal = (int) @$due + $catDueTotal;

                @endphp

                  <td>${{ (int) @$bid->material  }}</td>
                  <td>${{ (int) @$bid->labour_cost  }}</td>
                  <td>${{ (int) @$bid->subcontractor_price  }}</td>
                  <td>${{ (int) @$bidTotal  }}</td>
                  <td>${{ $paid }}</td>
                  <td>${{ $due }} </td> 
                  <td>{{ sprintf('%0.2f', $paid /@$bidTotal * 100) }} % </td> 
                </tr>

                <tr>
                  <td colspan="2" style="padding:20px;"></td>
                  <td><span class="doc_type_m">{{ @$payment_vendors }}</span></td>
                  <td></td>
                  <td><span class="doc_type_m">{{ @$bid->subcontractor->name }}</span></td>
                  <td colspan="4" style="padding:20px;"></td>
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
                  <td>${{ (int) @$catGrandTotal  }}</td>
                  <td>${{ $catPaidTotal }}</td>
                  <td>${{ $catDueTotal }} </td> 
                  <td colspan="2" style="padding:20px;"></td>
           </tr>

           <tr>
            <td colspan="9" style="padding:20px;"></td>
           </tr>

        @endforeach

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
           </tr>

           <tr>
               <td><b>Project Total</b></td>
               <td></td>
               <td><b>${{ $materialTotal }}</b></td>
               <td><b>${{ $labourTotal }}</b></td>
               <td><b>${{ $subcontractorTotal }}</b></td>
               <td><b>${{ $grandTotal }}</b></td>
               <td><b>${{ $paidTotal }}</b></td>
               <td><b>${{ $dueTotal }}</b></td>
               <td></td>
           </tr>

            </tbody>
        </table>
</div>

</div>
</div>
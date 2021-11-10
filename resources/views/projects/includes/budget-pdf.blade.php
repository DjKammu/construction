<div class="table-responsive">
   <h4 class="mt-0 text-center">Project {{ @$project->name }} </h4>
    <table id="project-types-table" class="table table-hover text-center payments-table">
            <thead>
            <tr class="text-danger">
                <th>Item No.</th>
                <th >Category&Trade</th>
                <th>Material</th>
                <th>Labor</th>
                <th>Subcontractor</th>
                <th>Vendors</th>
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
              <td  colspan="8"></td>
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
                  $bidTotal =  (float) @$bid->material + (float) @$bid->labour_cost + (float) @$bid->subcontractor_price;   
                     foreach(@$bid->changeOrders as $k => $order){
                       if($order->type == \App\Models\ChangeOrder::ADD ){
                         $bidTotal += $order->subcontractor_price;
                       }
                       else{
                         $bidTotal -= $order->subcontractor_price;
                       }
                     }
                     
                       $bidPayments =   $bid->payment;

                       $payment_vendors = $vendors_amount = [];

                       $payment_vendors = @collect($bidPayments)->map(function($p) {
                              if($p->vendor){
                                return $p->vendor->name;
                              }
                       })->unique()->join(',');

                       @collect($bidPayments)->each(function($p) use (&$vendors_amount){
                           if($p->vendor){
                              $amount = (double) \App\Models\Payment::format($p->payment_amount);
                              if(isset( $vendors_amount[$p->vendor->name])){
                                   $amount = $vendors_amount[$p->vendor->name] +
                                     \App\Models\Payment::format($p->payment_amount);   
                              }
                              $vendors_amount[$p->vendor->name] = (double) $amount;  
                             }
                       });
                        

                       $notes = @$bid->payment()->whereNotNull('notes')->pluck('notes')->join(',');
                      
                        
                      $paid =  (float) @$bid->payment()->sum('payment_amount');
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
                   <td><span class="doc_type_m">{{ (is_array($vendors_amount)) ? @implode($vendors_amount,',') : '' }}</span></td>
                  <td>${{ (float) @\App\Models\Payment::format($bidTotal)  }}</td>
                  <td>${{ \App\Models\Payment::format($paid) }}</td>
                  <td>${{ \App\Models\Payment::format($due) }} </td> 
                  <td>{{ sprintf('%0.2f', $paid /@$bidTotal * 100) }} % </td> 
                  <!-- <td>{{ trim(@$notes) }}</td>  -->
                </tr>

                <tr>
                  <td colspan="2" style="padding:10px;"></td>
                  <td></td>
                  <td></td>
                  <td><span class="doc_type_m">{{ @$bid->subcontractor->name }}</span></td>
                  <td><span class="doc_type_m">{{ @trim($payment_vendors,',') }}</span></td>
                  <td colspan="4" style="padding:10px;"></td>
                  <!-- <td colspan="4" style="padding:10px;"></td> -->
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
                  <td></td>
                  <td><b>${{ (float) @\App\Models\Payment::format($catGrandTotal ) }}</b></td>
                  <td><b>${{ \App\Models\Payment::format($catPaidTotal) }}</b></td>
                  <td><b>${{ \App\Models\Payment::format($catDueTotal) }} </b></td> 
                  <td><b>{{ ($catGrandTotal && $catPaidTotal) ? sprintf('%0.2f', @$catPaidTotal / @$catGrandTotal * 100) : 0 }} % </b></td>
                  <!-- <td></td> -->
           </tr>

           <tr>
            <td colspan="11" style="padding:10px;"></td>
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
               <td></td>
               <!-- <td></td> -->
           </tr>

           <tr>
               <td><b>Project Total</b></td>
               <td></td>
               <td><b>${{ \App\Models\Payment::format($materialTotal )}}</b></td>
               <td><b>${{ \App\Models\Payment::format($labourTotal) }}</b></td>
               <td><b>${{ \App\Models\Payment::format($subcontractorTotal) }}</b></td>
               <td></td>
               <td><b>${{ \App\Models\Payment::format($grandTotal) }}</b></td>
               <td><b>${{ \App\Models\Payment::format($paidTotal) }}</b></td>
               <td><b>${{ \App\Models\Payment::format($dueTotal) }}</b></td>
               <td><b>{{ ($paidTotal && $grandTotal) ? sprintf('%0.2f', @$paidTotal / @$grandTotal * 100) : 0 }} % </b></td>
               <!-- <td ></td> -->
            
           </tr>

            </tbody>
        </table>

        <p class="footer-text"> Date - {{ \Carbon\Carbon::now() }}</p>
</div>


<style type="text/css">
  
table.payments-table{
      font-size: 12px;
      font-family: Arial;
      border-bottom: 1px solid #dee2e6;
      border-right: 1px solid #dee2e6;
      border-left: 1px solid #dee2e6;
}

table.payments-table thead>tr>th{
   font-size: 12px;
}
.text-center {
    text-align: center!important;
}

.footer-text {
    width: 100%;
    font-size: 10px;
    text-align: right!important;
}
.table {
    width: 100%;
    margin-bottom: 1rem;
    color: #212529;
}
.table td, .table th {
    padding: 5px;
    border-top: 1px solid #dee2e6;
}

b, strong {
    font-weight: bolder;
}

</style>
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

         @php   
         $remainingTotal = $grandTotal =$paidTotal = 0;
         @endphp

              <tr>
                <td colspan=""></td>
                <td colspan="2">{{ @$project->name }}</td>
                <td></td>
                <td colspan="2"> {{ \Carbon\Carbon::now()->format('m-d-Y') }} </td>
                <td></td>
                <td></td>
                <td></td>
              </tr> 
            
            <tr class="text-danger">
                <th >Date </th>
                <th>Trade</th>
                <th>Subcontractor/Vendor</th>
                <th>Amount Paid</th>
                <th>Contract Amount </th>
                <th>Remaining Amount </th>
            </tr>
            </thead>
            <tbody>

              @foreach($payments as $payment)

             @php
                
                $paidTotal = (float) @$payment->payment_amount + $paidTotal;
                $remainingTotal = (float) @$payment->remaining;
                
             @endphp
             <tr>
               <td> {{ @$payment->date }}</td>
               <td> {{ @$payment->trade->name }}</td>
               <td> {{ (@$payment->vendor ) ? @$payment->vendor->name. 
                ' (Vendor)' :  @$payment->subcontractor->name }}</td>
               <td> ${{ \App\Models\Payment::format($payment->payment_amount) }}</td>

               <td> {{ (@$payment->vendor ) ? '-' :  '$'.\App\Models\Payment::format($payment->total_amount) }}</td>
               <td>  {{ (@$payment->vendor ) ? '-' :  '$'.\App\Models\Payment::format($payment->remaining) }} </td>
            
             </tr> 
             @endforeach

             @php
             $trades = ($payments) ? @$payments->unique('trade_id') : '';
             $grandTotal = ($trades) ? @$trades->sum('total_amount') : 0;

              $awardedBids = @$project->proposals()->IsAwarded()
                              ->where('subcontractor_id', @request()->sc)->get();
              $bidTotal = 0; 
              foreach($awardedBids as $awarded){
                 $bidTotal =  $bidTotal + (float) @$awarded->material + (float) @$awarded->labour_cost + (float) @$awarded->subcontractor_price;

                  foreach(@$awarded->changeOrders as $k => $order){
                       if($order->type == \App\Models\ChangeOrder::ADD ){
                         $bidTotal += $order->subcontractor_price;
                       }
                       else{
                         $bidTotal -= $order->subcontractor_price;
                       }
                     }
              } 



              $remainingTotal = ($remainingTotal == 0) ? $bidTotal : $remainingTotal;                

             @endphp
             <tr>
              <td colspan="2"></td>
              <td><b>Total</b></td>
              <td ><b>${{ \App\Models\Payment::format($paidTotal) }}</b></td>
              <td ><b>${{ \App\Models\Payment::format($bidTotal) }}</b></td>
              <!-- <td><b>${{ \App\Models\Payment::format($grandTotal) }}</b></td> -->
              <td colspan="2"> {!! (@request()->sc) ? '<b>$'.\App\Models\Payment::format($remainingTotal).'</b>' : '' !!} </td>
              <td></td>
            </tr>


            </tbody>
        </table>
</div>
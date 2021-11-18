<html>
    <head>
        <style>
            /** Define the margins of your page **/
            @page {
                margin: 100px 25px;
            }

            header {
                position: fixed;
                top: -60px;
                left: 0px;
                right: 0px;
                height: 50px;


                /** Extra personal styles **/
                text-align: center;
                line-height: 35px;
            }

            footer {
                position: fixed; 
                bottom:0px;
                text-align: right;
                font-size: 12px;
            }

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
                 font-size: 12px;
                 text-align: right!important;
                 position:absolute;
                 bottom:0;
                 right:0;
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
             
             .pagenum:before {
                    content: counter(page);
            }

        </style>
    </head>
    <body>
        <!-- Define header and footer blocks before your content -->
        <header>
            <h4>{{ @$project->name }}</h4>
        </header>

        <footer>
            {{ \Carbon\Carbon::now()->format('m-d-Y') }} - Page <span class="pagenum"></span>
        </footer>

        <main>

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

                  <td>${{  @\App\Models\Payment::format($bid->material)  }}</td>
                  <td>${{  @\App\Models\Payment::format($bid->labour_cost)  }}</td>
                  <td>${{  @\App\Models\Payment::format($bid->subcontractor_price)  }}</td>
                  <!-- <td><span class="doc_type_m">{{  @implode(',',$vendors) }}</span></td> -->
                  <td>${{  \App\Models\Payment::format($bidTotal)  }}</td>
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
                  <!-- <td><span class="doc_type_m">{{ @trim($payment_vendors,',') }}</span></td> -->
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
                  <!-- <td></td> -->
                  <td><b>${{ \App\Models\Payment::format($catGrandTotal ) }}</b></td>
                  <td><b>${{ \App\Models\Payment::format($catPaidTotal) }}</b></td>
                  <td><b>${{ \App\Models\Payment::format($catDueTotal) }} </b></td> 
                  <td><b>{{ ($catGrandTotal && $catPaidTotal) ? sprintf('%0.2f', @$catPaidTotal / @$catGrandTotal * 100) : 0 }} % </b></td>
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

        
          
           <tr>
               <td colspan="2"><b>Extra</b></td>
               <td colspan="8" style="padding:10px;"></td>
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
               <td>${{ @\App\Models\Payment::format($vndr)}}</td>
               <td colspan="7" style="padding:10px;"></td>
           </tr>

           @endforeach
         
         <tr>
               <td class="text-danger h6 text-center" colspan="2">
               <b>Extra Total </b>
               </td>
               <td><b>${{ \App\Models\Payment::format($extraTotal )}}</b></td>
               <td colspan="7" style="padding:10px;"></td>
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
               <td><b>${{ \App\Models\Payment::format($materialTotal + $extraTotal)}}</b></td>
               <td><b>${{ \App\Models\Payment::format($labourTotal) }}</b></td>
               <td><b>${{ \App\Models\Payment::format($subcontractorTotal) }}</b></td>
               <!-- <td></td> -->
               <td><b>${{ \App\Models\Payment::format($grandTotal + $extraTotal) }}</b></td>
               <td><b>${{ \App\Models\Payment::format($paidTotal + $extraTotal) }}</b></td>
               <td><b>${{ \App\Models\Payment::format($dueTotal) }}</b></td>
               <td><b>{{ ($paidTotal && $grandTotal) ? sprintf('%0.2f', @$paidTotal / @$grandTotal * 100) : 0 }} % </b></td>
               <!-- <td ></td> -->
            
           </tr>

            </tbody>
        </table>
</div>

 </main>
    </body>
</html>
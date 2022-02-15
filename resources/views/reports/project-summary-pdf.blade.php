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
            .text-left{
              text-align: left !important;
            }

        </style>
    </head>
    <body>
        <!-- Define header and footer blocks before your content -->
        <header>
            <h4>Project Summary </h4>
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
                <td colspan="2">{{ @$project->name }}<</td>
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
</div>

 </main>
    </body>
</html>
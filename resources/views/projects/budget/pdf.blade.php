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
                  font-size: 10px;
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
              table-layout: fixed;
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
            <h4>{{ @$project->name }} Construction Budget </h4>
        </header>

        <footer>
            {{ \Carbon\Carbon::now()->format('m-d-Y') }} - Page <span class="pagenum"></span>
        </footer>

        <main>
          <table id="project-types-table" class="table table-hover text-center payments-table">
                <thead>
                <tr class="text-danger">
                    <th style="width: 80px;">Item No. 
                </th>
                    <th>Trade</th>
                    <!-- <th>Price /Sq Ft </th> -->
                    <th>Budget </th>
                </tr>
                </thead>
                <tbody>
                   @foreach($lines as $line)
                  <tr>
                    <td>
                   {{ $line['account_number'] }}
                    <td> 
                    {{ $line['trade']}}</td>
                    <!-- <td> {{ $line['price_sq_ft'] }}</td> -->
                    <td> {{ $line['budget'] }}</td>
                  </tr>
                  @endforeach
                  
                  <tr>
                    <td colspan="3"></td>
                  </tr>
                  <tr>
                    <!-- <td></td> -->
                    <th>Total</th>
                    <th>{{$total_price_sq_ft}}</th>
                    <th>$ {{ number_format($total_budget,2)}}</th>
                  </tr>
                  <tr>
                    <td colspan="3"></td>
                  </tr>
                  <tr>
                    <!-- <th></th> -->
                    <th >Total Budget / Sq Ft </th>
                    <th></th>
                    <th>Total Budget / Hotel Keys </th>
                  </tr>
                  <tr>
                    <!-- <th> </th> -->
                    <th>{{number_format(($total_budget / @$project->total_construction_sq_ft),2)}}</th>
                    <th></th>
                    <th>${{(@$project->hotel_keys != 0) ?  number_format(($total_budget / @$project->hotel_keys), 2) : (0) }}</th>
                  </tr>
                </tbody>
                </table> 
       </main>
    </body>
</html>
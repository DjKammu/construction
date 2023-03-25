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
            <h4>{{ @$project->name }}</h4>
        </header>

        <footer>
            {{ \Carbon\Carbon::now()->format('m-d-Y') }} - Page <span class="pagenum"></span>
        </footer>

        <main>
          @include('projects.ffe.budget-content')
       </main>
    </body>
</html>
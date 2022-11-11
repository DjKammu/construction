
<div class="table-responsive table-payments">
       
       <table id="project-types-table" class="table table-hover text-center payments-table">
            <thead>
            <tr class="text-danger">
                <th >Date </th>

                <th >Item</th>
                <th >PO Sent </th> 
                <th >Date Shipped </th>
                 <th >Date Received </th>

                <th>Vendor</th>
                <!-- <th>Subcontractor</th> -->
                <th>Lead Time</th>
                <th>Tentative Date Delivery</th>
            </tr>
            </thead>
            <tbody>
              @foreach($logs as $log)

             <tr>
               <td> {{ @$log->date }}</td>
               <td> {{ @$log->item }}</td>
               <td> {{ @$log->po_sent }}</td>
               <td> {{ @$log->date_shipped }}</td>
               <td> {{ @$log->date_received }}</td>
               <td> {{ @$log->vendor->name }}</td>
               <td> {{ @$log->lead_time }}</td>
               <td> {{ @$log->tentative_date_delivery }}</td>
             
            <td>{{ @$log->status->name }}</td>
          
             </tr> 
             @endforeach
            <!-- Project Types Go Here -->
            </tbody>
        </table>

</div>
<table>
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

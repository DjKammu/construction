<div class="table-responsive">
   <!-- <h4 class="mt-0 text-center">  </h4> -->
    <table id="awarded-contracts" class="table table-hover text-center payments-table">
            <thead>
            <tr class="text-danger">
                <th>Awarded  Contract</th>
                <th>Pending Contract</th>
            </tr>
            </thead>
            <tbody>
              <tr class="text-danger">
                <th><a target="_blank" href="{{ url('contracts-reports').'?t=awarded&p='.request()->p}}" ><img src="/img/pdf.png"></a></th>
                <th><a target="_blank" href="{{ url('contracts-reports').'?t=pending&p='.request()->p}}" ><img src="/img/pdf.png"></a></th>
              </tr>
            </tbody>
        </table>
</div>

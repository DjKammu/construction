<style type="text/css">
span.doc-type{
 font-size: 12px;
 padding-top: 8px;
 display: block;
}
span.doc_type_m{
 font-size: 10px;
 padding-top: 3px;
 display: block;
}
table.payments-table{
      font-size: 12px;
      font-family: Arial;
}

table.payments-table thead>tr>th{
   font-size: 12px;
}
</style>

<div class="table-responsive">
   <!-- <h4 class="mt-0 text-center">  </h4> -->
    <table id="project-types-table" class="table table-hover text-center payments-table">
            <thead>
            <tr class="text-danger">
                <th>No.</th>
                <th >Project </th>
                <th>Property</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>

          

            @foreach($projects as $project)
              <tr>
              <td>{{ $project->id }}</td>
                <td ><a   href="{{ url('projects/'.$project->id )}}">
                  {{ $project->name }} </a>  </td>
                <td>{{ @$project->project_type->name }}</td>
                <td>{{ @$project->p_status->name }}</td>
               </tr>
            @endforeach

          

            </tbody>
        </table>
</div>

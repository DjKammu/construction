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
                <th>Property <span class="sorting-outer">
                  <a href="javascript:void(0)" onclick="sortOrderByStatus('property', 'ASC')">
                    <i class="fa fa-sort-asc" o ></i></a>
                  <a href="javascript:void(0)" onclick="sortOrderByStatus('property', 'DESC')">
                    <i class="fa fa-sort-desc"></i> </a>
                </span></th>
                <th >Project <span class="sorting-outer">
                  <a href="javascript:void(0)" onclick="sortOrderByStatus('project', 'ASC')">
                    <i class="fa fa-sort-asc" o ></i></a>
                  <a href="javascript:void(0)" onclick="sortOrderByStatus('project', 'DESC')">
                    <i class="fa fa-sort-desc"></i> </a>
                </span></th>
                <th>Project Type <span class="sorting-outer">
                  <a href="javascript:void(0)" onclick="sortOrderByStatus('project_type', 'ASC')">
                    <i class="fa fa-sort-asc" o ></i></a>
                  <a href="javascript:void(0)" onclick="sortOrderByStatus('project_type', 'DESC')">
                    <i class="fa fa-sort-desc"></i> </a>
                </span></th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>

          

            @foreach($projects as $project)
              <tr>
              <td>{{ $project->id }}</td>
                <td>{{ @$project->property->name }}</td>
                <td >

                  @if(request()->route()->getName()  == 'reports.index')

                  <a href="{{ url('projects/'.$project->id )}}">
                   {{ $project->name }} </a> 
                 @else
                 {{ $project->name }}
                 @endif

                 </td>
                <td>{{ @$project->project_type->name }}</td>
                <td>{{ @$project->p_status->name }}</td>
               </tr>
            @endforeach

          

            </tbody>
        </table>
</div>

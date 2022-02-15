@extends('layouts.admin-app')

@section('title', 'Project Reports')


@section('content')
      <!-- Start Main View -->
  <div class="card p-2">
    <div class="row">
        <div class="col-md-12">
              <!-- Start Main View -->
            @if(session()->has('message'))
                <div class="alert alert-success alert-dismissible fade show">
                  <strong>Success!</strong> {{ session()->get('message') }}
                  <button type="button" class="close" data-dismiss="alert">&times;</button>
              </div>
            @endif

            @if(session()->has('error'))
                <div class="alert alert-warning alert-dismissible fade show">
                  <strong>Error!</strong> {{ session()->get('error') }}
                  <button type="button" class="close" data-dismiss="alert">&times;</button>
              </div>
            @endif

             @if ($errors->any())
               <div class="alert alert-warning alert-dismissible fade show">
                 <button type="button" class="close" data-dismiss="alert">&times;</button>
                  <strong>Error!</strong>  
                   {{implode(',',$errors->all() )}}
                </div>
             @endif

            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-6">
                        <h4 class="mt-0 text-left">Projects List</h4>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-9">
                       <select style="height: 26px;" onchange="return window.location.href = '?p='+this.value"> 
                      <option>Select Project Type</option>
                      @foreach($projectTypes as $type)
                         <option value="{{ $type->slug }}" {{ (@request()->p == $type->slug) ? 'selected' : ''}}> {{ $type->name }}</option>
                      @endforeach
                      </select>
                        <select style="height: 26px;"  onchange="return window.location.href = '?st='+this.value"name="status"> 
                          <option value="">Select Status</option>
                          <option value="{{\App\Models\Project::ACTIVE_STATUS }}" {{ (@request()->st == \App\Models\Project::ACTIVE_STATUS) ? 'selected' : ''}}>{{\App\Models\Project::ACTIVE_TEXT  }}</option>
                          <option value="{{ \App\Models\Project::PUT_ON_HOLD_STATUS }}" {{ (@request()->st == \App\Models\Project::PUT_ON_HOLD_STATUS) ? 'selected' : ''}}>{{\App\Models\Project::PUT_ON_HOLD_TEXT  }}</option>
                          <option value="{{ \App\Models\Project::FINISHED_STATUS }}" {{ (@request()->st == \App\Models\Project::FINISHED_STATUS) ? 'selected' : ''}}>{{\App\Models\Project::FINISHED_TEXT  }}</option>
                          <option value="{{ \App\Models\Project::CANCELLED_STATUS }}" {{ (@request()->st == \App\Models\Project::CANCELLED_STATUS) ? 'selected' : ''}}>{{\App\Models\Project::CANCELLED_TEXT  }}</option>
                          <option value="{{ \App\Models\Project::ARCHIVED_STATUS }}" {{ (@request()->st == \App\Models\Project::ARCHIVED_STATUS) ? 'selected' : ''}}>{{\App\Models\Project::ARCHIVED_TEXT  }}</option>
                        </select>

                      <input type="text" name="s" value="{{ @request()->s }}" id="inputSearch" >
                      <button id="search">Search</button>
                    </div>
                    <div class="col-3 text-right">
                       <label>Per Page </label>
                      <select style="height: 26px;" name="per_page"  onchange="selectPerpage(this.value)"> 
                        <option value="">Per Page</option>
                        <option value="25" {{ (request()->per_page == 25) ? 'selected' : ''}}>25</option>
                        <option value="50" {{ (request()->per_page == 50) ? 'selected' : ''}}>50</option>
                        <option value="100" {{ (request()->per_page == 100) ? 'selected' : ''}}> 100</option>
                        <option value="150" {{ (request()->per_page == 150) ? 'selected' : ''}}>150</option>
                        </select>
                    </div>
                </div>
                <!-- Categories Table -->
                <div class="table-responsive">

                   <table id="project-types-table" class="table table-hover text-center">
                        <thead>
                        <tr class="text-danger">
                            <th>No.</th>
                            <th>Project Name</th>
                            <!-- <th>Projects</th> -->
                            <th>Project Summary</th>
                            <th>Sub Contractor Payment</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($projects as $key => $project)
                         <tr>
                           <td> {{ $key + 1 }}</td>
                           <td>{{ $project->name }}</td>
                           <td>        
                            <button onclick="return window.location.href='reports/{{$project->id}}/project-summary'" rel="tooltip" class="btn btn-neutral bg-transparent btn-icon" data-original-title="Project Summary" title="Project Summary">      
                              <i class="fa fa-file"></i>  </button> 
                          </td>
                          <td>        
                            <button onclick="return window.location.href='reports/{{$project->id}}/subcontractor-payment'" rel="tooltip" class="btn btn-neutral bg-transparent btn-icon" data-original-title="Sub Contractor Payment" title="Sub Contractor Payment">      
                              <i class="fa fa-file"></i>  </button> 
                          </td>
                         </tr> 
                         @endforeach
                        <!-- Project Types Go Here -->
                        </tbody>
                    </table>
                </div>

                {!! $projects->render() !!}
            </div>
        </div>
    </div>
</div>

@endsection

@section('pagescript')

<script type="text/javascript">

  $(document).ready(function(){

  $('#search').click(function(){
        var search = $('#inputSearch').val();

        if(!search){
         // alert('Please enter to search');
        }
        window.location.href = '?s='+search;
  });

  $(document).keyup(function(event) {
    if (event.keyCode === 13) {
        $("#search").click();
    }
});
  });

    function selectPerpage(perPage){
     var fullUrl = window.location.href;
     let isPerpage = '{{ Request::input("per_page")}}';

     if(!isPerpage){
       window.location.href = fullUrl+(fullUrl.includes('?')?'&':'?')+'per_page='+perPage;
     }
     else if(isPerpage != perPage){
       window.location.href = fullUrl.replace(isPerpage, perPage)
     }
  } 


</script>
@endsection
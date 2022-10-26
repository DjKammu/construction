@extends('layouts.admin-app')

@section('title', 'Projects')

@section('content')
  @include('includes.favourite')
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
                    <div class="col-6 text-right">
                        <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='projects/create'">Add Project
                        </button>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-9">
                  <form>
                       <select style="height: 26px;" name="p"> 
                      <option value="">Select Project Type</option>
                      @foreach($projectTypes as $type)
                         <option value="{{ $type->slug }}" {{ (@request()->p == $type->slug) ? 'selected' : ''}}> {{ $type->name }}</option>
                      @endforeach
                      </select>

                      <select style="height: 26px;" name="pr"> 
                      <option value="">Select Property</option>
                      @foreach($propertyTypes as $type)
                         <option value="{{ $type->id }}" {{ (@request()->pr == $type->id) ? 'selected' : ''}}> {{ $type->name }}</option>
                      @endforeach

                      
                      </select>
                        <select style="height: 26px;" name="st"> 
                          <option value="">Select Status</option>
                          @foreach($statuses as $status)
                           <option value="{{ $status->id }}" {{ (@request()->st == $status->id) ? 'selected' : ''}}> {{ $status->name }}</option>
                        @endforeach

                          <!-- <option value="{{\App\Models\Project::ACTIVE_STATUS }}" {{ (@request()->st == \App\Models\Project::ACTIVE_STATUS) ? 'selected' : ''}}>{{\App\Models\Project::ACTIVE_TEXT  }}</option>
                          <option value="{{ \App\Models\Project::PUT_ON_HOLD_STATUS }}" {{ (@request()->st == \App\Models\Project::PUT_ON_HOLD_STATUS) ? 'selected' : ''}}>{{\App\Models\Project::PUT_ON_HOLD_TEXT  }}</option>
                          <option value="{{ \App\Models\Project::FINISHED_STATUS }}" {{ (@request()->st == \App\Models\Project::FINISHED_STATUS) ? 'selected' : ''}}>{{\App\Models\Project::FINISHED_TEXT  }}</option>
                          <option value="{{ \App\Models\Project::CANCELLED_STATUS }}" {{ (@request()->st == \App\Models\Project::CANCELLED_STATUS) ? 'selected' : ''}}>{{\App\Models\Project::CANCELLED_TEXT  }}</option>
                          <option value="{{ \App\Models\Project::ARCHIVED_STATUS }}" {{ (@request()->st == \App\Models\Project::ARCHIVED_STATUS) ? 'selected' : ''}}>{{\App\Models\Project::ARCHIVED_TEXT  }}</option> -->
                        </select>

                      <input type="text" name="s" value="{{ @request()->s }}" id="inputSearch" >
                      <button type="submit" id="search">Search</button>
                    </form>
                    </div>
                    <div class="col-1" style="padding: 0;">
                       <label>Start Date </label>
                      <select style="height: 26px;" name="per_page"  onchange="sortBy(this.value)"> 
                        <option value="">Sort By</option>
                        <option value="start_date,ASC" {{ (request()->orderby == 'start_date' && request()->order == 'ASC' ) ? 'selected' : ''}}>Date ASC</option>
                        <option value="start_date,DESC" {{ (request()->orderby == 'start_date' && request()->order == 'DESC' ) ? 'selected' : ''}}>Date DESC</option>
                        </select>
                    </div> 
                    <div class="col-2 text-right">
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

                  <table id="subcontractors-table" class="table card-table dataTable no-footer" role="grid" aria-describedby="subcontractors-table_info">
                         <thead class="d-none">
                            <tr role="row">
                               <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 0px;"></th>
                            </tr>
                         </thead>
                         <tbody class="row">
                          @foreach($projects as $project)
                            <tr class="text-center col-lg-4 col-sm-6 odd" style="display: flex; flex-wrap: wrap;" role="row">
                               <td>
                                  <a style="text-decoration: none; position: relative;" href="projects/{{ $project->id }}">
                                    <span class="cross"> 
                                     <form 
                                        method="post" 
                                        action="{{route('projects.destroy',$project->id)}}"> 
                                         @csrf
                                        {{ method_field('DELETE') }}

                                        <button 
                                          type="submit"
                                          onclick="return confirm('Are you sure?')"
                                          class="btn btn-neutral bg-transparent btn-icon" data-original-title="Delete Property Type" title="Delete Property Type"><i class="fa fa-trash text-danger"></i> </button>
                                      </form>
                                    </span>
                                     <div class="card card-user card-table-item" style="width: 100%; height: 100%;">
                                        <div class="card-body pb-0">
                                           <div class="author mt-1">
                                              <img class="avatar border-gray" src="{{ ($project->photo) ? url(\Storage::url($project->photo)) : asset('img/image_placeholder.png') }}">                        
                                              <h5 class="title mb-0">{{ $project->name }}</h5>
                                           </div>
                                        </div>
                                     </div>
                                  </a>
                               </td>
                            </tr>

                            @endforeach
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

  // $('#search').click(function(){
  //       var search = $('#inputSearch').val();

  //       if(!search){
  //        // alert('Please enter to search');
  //       }
  //       window.location.href = '?s='+search;
  // });

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

   function replaceUrlParam(url, paramName, paramValue)
    {
        if (paramValue == null) {
            paramValue = '';
        }
        var pattern = new RegExp('\\b('+paramName+'=).*?(&|#|$)');
        if (url.search(pattern)>=0) {
            return url.replace(pattern,'$1' + paramValue + '$2');
        }
        url = url.replace(/[?#]$/,'');
        return url + (url.indexOf('?')>0 ? '&' : '?') + paramName + '=' + paramValue;
    }

     function sortBy(val){
     val = val.split(',');
     let orderBy = val[0];
     let order = val[1];
      // return; 
      var fullUrl = window.location.href.split("#")[0];
      let isOrderBy = fullUrl.includes('orderby') ;
      let isSort = fullUrl.includes('order') ;
      
      var url = '/';
      if(isOrderBy || isSort){ 
          fullUrl = replaceUrlParam(fullUrl,'orderby',orderBy);
          fullUrl = replaceUrlParam(fullUrl,'order',order);
          url = fullUrl;
      }
      else{
         url = fullUrl+(fullUrl.includes('?')?'&':'?')+'orderby='+orderBy+'&order='+order
      }
      window.location.href = url;

 }



</script>
<style type="text/css">
  
span.cross{
    position: absolute;
    z-index: 10;
    left: 30px;
    display: none;
}
tr a:hover span.cross{
  display: block;
}
button.btn.btn-neutral.bg-transparent.btn-icon{
  background-color: transparent !important;
}
td{
  width: 100%;
}
</style>
@endsection
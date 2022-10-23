@extends('layouts.admin-app')

@section('title', 'Property Groups')

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
                        <h4 class="mt-0 text-left">Property Groups List</h4>
                    </div>
                    <div class="col-6 text-right">
                        <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='property-groups/create'">Add Property Group
                        </button>
                    </div>
                </div>

                <div class="row mb-2">
                 
                    <div class="col-6">
                     
                      <input type="text" name="s" value="{{ @request()->s }}" id="inputSearch" >
                      <button id="search">Search</button>
                    </div>
                    <div class="col-6 text-right">
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
              
                <div class="table-responsive">
                    
                    <table id="project-types-table" class="table table-hover text-center">
                        <thead>
                        <tr class="text-danger">
                            <th>Acc. No.</th>
                            <th>Name</th>
                            <th>Properties</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                        </thead>
                        <tbody>
                          @foreach($property_groups as $property_group)
                         <tr>
                           <td> {{ $property_group->account_number }}</td>
                           <td>{{ $property_group->name }}</td>
                           <td>{!! @$property_group->properties->map(function ($project, $key) {
                               return sprintf('<a target="_blank" href="properties/%s">%s</a>', @$project->id, $project->name);
                           })->join(' , ') !!}
                          </td>
                           <td>        
                            <button onclick="return window.location.href='property-groups/{{$property_group->id}}'" rel="tooltip" class="btn btn-neutral bg-transparent btn-icon" data-original-title="Edit Trade" title="Edit Trades">            <i class="fa fa-edit text-success"></i>        </button> 
                          </td>
                          <td>
                             <form 
                              method="post" 
                              action="{{route('property-groups.destroy',$property_group->id)}}"> 
                               @csrf
                              {{ method_field('DELETE') }}

                              <button 
                                type="submit"
                                onclick="return confirm('Are you sure?')"
                                class="btn btn-neutral bg-transparent btn-icon" data-original-title="Delete Trade" title="Delete Bussiness Type"><i class="fa fa-trash text-danger"></i> </button>
                            </form>
                           </td>
                         </tr> 
                         @endforeach
                        <!-- Project Types Go Here -->
                        </tbody>
                    </table>
                </div>

                {!! $property_groups->render() !!}

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
</style>
@endsection
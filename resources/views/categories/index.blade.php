@extends('layouts.admin-app')

@section('title', 'Categories')

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
                        <h4 class="mt-0 text-left">Categories List</h4>
                    </div>
                    <div class="col-6 text-right">
                        <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='categories/create'">Add Category
                        </button>
                    </div>
                </div>
                <!-- Categories Table -->
                <div class="table-responsive">
                    <table id="Category-types-table" class="table table-hover text-center">
                        <thead>
                        <tr class="text-danger">
                            <th>Acc. No.<span class="sorting-outer">
                              <a href="javascript:void(0)" onclick="sortOrderBy('account_number', 'ASC')"><i class="fa fa-sort-asc"></i></a>
                              <a href="javascript:void(0)" onclick="sortOrderBy('account_number', 'DESC')"><i class="fa fa-sort-desc"></i> </a>
                            </span></th>
                            <th>Category <span class="sorting-outer">
                              <a href="javascript:void(0)" onclick="sortOrderBy('name', 'ASC')"><i class="fa fa-sort-asc"></i></a>
                              <a href="javascript:void(0)" onclick="sortOrderBy('name', 'DESC')"><i class="fa fa-sort-desc"></i> </a>
                            </span></th>
                            <!-- <th>Categorys</th> -->
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                        </thead>
                        <tbody>
                          @foreach($categories as $type)
                         <tr>
                           <td> {{ $type->account_number }}</td>
                           <td>{{ $type->name }}</td>
                           <td>        
                            <button onclick="return window.location.href='categories/{{$type->id}}'" rel="tooltip" class="btn btn-neutral bg-transparent btn-icon" data-original-title="Edit Category Type" title="Edit Category">            <i class="fa fa-edit text-success"></i>        </button> 
                          </td>
                          <td>
                             <form 
                              method="post" 
                              action="{{route('categories.destroy',$type->id)}}"> 
                               @csrf
                              {{ method_field('DELETE') }}

                              <button 
                                type="submit"
                                onclick="return confirm('Are you sure?')"
                                class="btn btn-neutral bg-transparent btn-icon" data-original-title="Delete Category Type" title="Delete Category"><i class="fa fa-trash text-danger"></i> </button>
                            </form>
                           </td>
                         </tr> 
                         @endforeach
                        <!-- Category Types Go Here -->
                        </tbody>
                    </table>
                </div>
                 {!! $categories->render() !!}
            </div>
        </div>
    </div>
</div>

@endsection
@section('pagescript')

<script type="text/javascript">
  
    function sortOrderBy(orderBy,order){
       
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



</script>
<style type="text/css">
  
i.fa.fa-sort-desc {
    position: relative;
    left: -8px;
    cursor: pointer;
    top: 1px;
}
i.fa.fa-sort-asc{
  position: relative;
    left: 4px;
    cursor: pointer;
    top: -2px;
}
.sorting-outer{
  position: absolute;
}
.sorting-outer a{
  color: #ef8157 ;
}
.table-responsive.table-payments{
  overflow: auto;
}
</style>

@endsection
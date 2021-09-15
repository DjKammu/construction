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
                        <tr class="text-daQQQQQQZqnger">
                            <th>Acc. No.</th>
                            <th>Category</th>
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

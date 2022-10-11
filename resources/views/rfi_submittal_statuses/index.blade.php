@extends('layouts.admin-app')

@section('title', 'RFI/Submittal Statuses')

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
                        <h4 class="mt-0 text-left">Statuses List</h4>
                    </div>
                    <div class="col-6 text-right">
                        <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='statuses/create'">Add RFI/Submittal Status
                        </button>
                    </div>
                </div>
                <!-- Categories Table -->
                <div class="table-responsive">
                    <table id="project-types-table" class="table table-hover text-center">
                        <thead>
                        <tr class="text-danger">
                            <th>Acc. No.</th>
                            <th>RFI/Submittal Status</th>
                            <!-- <th>Projects</th> -->
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                        </thead>
                        <tbody>
                          @foreach($statuses as $status)
                         <tr>
                           <td> {{ $status->account_number }}</td>
                           <td>{{ $status->name }}</td>
                           <td>        
                            <button onclick="return window.location.href='statuses/{{$status->id}}'" rel="tooltip" class="btn btn-neutral bg-transparent btn-icon" data-original-title="Edit Ball In Court" title="Edit Ball In Court">            <i class="fa fa-edit text-success"></i>        </button> 
                          </td>
                          <td>
                             <form 
                              method="post" 
                              action="{{route('rfi-submittal.statuses.destroy',$status->id)}}"> 
                               @csrf
                              {{ method_field('DELETE') }}

                              <button 
                                type="submit"
                                onclick="return confirm('Are you sure?')"
                                class="btn btn-neutral bg-transparent btn-icon" data-original-title="Delete Ball In Court" title="Delete Ball In Court"><i class="fa fa-trash text-danger"></i> </button>
                            </form>
                           </td>
                         </tr> 
                         @endforeach
                        <!-- Project Types Go Here -->
                        </tbody>
                    </table>
                </div>
                 {!! $statuses->render() !!}
            </div>
        </div>
    </div>
</div>

@endsection

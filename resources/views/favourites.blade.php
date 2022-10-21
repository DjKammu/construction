@extends('layouts.admin-app')

@section('title', 'Favourites')

@section('content')
      <!-- Start Main View -->
  <div class="card p-2">
    <div class="row">
        <div class="col-md-12">

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
                        <h4 class="mt-0 text-left">All Favourites</h4>
                    </div>
                </div>

               <div class="table-responsive">
                    <table id="project-types-table" class="table table-hover text-center">
                        <thead>
                        <tr class="text-danger">
                            <th>S. No.</th>
                            <th>Label / Url</th>
                            <th>Update</th>
                            <th>Delete</th>
                        </tr>
                        </thead>
                        <tbody>
                          @foreach($favourites as $favourite)
                         <tr>
                           <td> {{ $favourite->id }}</td>
                           <td style="max-width: 100px"> <a href="{{ $favourite->url }}" target="_blank"> {{ ($favourite->label )  ? $favourite->label  : $favourite->url }}</a></td>
                         
                         <td>
                             <form 
                              method="post" 
                              action="{{route('update.favourite',$favourite->id)}}"> 
                               @csrf
                              <input type="text" placeholder="Label" name="label" value="{{ $favourite->label}}" required="">
                              <button 
                                type="submit"
                                class="btn btn-neutral bg-transparent btn-icon" data-original-title="Update" title="Update"><i class="fa fa-save  text-danger"></i> </button>
                            </form>
                           </td>

                           <td>
                             <form 
                              method="post" 
                              action="{{route('delete.favourite',$favourite->id)}}"> 
                               @csrf
                              {{ method_field('DELETE') }}

                              <button 
                                type="submit"
                                onclick="return confirm('Are you sure?')"
                                class="btn btn-neutral bg-transparent btn-icon" data-original-title="Delete Favourite" title="Delete Favourite"><i class="fa fa-trash text-danger"></i> </button>
                            </form>
                           </td>
                           </tr> 

                         @endforeach
                        <!-- Project Types Go Here -->
                        </tbody>
                    </table>
                </div>
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
  cursor: pointer;
}
</style>
@endsection
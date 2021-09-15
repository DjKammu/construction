@extends('layouts.admin-app')

@section('title', 'Edit Property')

@section('content')

@include('includes.back')
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
                        <h4 class="mt-0 text-left">Edit Property</h4>
                    </div>
                </div>

               <div class="row">
                        <div class="col-md-12">
                            <div class="card-body">

                              <div class="nav-tabs-navigation">
                                    <div class="nav-tabs-wrapper">
                                        <ul id="tabs" class="nav nav-tabs" role="tablist">

                                            <li class="nav-item">
                                                <a class="nav-link text-dark active" data-toggle="tab" href="#details" role="tab"
                                                   aria-expanded="true">Details</a>
                                            </li>

                                            <li class="nav-item">
                                                <a class="nav-link text-dark" data-toggle="tab" href="#documents" role="tab"
                                                   aria-expanded="false">Documents</a>
                                            </li>

                                        </ul>
                                    </div>
                               </div>

                                <div id="my-tab-content" class="tab-content">

                                    @include('properties.includes.details')
                                    @include('properties.includes.documents')
                             
                              </div>

                            </div>
                            </div>
                        </div>
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
}
td{
  width: 100%;
}
</style>
@endsection
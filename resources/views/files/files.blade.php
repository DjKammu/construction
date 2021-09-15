@extends('layouts.admin-app')

@section('title', 'Files')

@section('content')

 <!-- Start Main View -->
                <!-- Dashboard Overview -->
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    @foreach($breadcrumbs as $breadcrumb)
    <li class="breadcrumb-item"><a href="{{ url($breadcrumb['link']) }}">{{ \Str::title(str_replace('-', ' ', $breadcrumb['name'])) }} </a></li>
    @endforeach
    <li class="breadcrumb-item active" aria-current="page">{{ @ucfirst(last(request()->segments()))}}</li>
  </ol>
</nav>

<div class="row">

 @if(!empty($files))
   @foreach($files as $file)      
    <div class="col-lg-4 col-md-6 col-sm-6">
        <div class="card card-stats">
            @if(@$removable)
            <span class="cross"> 
             <form method="post" action="{{route('files.destroy')}}?path={{$file['path']}}"> 
                                                 @csrf
                                                {{ method_field('DELETE') }}

                <button type="submit" onclick="return confirm('Are you sure to delete it? Make sure you can`t retrieve from archive!')" class="btn btn-neutral bg-transparent btn-icon" data-original-title="Delete Property Type" title="Delete Property Type"><i class="fa fa-trash text-danger"></i> </button>
              </form>
            </span>
            @endif 

            <div class="card-body ">
                <div class="row">
                    <div class="col-5 col-md-4">
                        <div class="icon-big text-center icon-warning">
                            <i class="fa fa-file text-primary"></i>
                        </div>
                    </div>
                    <div class="col-7 col-md-8">
                        <div class="numbers">
                            <p class="card-category">{{ @ucfirst($file['basename']) }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer ">
                <hr>
                <div class="stats">
                    <a href="{{ url($file['path']) }}" class="text-muted" target="_blank">
                        <i class="fa fa-eye"></i> View </a>
                </div>
            </div>
        </div>
    </div>
  
   @endforeach
   @endif


      <!-- Files Overview -->
   
   @if(empty($files))
   @foreach($directories as $directory)    

   <div class="col-lg-4 col-md-6 col-sm-6">
        <div class="card card-stats">
            <div class="card-body ">
                <div class="row">
                    <div class="col-5 col-md-4">
                        <div class="icon-big text-center icon-warning">
                            <i class="fa fa-folder text-warning"></i>
                        </div>
                    </div>
                    <div class="col-7 col-md-8">
                        <div class="numbers">
                            <p class="card-title">{{ @ucfirst($directory['basename']) }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer ">
                <hr>
                <div class="stats">
                    <a href="{{ route('files.property-type',['directory' => $directory['dirname'],'property_type' => $directory['basename'] ]) }}" class="text-muted"><i class="fa fa-eye"></i> View </a>
                </div>
            </div>
        </div>
    </div>
   @endforeach
   @endif

</div>


@endsection


@section('pagescript')

<style type="text/css">
  
span.cross{
    position: absolute;
    z-index: 10;
    right: 6px;
     display: none; 
    bottom: -16px;
}
.card.card-stats:hover span.cross{
  display: block;
}

</style>
@endsection
@extends('layouts.admin-app')

@section('title', 'Files')

@section('content')

 <!-- Start Main View -->
                <!-- Dashboard Overview -->

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item active">Files</li>
  </ol>
</nav>
<div class="row">

 @if(!empty($files))
   @foreach($files as $directory)      
    <div class="col-lg-4 col-md-6 col-sm-6">
        <div class="card card-stats">
            <div class="card-body ">
                <div class="row">
                    <div class="col-5 col-md-4">
                        <div class="icon-big text-center icon-warning">
                            <i class="fa fa-file text-primary"></i>
                        </div>
                    </div>
                    <div class="col-7 col-md-8">
                        <div class="numbers">
                            <p class="card-category">{{ @$directory }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer ">
                <hr>
                <div class="stats">
                    <a href="{{ url(\Storage::url($directory)) }}" class="text-muted" target="_blank">
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
                            <p class="card-title">{{ @ucfirst($directory) }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer ">
                <hr>
                <div class="stats">
                    <a href="{{ route('files.index',['directory' => $directory]) }}" class="text-muted"><i class="fa fa-eye"></i> View </a>
                </div>
            </div>
        </div>
    </div>
  
   @endforeach
   @endif

</div>


@endsection

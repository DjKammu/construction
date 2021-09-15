@extends('layouts.admin-app')

@section('title', 'Dashboard')

@section('content')

 <!-- Start Main View -->
                <!-- Dashboard Overview -->
<div class="row">

    <!-- Categories Overview -->
    <div class="col-lg-4 col-md-6 col-sm-6">
        <div class="card card-stats">
            <div class="card-body ">
                <div class="row">
                    <div class="col-5 col-md-4">
                        <div class="icon-big text-center icon-warning">
                            <i class="fa fa-list-ul text-warning"></i>
                        </div>
                    </div>
                    <div class="col-7 col-md-8">
                        <div class="numbers">
                            <p class="card-category">Project Types</p>
                            <p id="categories_count" class="card-title">{{ @$projectTypes }}<p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer ">
                <hr>
                <div class="stats">
                    <a href="{{ route('project-types.index')}}" class="text-muted"><i class="fa fa-eye"></i> View All</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Trades Overview -->
  <!--   <div class="col-lg-4 col-md-6 col-sm-6">
        <div class="card card-stats">
            <div class="card-body ">
                <div class="row">
                    <div class="col-5 col-md-4">
                        <div class="icon-big text-center icon-warning">
                            <i class="fa fa-building-o text-success"></i>
                        </div>
                    </div>
                    <div class="col-7 col-md-8">
                        <div class="numbers">
                            <p class="card-category">Properties</p>
                            <p id="trades_count" class="card-title">{{ @$properties }}<p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer ">
                <hr>
                <div class="stats">
                    <a href="{{ route('properties.index')}}" class="text-muted"><i class="fa fa-eye"></i> View All</a>
                </div>
            </div>
        </div>
    </div> -->

     <!-- Files Overview -->
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
                            <p class="card-category">Document Types</p>
                            <p id="projects_count" class="card-title">{{ @$documentTypes }}<p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer ">
                <hr>
                <div class="stats">
                    <a href="{{ route('document-types.index') }}" class="text-muted"><i class="fa fa-eye"></i> View All</a>
                </div>
            </div>
        </div>
    </div>


    <!-- Subcontractors Overview -->
    <div class="col-lg-4 col-md-6 col-sm-6">
        <div class="card card-stats">
            <div class="card-body ">
                <div class="row">
                    <div class="col-5 col-md-4">
                        <div class="icon-big text-center icon-warning">
                            <i class="fa fa-user-circle text-danger"></i>
                        </div>
                    </div>
                    <div class="col-7 col-md-8">
                        <div class="numbers">
                            <p class="card-category">Roles</p>
                            <p id="subcontractors_count" class="card-title">{{@$roles}}<p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer ">
                <hr>
                <div class="stats">
                    <a href="{{ route('roles.index') }}" class="text-muted"><i class="fa fa-eye"></i> View All</a>
                </div>
            </div>
        </div>
    </div>
  
    <!-- Users Overview -->
    <div class="col-lg-4 col-md-6 col-sm-6">
        <div class="card card-stats">
            <div class="card-body ">
                <div class="row">
                    <div class="col-5 col-md-4">
                        <div class="icon-big text-center icon-warning">
                            <i class="fa fa-user text-success"></i>
                        </div>
                    </div>
                    <div class="col-7 col-md-8">
                        <div class="numbers">
                            <p class="card-category">Users</p>
                            <p id="users_count" class="card-title">{{@$users}}<p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer ">
                <hr>
                <div class="stats">
                    <a href="{{ route('users.index')}}" class="text-muted"><i class="fa fa-eye"></i> View All</a>
                </div>
            </div>
        </div>
    </div>

      <!-- Files Overview -->
    <!-- <div class="col-lg-4 col-md-6 col-sm-6">
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
                            <p class="card-category">Files</p>
                            <p id="files_count" class="card-title">{{ @$files }}<p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer ">
                <hr>
                <div class="stats">
                    <a href="{{ route('files.index') }}" class="text-muted"><i class="fa fa-eye"></i> View All</a>
                </div>
            </div>
        </div>
    </div> 
 -->
     <!-- Files Overview -->
    <!-- <div class="col-lg-4 col-md-6 col-sm-6">
        <div class="card card-stats">
            <div class="card-body ">
                <div class="row">
                    <div class="col-5 col-md-4">
                        <div class="icon-big text-center icon-warning">
                            <i class="fa fa-search text-success"></i>
                        </div>
                    </div>
                    <div class="col-7 col-md-8">
                        <div class="numbers">
                            <p class="card-category">Search</p>
                            <p id="files_count" class="card-title">0<p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer ">
                <hr>
                <div class="stats">
                    <a href="{{ route('documents.search') }}" class="text-muted"><i class="fa fa-eye"></i> View All</a>
                </div>
            </div>
        </div>
    </div> -->

</div>


@endsection

@extends('layouts.admin-app')

@section('title', 'Product Lines')

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
                        <h4 class="mt-0 text-left">{{ @$project->name }} - Project Line</h4>
                    </div>
                </div>

               <div class="row">
                        <div class="col-md-12">
                             <project-line original_amount="{{ @$project->original_amount }}" projectid="{{ @$project->id }}" retainage="{{ @$project->retainage_percentage }}"></project-line>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('pagescript') 
<style type="text/css">

.sorting-outer{
  position: absolute;
}

.sorting-outer a{
  color: #ef8157 ;
}


i.fa.fa-sort-desc {
    position: relative;
    left: -8px;
    cursor: pointer;
    top: 3px;
}
i.fa.fa-sort-asc{
  position: relative;
    left: 4px;
    cursor: pointer;
    top: 0px;
}
</style>

@endsection
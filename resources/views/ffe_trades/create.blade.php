@extends('layouts.admin-app')

@section('title', 'FFE Trade')

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
                        <h4 class="mt-0 text-left">Add FFE Trade</h4>
                    </div>
                </div>

               <div class="row">
                        <div class="col-md-12">
                            <div class="card-body">
                                <form   method="post" 
                              action="{{ route('ffe.trades.store') }}" enctype="multipart/form-data">
                                  @csrf

                                    <!-- Current Password -->
                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Name 
                                                </label>
                                                <input  name="name" value="{{ old('name')}}" type="text" class="form-control" placeholder="Name" required="">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Current Password -->
                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Account Number 
                                                </label>
                                                <input  name="account_number"  value="{{ old('account_number')}}" type="text" class="form-control" placeholder="Account Number" required="">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">FFE Category
                                                </label>
                                                <select class="form-control" name="category_id"> 
                                                  <option value=""> Select Category</option>
                                                  @foreach($categories as $cat)
                                                   <option value="{{ $cat->id }}" >{{ $cat->name}}
                                                   </option>
                                                  @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div> 

                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">
                                                  Scope
                                                </label>
                                               <input type="file" name="scope">
                                            </div>
                                        </div>
                                    </div> 



                                    <!-- Submit Button -->
                                    <div class="col-12 text-center">
                                        <button id="change-password-button" type="submit" class="btn btn-danger">Create FFE Trade
                                        </button>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>

@endsection
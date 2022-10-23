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
                        <h4 class="mt-0 text-left">Edit FFE Trade</h4>
                    </div>
                </div>

               <div class="row">
                        <div class="col-md-12">
                            <div class="card-body">
                                <form   method="post" 
                              action="{{ route('ffe.trades.update',$trade->id ) }}" enctype="multipart/form-data">
                                  @method('PUT')
                                  @csrf

                                    <!-- Current Password -->
                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Name 
                                                </label>
                                                <input  name="name" value="{{ $trade->name }}" type="text" class="form-control" placeholder="Name" required="">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Current Password -->
                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Account Number 
                                                </label>
                                                <input  name="account_number"  value="{{ $trade->account_number }}" type="text" class="form-control" placeholder="Account Number" required="">
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
                                                   <option value="{{ $cat->id }}" {{ ($trade->category_id == $cat->id ) ? 'selected="selected"' : ''}}>{{ $cat->name}}
                                                   </option>
                                                  @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div> 
                                   
                                   <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                   @if($trade->scope)
                                                 @php
                                                   $fileInfo = pathinfo($trade->scope);
                                                   $extension = @$fileInfo['extension'];
                                                  
                                                if(in_array($extension,['doc','docx','docm','dot',
                                                'dotm','dotx'])){
                                                    $extension = 'word'; 
                                                 }
                                                 else if(in_array($extension,['csv','dbf','dif','xla',
                                                'xls','xlsb','xlsm','xlsx','xlt','xltm','xltx'])){
                                                    $extension = 'excel'; 
                                                 }
                                                 @endphp
                                                 
                                                  <a href="{{ url(\Storage::url($trade->scope)) }}" target="_blank">
                                                  <img class="avatar border-gray" src="{{ asset('img/'.@$extension.'.png') }}">
                                                  </a> 
                                                  {{$trade->scope }}

                                                 @endif
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
                                        <button id="change-password-button" type="submit" class="btn btn-danger">Update FFE Trade
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
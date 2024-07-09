@extends('layouts.admin-app')

@section('title', 'Trade')

@section('content')

@include('includes.back', 
['url' => route("projects.show", ['project' => request()->id]),
'to' => 'to Project'])


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
                        <h4 class="mt-0 text-left">{{ @$project->name }} - Assign Trade</h4>
                    </div>
                </div>

               <div class="row">
                        <div class="col-md-12">
                            <div class="card-body">
                                <form   method="post" 
                              action="{{ route('projects.trades',['id' => request()->id]) }}" >
                                  @csrf

                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Select Trades
                                                </label>
                                                <!-- <select class="form-control" name="trade_id"> 
                                                  <option value=""> Select Trade</option>
                                                  @foreach($trades as $trade)
                                                   <option value="{{ $trade->id }}" >{{ $trade->name}}
                                                   </option>
                                                  @endforeach
                                                </select> -->

                                                 
                                                  <div class="form-group">
                                                  @forelse($trades as $trade)
                                                  <div class="form-check-inline">
                                                    <label class="form-check-label">
                                                      <input type="checkbox"
                                                       name="trade_id[]" value="{{ $trade->id }}">
                                                      {{ $trade->name}}
                                                    </label>
                                                  </div>
                                                  @empty 
                                                    <label class="text-dark" for="No Pending">
                                                    <b>No Pending Trades</b>
                                                    </label>
                                                  @endforelse
                                                   </div>
                                            </div>
                                        </div>
                                    </div> 

                                    <!-- Submit Button -->
                                    <div class="col-12 text-center">
                                        <button id="change-password-button" type="submit" class="btn btn-danger">Assign Trade
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
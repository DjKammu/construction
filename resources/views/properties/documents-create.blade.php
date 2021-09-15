@extends('layouts.admin-app')

@section('title', 'Document')

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
                        <h4 class="mt-0 text-left">Add Document</h4>
                    </div>
                </div>

               <div class="row">
                        <div class="col-md-12">
                            <div class="card-body">
                                <form   method="post" 
                              action="{{ route('properties.documents',[ 'id' => request()->id]) }}"
                               enctype="multipart/form-data">
                                  @csrf

                                    <!-- Current Password -->
                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Name 
                                                </label>
                                                <input  name="name" value="{{ old('name')}}" type="text" class="form-control" placeholder="Document Name" required="">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Document Type
                                                </label>
                                                <select class="form-control" name="document_type_id"> 
                                                  <option value=""> Select Document Type</option>
                                                  @foreach($documentsTypes as $type)
                                                   <option value="{{ $type->id }}" >{{ $type->name}}
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
                                                  Tenant
                                                </label>
                                                <select class="form-control" id="year" 
                                                name="tenant_id"> 
                                                  <option value=""> Select Tenant</option>
                                                    @foreach($tenants as $tenant)
                                                     <option value="{{ $tenant->id }}" > {{ $tenant->name }}</option>
                                                  @endforeach

                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Year
                                                </label>
                                                <select class="form-control" id="year" 
                                                name="year"> 
                                                  <option value=""> Select Year</option>

                                                   @for($i = date('Y'); $i >= date('Y') - 50; $i--)
                                                    <option value="{{ $i }}" 
                                                    {{ ($i == date('Y') ? 'selected' : '')}}
                                                    >{{ $i }}
                                                   </option>
                                                  @endfor

                                                </select>
                                            </div>

                                             <div class="form-group">
                                                <label class="text-dark" for="password">Month
                                                </label>
                                                <select class="form-control" id="month" name="month"> 
                                                  <option value=""> Select Month</option>
                                                  @for ($i=1; $i<=12; $i++)
                                                    <option value="{{  $i }}"  {{ ( date('F') ==  date('F', mktime(0, 0, 0, $i, 1)) ? 'selected' : '')}} >{{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                                   </option>
                                                  @endfor

                                                </select>
                                            </div>

                                            @php
                                            
                                            $days = \Carbon\Carbon::now()->daysInMonth;

                                            @endphp

                                             <div class="form-group">
                                                <label class="text-dark" for="password">Date
                                                </label>
                                                <select class="form-control" id="date" name="date"> 
                                                  <option value=""> Select Date</option>

                                                  @for ($i=1; $i<=$days; $i++)
                                                    <option value="{{ $i }}" >{{ sprintf("%02d", $i) }}
                                                   </option>
                                                  @endfor

                                                </select>
                                            </div>
                                        </div>
                                    </div> 

                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">File 
                                                </label>
                                                <input  name="file[]"  type="file" multiple="">
                                            </div>
                                        </div>
                                    </div>


                                    <!-- Submit Button -->
                                    <div class="col-12 text-center">
                                        <button id="change-password-button" type="submit" class="btn btn-danger">Create Document
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

@section('pagescript')

<script type="text/javascript">

  $(document).ready(function(){

  $('#month').click(function(){
        var month = $(this).val();
        var year = $('#year').val();
        var days =  new Date(year, month, 0).getDate();
        var Html = '';

       if(days){
        Html +='<option>Select Date</option>';
        for (let i = 1; i <= days; i++) {
          Html += '<option value="'+i+'" >'+minTwoDigits(i)+'</option>';
        }
        $('#date').html('');   
        $('#date').html(Html);  
       }

  });

  function minTwoDigits(n) {
    return (n < 10 ? '0' : '') + n;
  }



  });

</script>
@endsection
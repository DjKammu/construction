@extends('layouts.admin-app')

@section('title', 'Set Up')

@section('content')
      <!-- Start Main View -->
  <div class="card p-2">
    <div class="row">
        <div class="col-md-12">

            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-6">
                        <h4 class="mt-0 text-left">All Setup</h4>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-12">
                         <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='{{url("users")}}'">Users
                        </button>
                        <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='{{url("roles")}}'">Roles
                        </button>
                    </div>
                    <div class="col-12">
                        <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='{{ route("document-types.index") }}'">Document Types
                        </button>
                        <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='{{ route("project-types.index") }}'">Project Types
                        </button> 

                         <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='{{ route("categories.index") }}'">Categories
                        </button>
                        <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='{{ route("trades.index") }}'">Trades
                        </button>

                        <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='{{ route("subcontractors.index") }}'">Subcontractors
                        </button>
                        <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='{{ route("vendors.index") }}'">Vendors
                        </button>
                        
                        <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='{{url("setting")}}'">Email
                        </button>
                        <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='{{url("properties")}}'">Property
                        </button> 
                        <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='{{url("assignees")}}'">RFI/Submittal Assignees
                        </button>
                        <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='{{url("ball_in_courts")}}'">Ball In Courts
                        </button>
                        <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='{{url("statuses")}}'"> Project Statuses
                        </button>
                        <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='{{url("rfi-submittal/statuses")}}'">RFI/Submittal Statuses
                        </button>
                        <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='{{url("property-groups")}}'">Property Groups
                        </button>
                        <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='{{url("inspection-types")}}'">Inspection Type
                        </button>
                        <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='{{url("report-companies")}}'">Report Company
                        </button>
                        <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='{{url("payment-statuses")}}'">Payment Status
                        </button>
                        <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='{{url("procurement-statuses")}}'">Procurement Status
                        </button>
                        <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='{{url("inspection-categories")}}'">Inspection Category
                        </button>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-6">
                        <h4 class="mt-0 text-left">FFE Modules</h4>
                    </div>
                </div>

                <div class="row mb-2">
                   
                    <div class="col-12">   
                        <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='{{ route("ffe.categories.index") }}'"> FFE Categories
                        </button>
                        <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='{{ route("ffe.trades.index") }}'"> FFE Trades
                        </button>
                        <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='{{ route("ffe.vendors.index") }}'"> FFE Vendors
                        </button>
                    </div>
                </div> 


                <div class="row mb-2">
                    <div class="col-6">
                        <h4 class="mt-0 text-left">Soft Cost Modules</h4>
                    </div>
                </div>

                <div class="row mb-2">
                   
                    <div class="col-12">   
                        <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='{{ route("soft-cost.categories.index") }}'"> Soft Cost Categories
                        </button>
                        <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='{{ route("soft-cost.trades.index") }}'"> Soft Cost Trades
                        </button>
                        <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='{{ route("soft-cost.vendors.index") }}'"> Soft Cost Vendors
                        </button>
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
}
td{
  width: 100%;
}
</style>
@endsection
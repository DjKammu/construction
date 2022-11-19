@extends('layouts.admin-app')

@section('title', 'FFE Procurement Log')

@section('content')

@include('includes.back',['url' => route('ffe.index',['project' => request()->project ]) , 'to' => 'To FFE'])


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
                        <h4 class="mt-0 text-left"> {{ @$log->project->name }} -  Make Procurement Log </h4>
                    </div>
                </div>

               <div class="row">
                        <div class="col-md-12">
                            <div class="card-body">
                                <form   method="post" 
                              action="{{ route('projects.ffe.logs.store',['id' => (@$log->id) ? @$log->id : 0 , 'project' => request()->project ]) }}"
                               enctype="multipart/form-data">
                                  @csrf
                        
                                  <div class="row">
                                     <div class="col-lg-5 col-md-6 mx-auto">
                                        <div class="form-group">
                                            <label class="text-dark" for="password">Date 
                                            </label>
                                            <input  name="date" value="{{ old('date')}}" type="text" class="form-control date" placeholder="Date">
                                        </div>
                                     </div>
                                    </div>

                                   

                                    <div class="row">
                                         <div class="col-lg-5 col-md-6 mx-auto">

                                           <div class="form-group">
                                                <label class="text-dark" for="password"> Item 
                                                </label>
                                                <input  name="item" value="{{ old('item')}}" type="text" class="form-control" placeholder="Item" >
                                            </div>
                                        </div>
                                    </div> 

                                    <div class="row">
                                          <div class="col-lg-5 col-md-6 mx-auto">
                                              <div class="form-group">
                                                  <label class="text-dark" for="password">Vendor
                                                  </label>
                                                  <select class="form-control" name="ffe_vendor_id"> 
                                                    <option value="">Select Vendor</option>
                                                    @foreach(@$vendors as $vendor)
                                                     <option value="{{ $vendor->id }}">{{ $vendor->name}}
                                                     </option>
                                                    @endforeach
                                                  </select>
                                              </div>
                                          </div>
                                        </div>
                                        <div class="row">
                                         <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">PO Sent Date 
                                                </label>
                                                <input  name="po_sent" value="{{ old('po_sent')}}" type="text" class="form-control date" placeholder="Date">
                                            </div>
                                         </div>
                                        </div>
                                       

                                    

                                    <div class="row">
                                         <div class="col-lg-5 col-md-6 mx-auto">

                                           <div class="form-group">
                                                <label class="text-dark" for="password"> Lead Time
                                                </label>
                                                <input  name="lead_time" value="{{ old('lead_time')}}" type="text" class="form-control" placeholder="Lead Time" step="any"step="any">
                                            </div>
                                        </div>
                                    </div> 
                                     

                                     <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Payment Status
                                                </label>
                                                <select class="form-control" name="status_id"> 
                                                  <option value="">Select Payment Status</option>
                                                     @foreach($statuses as $status)
                                                       <option value="{{ $status->id }}" {{ (@$project->status == $status->id) ? 'selected' : ''}}> {{ $status->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                     <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Procurement Status
                                                </label>
                                                <select class="form-control" name="procurement_status_id"> 
                                                  <option value="">Select Procurement Status</option>
                                                     @foreach($procurementStatus as $status)
                                                       <option value="{{ $status->id }}" {{ (@$project->status == $status->id) ? 'selected' : ''}}> {{ $status->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                         <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Date Shipped 
                                                </label>
                                                <input  name="date_shipped" value="{{ old('date_shipped')}}" type="text" class="form-control date" placeholder="Date Shipped">
                                            </div>
                                         </div>
                                        </div>

                                        <div class="row">
                                         <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Tentative Date Delivery 
                                                </label>
                                                <input  name="tentative_date_delivery" value="{{ old('tentative_date_delivery')}}" type="text" class="form-control date" placeholder="Tentative Date Delivery">
                                            </div>
                                         </div>
                                        </div>

                                        <div class="row">
                                         <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Date Received
                                                </label>
                                                <input  name="date_received" value="{{ old('date_received')}}" type="text" class="form-control date" placeholder="Date Received">
                                            </div>
                                         </div>
                                        </div>

                                        <div class="row">
                                         <div class="col-lg-5 col-md-6 mx-auto">

                                           <div class="form-group">
                                                <label class="text-dark" for="password"> Store Place
                                                </label>
                                                <input  name="store_place" value="{{ old('store_place')}}" type="text" class="form-control" placeholder="Store Place" step="any"step="any">
                                            </div>
                                        </div>
                                    </div> 

                                    
                                   <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Received Shipment Attachment
                                                </label>
                                                <input  name="files[]"  type="file" multiple="">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Invoice
                                                </label>
                                                <input  name="invoice"  type="file">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">PO Sent File
                                                </label>
                                                <input  name="po_sent_file"  type="file">
                                            </div>
                                        </div>
                                    </div>
                                   
                                   <div class="row">
                                         <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Notes
                                                </label>
                                                <textarea  name="notes"  type="text" class="form-control" placeholder="Notes" >
                                                 {{ old('notes')}}</textarea>
                                            </div>
                                        </div>
                                    </div> 


                                    <!-- Submit Button -->
                                    <div class="col-12 text-center">
                                      <input type="hidden" name="project_id" value="{{ $id }}" />
                                        <button id="change-password-button" type="submit" class="btn btn-danger">Create Procurement Log
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

$( document ).ready(function() {
    $('.date').datetimepicker({
      format: 'M-D-Y'
  });

$("input[name='non_contract']").click(function() {
      var id = $(this).attr('div');
      $(".subcontractor-vendor").hide();
      $("#" + id).show();
  });



 // $('select[name="vendor_id"]').change(function(){
 //      let vendorId = $(this).val();
 //      $.ajax({
 //            url: "{{ route('vendor.materials')}}"+'?vendor_id='+vendorId,
 //            type: "GET",
 //            success: function (response) {
 //                var html = '<option value="">Select Material</option>';
 //                for (let i = 0; i < response.length; i++) {
 //                  html += '<option value="'+response[i].id+'">'+response[i].name+'</option>';
 //                }
 //                $('#materials').html(html);
 //            }
 //        });
    
 // });

});

</script>
@endsection

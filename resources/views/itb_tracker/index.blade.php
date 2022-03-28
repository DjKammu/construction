@extends('layouts.admin-app')

@section('title', 'ITB Tracker')

@section('content')
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
                        <h4 class="mt-0 text-left">Projects List</h4>
                    </div>
                </div>

                <div class="row mb-2">
                   <div class="col-9">
                       <select style="height: 26px;" onchange="selectProject(this.value,'pt')"> 
                      <option value="">Select Project</option>
                      @foreach($projects as $pr)
                         <option value="{{ $pr->id }}" {{ ($projectId == $pr->id) ? 'selected' : ''}}> {{ $pr->name }}</option>
                      @endforeach
                      </select>
                    </div>
                </div>
                <!-- Categories Table -->
            </div>
        </div>
       <div class="col-md-12">

         <div class="card-body">
                <div class="row mb-2">
                    <div class="col-6">
                        <h4 class="mt-0 text-left">Trades List</h4>
                    </div>
                </div>
                <!-- Categories Table -->
                <div class="table-responsive">
                    <table id="category-types-table" class="table table-hover text-center">
                        <thead>
                        <tr class="text-daQQQQQQZqnger">
                            <th>Acc. No.</th>
                            <th>Trade</th>
                            <th>Subcontractors</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Email Sent </th>
                            <th>Bid Recieved </th>
                            <th>Subcontract Signed</th>
                        </tr>
                        </thead>
                        <tbody>
                          @foreach($trades as $trade)
                         <tr>
                           <td> {{ $trade->account_number }}</td>
                           <td>{{ $trade->name }}</td>
                            <td class="text-left">
                              @foreach($trade->subcontractors as $subcontractor)
                              <input class="checkbox" type="checkbox" value="1">
                              <span> {{ $subcontractor->name }}</span>
                               </br> 
                              @endforeach
                           </td> 
                           <td>
                              @foreach($trade->subcontractors as $subcontractor)
                              {{ $subcontractor->email_1 }}
                               </br> 
                              @endforeach
                           </td> 
                          
                           <td>
                              @foreach($trade->subcontractors as $subcontractor)
                              {{ $subcontractor->mobile }}
                               </br> 
                              @endforeach
                           </td>

                           <td>
                              @foreach($trade->subcontractors as $subcontractor)
                              {{ \App\Models\ITBTracker::$ITBArr[\App\Models\ITBTracker::FALSE] }}
                               </br> 
                              @endforeach
                           </td>
                           <td>
                              @foreach($trade->subcontractors as $subcontractor)
                              {{ \App\Models\ITBTracker::$ITBArr[\App\Models\ITBTracker::FALSE] }}
                               </br> 
                              @endforeach
                           </td>
                           <td>
                              @foreach($trade->subcontractors as $subcontractor)
                              {{ \App\Models\ITBTracker::$ITBArr[\App\Models\ITBTracker::FALSE] }}
                               </br> 
                              @endforeach
                           </td>
                          
                          
                         </tr> 
                         @endforeach
                        <!-- Category Types Go Here -->
                        </tbody>
                    </table>
                </div>            
              </div>
            </div>  
    </div>
</div>

@endsection


@section('pagescript')
<script type="text/javascript">

function selectProject(project, cLass){
       let path = window.location.href.split('?')[0]
       path = path.split('#')[0]
       let url = ''; 
       url += 'p='+project;
       window.location.href = path+'?'+url;     
  } 

</script>
<style type="text/css">
  #category-types-table{
    font-size: 12px;
  }
  .checkbox{
    margin-right: 4px;
  }
</style>>
@endsection
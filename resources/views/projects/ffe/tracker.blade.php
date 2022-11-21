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
                      <div class="col-6 text-right">
                        <button type="button" class="btn btn-danger mt-0"  onclick="sendMail()">Send Mail
                        </button>
                    </div>

                </div>
                <!-- Categories Table -->
                <div class="table-responsive">
                    <table id="category-types-table" class="table table-hover text-center">
                        <thead>
                        <tr class="text-danger">
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
                           
                           @php
                             $subcontractorHtml = '';
                             $emailHtml = '';
                             $mobileHtml = '';
                             $mailSentHtml = '';
                             $bidHtml = '';
                             $signedHtml = '';
                             foreach($trade->subcontractors as $tsk =>  $subcontractor){
                               $checked = ($subcontractor->mail_sent == true ) ? 'checked=checked' : '';
                               $selectedTrueBid = $subcontractor->bid_recieved == \App\Models\ITBTracker::TRUE ? "selected" : "";
                               $selectedFalseBid = $subcontractor->bid_recieved == \App\Models\ITBTracker::FALSE ? "selected" : "";
                               $selectedTrueSigned = $subcontractor->contract_sign == \App\Models\ITBTracker::TRUE ? "selected" : "";
                               $selectedFalseSigned = $subcontractor->contract_sign == \App\Models\ITBTracker::FALSE ? "selected" : "";
                               $subcontractorHtml .=  "<input class='checkbox subcontractor' type='checkbox' value='$trade->id,$subcontractor->id'   $checked ><span>$subcontractor->name</span> </br>";

                               $emailHtml .= " $subcontractor->email_1 </br>";
                               $mobileHtml .= " $subcontractor->mobile </br>";
                               $mailSentHtml .= \App\Models\ITBTracker::$ITBArr[$subcontractor->mail_sent]."
                                </br>"; 
                                $bidHtml .= "
                                 <select onchange='selectBid(this.value,$subcontractor->tracker_id)'> 
                                  <option value=''>Select</option>
                                   <option value=".\App\Models\ITBTracker::TRUE." $selectedTrueBid>".\App\Models\ITBTracker::TRUE_TEXT." </option>
                                   <option value=".\App\Models\ITBTracker::FALSE." $selectedFalseBid>".\App\Models\ITBTracker::FALSE_TEXT." </option>
                                  </select></br>"; 

                                 $signedHtml .= "<select onchange='selectSign(this.value,$subcontractor->tracker_id)'> 
                                  <option value=''>Select</option>
                                   <option value=".\App\Models\ITBTracker::TRUE." $selectedTrueSigned>".\App\Models\ITBTracker::TRUE_TEXT." </option>
                                   <option value=".\App\Models\ITBTracker::FALSE." $selectedFalseSigned>".\App\Models\ITBTracker::FALSE_TEXT." </option>
                                  </select></br>";
                             
                             }
                      
                             @endphp

                         <tr>
                           <td> {{ $trade->account_number }}</td>
                           <td>{{ $trade->name }}</td>
                            <td style="width: 20%;" class="text-left">
                              {!! $subcontractorHtml !!}
                           </td> 
                           <td>
                             {!! $emailHtml !!}
                           </td>          
                           <td>
                              {!! $mobileHtml !!}
                           </td>
                           <td>
                             {!! $mailSentHtml !!}
                           </td>
                           <td>
                             {!! $bidHtml !!}
                           </td>
                           <td>
                             {!! $signedHtml !!}
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

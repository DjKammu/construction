   <div class="tab-pane" id="tracker" role="tabpanel" aria-expanded="true">
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

        </div>
       <div class="col-md-12">

         <div class="card-body">
                <div class="row mb-2">
                     <div class="col-6">
                         <h4 class="mt-0 text-left">ITB Tracker - Trades List</h4>
                     </div>
                      <div class="col-6 text-right">
                        <button type="button" class="btn btn-danger mt-0"  
                        onclick="sendMailTracker()">Send Mail
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
                            <th>Vendors</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Email Sent </th>
                            <th>Bid Recieved </th>
                            <th>Subcontract Signed</th>
                        </tr>
                        </thead>
                        <tbody>
                          @foreach($ITBtrades as $trade)
                           
                           @php
                             $subcontractorHtml = '';
                             $emailHtml = '';
                             $mobileHtml = '';
                             $mailSentHtml = '';
                             $bidHtml = '';
                             $signedHtml = '';
                             foreach($trade->sc_vendors as $tsk =>  $subcontractor){
                               $checked = ($subcontractor->mail_sent == true ) ? 'checked=checked' : '';
                               $selectedTrueBid = $subcontractor->bid_recieved == \App\Models\SoftCostITBTracker::TRUE ? "selected" : "";
                               $selectedFalseBid = $subcontractor->bid_recieved == \App\Models\SoftCostITBTracker::FALSE ? "selected" : "";
                               $selectedTrueSigned = $subcontractor->contract_sign == \App\Models\SoftCostITBTracker::TRUE ? "selected" : "";
                               $selectedFalseSigned = $subcontractor->contract_sign == \App\Models\SoftCostITBTracker::FALSE ? "selected" : "";
                               $subcontractorHtml .=  "<input class='checkbox subcontractor' type='checkbox' value='$trade->id,$subcontractor->id'   $checked ><span>$subcontractor->name</span> </br>";

                               $emailHtml .= " $subcontractor->email </br>";
                               $mobileHtml .= " $subcontractor->mobile </br>";
                               $mailSentHtml .= @\App\Models\SoftCostITBTracker::$ITBArr[$subcontractor->mail_sent]."
                                </br>"; 
                                $bidHtml .= "
                                 <select onchange='selectBid(this.value,$subcontractor->tracker_id)'> 
                                  <option value=''>Select</option>
                                   <option value=".\App\Models\SoftCostITBTracker::TRUE." $selectedTrueBid>".\App\Models\SoftCostITBTracker::TRUE_TEXT." </option>
                                   <option value=".\App\Models\SoftCostITBTracker::FALSE." $selectedFalseBid>".\App\Models\SoftCostITBTracker::FALSE_TEXT." </option>
                                  </select></br>"; 

                                 $signedHtml .= "<select onchange='selectSign(this.value,$subcontractor->tracker_id)'> 
                                  <option value=''>Select</option>
                                   <option value=".\App\Models\SoftCostITBTracker::TRUE." $selectedTrueSigned>".\App\Models\SoftCostITBTracker::TRUE_TEXT." </option>
                                   <option value=".\App\Models\SoftCostITBTracker::FALSE." $selectedFalseSigned>".\App\Models\SoftCostITBTracker::FALSE_TEXT." </option>
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
</div>

 <div class="tab-pane" id="proposals" role="tabpanel" aria-expanded="true">
                           
    <div class="row mb-2">
        <div class="col-6">
            <h4 class="mt-0 text-left">{{ @$project->name }} - Proposals List </h4>
        </div>
        <div class="col-6 text-right">

            <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='{{ route("projects.proposals",['id' => request()->project, 
              'trade' => @$trade ])  }}'"> Add Proposal
            </button>
        </div>

    </div>
  <div class="row mb-2">
        <div class="col-5">
       
            <select class="form-control" onchange="return window.location.href='?trade='+this.value+'#proposals'" name="trade"> 
              @foreach($trades as $trd)
               <option value="{{ $trd->id }}" 
                {{ ($trd->id == $trade) ? 'selected=""' : ''}}
                >{{ $trd->name}}
               </option>
              @endforeach
            </select>
        </div>
    </div>
    
<div id="proposals-list" class="row py-3">
   @foreach($proposals as $proposal)
   @php
     $bidTotal = (float) $proposal->material + (float) $proposal->labour_cost + (float) $proposal->subcontractor_price
   @endphp
   <div class="col-lg-4 col-sm-6 mb-4" style="display: flex; flex-wrap: wrap;">
      <div class="card card-user card-table-item" style="width: 100%; height: 100%;">
         <div class="card-header text-center">
            <div class="author mt-1">
               <img class="avatar border-success" src="{{ ($proposal->subcontractor->image) ? url(\Storage::url($proposal->subcontractor->image)) : asset('img/image_placeholder.png')  }}">             
               <h5 class="card-title">{{ @$proposal->subcontractor->name}}</h5>
            </div>
         </div>
         <div class="card-body">
            <p class="mb-1">Materials: ${{ \App\Models\Payment::format($proposal->material)}}</p>
            <p class="mb-1">Labor Cost: ${{ \App\Models\Payment::format($proposal->labour_cost)}}</p>
            <p class="mb-1">Subcontractor Price: ${{ \App\Models\Payment::format($proposal->subcontractor_price)}}</p>
            <p class="mb-1">Original Bid Total: <b>${{ \App\Models\Payment::format( $bidTotal )}}</b></p>
            <p class="card-text">Notes: {{ $proposal->notes}}</p>
            @foreach($proposal->changeOrders as $k => $order)
              @php 
              if($order->type == \App\Models\ChangeOrder::ADD ){
                 $bidTotal += $order->subcontractor_price;
               }
               else{
                 $bidTotal -= $order->subcontractor_price;
               }
              @endphp
              <p class="mb-1">Change Order {{ $k+1}} : {{$order->type == \App\Models\ChangeOrder::ADD  ? '+' : '-'}} ${{ \App\Models\Payment::format($order->subcontractor_price)}}</p>
               <p class="card-text">Notes: {{ $order->notes}}</p>
            @endforeach
             <p class="mb-1">Trade Budget: <b>${{ \App\Models\Payment::format(@$proposal->trade_budget) }}</b></p>
             <p class="mb-1">Grand Total: <b>${{ \App\Models\Payment::format($bidTotal) }}</b></p>
            <div class="row">
               <div class="col-6">
                  <p class="card-text">Files</p>
               </div>

               <div class="col-6">
                <form 
                class="file_form" action="{{route('projects.proposals.upload',['id' => $proposal->id]).'#proposals'}}" 
                method="post" 
                enctype="multipart/form-data">
                 @csrf
                <input class="uploadImage" type="file" style="display: none;" 
                name="file" />
                <a class="add_file text-danger pull-right" style="cursor: pointer;">
                 Add File
                </a>
              </form>
               
              </div>

               <div class="col-12  text-right">
          
                @if(!empty($proposal->files))
                 @foreach(@explode(',',$proposal->files) as $file)
   
                        @php
                           $fileInfo = pathinfo($file); 
                             $extension = @$fileInfo['extension'];
                          
                              if(in_array($extension,['doc','docx','docm','dot',
                            'dotm','dotx'])){
                                $extension = 'word'; 
                             }
                             else if(in_array($extension,['csv','dbf','dif','xla',
                            'xls','xlsb','xlsm','xlsx','xlt','xltm','xltx'])){
                                $extension = 'excel'; 
                             }
                           
                            if(!$extension){
                              $extension = 'pdf';
                            }

                        @endphp
                        <a href="{{ url($file) }}" target="_blank">
                      <img class="avatar border-gray proposal_file" 
                      src="{{ asset('img/'.$extension.'.png') }}">
                      </a>

                 @endforeach

                 @endif

               </div>

            </div>
         </div>
         <div class="card-footer text-center">
            <div class="btn-group-sm" role="group" aria-label="Basic example">

              @php  
                 $awarded = @$project->proposals()->whereTradeId($proposal->trade_id)
                            ->IsAwarded()->exists();
              @endphp
            <button onclick="return window.location.href='{{ route("projects.proposals.award",
            ['id' => $proposal->id, 'status' => $proposal->awarded ])  }}'" type="button" class="btn btn-success"  {{ ($proposal->awarded == 0 && $awarded == 1 ) ? 'disabled' : '' }}>
            {{ ($proposal->awarded == 1) ? 'Retract' : 'Award'  }}
          </button>                
            <button onclick="return window.location.href='{{ route("projects.proposals.edit",
            ['id' => $proposal->id ])  }}'" type="button" class="btn btn-warning">Edit</button> 
            <form  style="display: initial;" 
              method="post" 
              action="{{route('projects.proposals.destroy',['id' => $proposal->id]).'#proposals'}}"> 
               @csrf
              {{ method_field('DELETE') }}
              <button 
                type="submit"
                onclick="return confirm('Are you sure?')"
                class="btn btn-danger btn-sm" >Remove </button>
            </form>              
         </div>
      </div>
   </div>
   </div>
   @endforeach


</div>
</div>
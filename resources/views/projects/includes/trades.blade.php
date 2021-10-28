 <div class="tab-pane" id="trades" role="tabpanel" aria-expanded="true">
                           
    <div class="row mb-2">
        <div class="col-6">
            <h4 class="mt-0 text-left">{{ @$project->name }} - Trades List </h4>
        </div>
        <div class="col-6 text-right">

            <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='{{ route("projects.trades",['id' => request()->project ])  }}'">Add Trade
            </button>
        </div>

    </div>
  <div class="row mb-2">
        <div class="col-12">
            <form method="post" action="{{ route('projects.trades.multiple', [ 'id' => request()->project]) }}"> 
              @csrf
            <select style="height: 26px;"  name="project_id"> 
              <option value=""> Select Project</option>
              @foreach($projects as $project)
               <option value="{{ $project->id }}" >{{ $project->name}}
               </option>
              @endforeach
            </select>
            <button >Assign Trades from other projects</button>
          </form>
        </div>
    </div>
  

    <!-- Categories Table -->
    <div class="table-responsive">

       <table id="project-types-table" class="table table-hover text-center">
                        <thead>
                        <tr class="text-danger">
                            <th>Acc. No.</th>
                            <th>Trade</th>
                            <th>Delete</th>
                        </tr>
                        </thead>
                        <tbody>
                          @foreach($trades as $type)
                         <tr>
                           <td> {{ $type->account_number }}</td>
                           <td>{{ $type->name }}</td>
                         
                          <td>
                             <form 
                              method="post" 
                              action="{{route('projects.trades.destroy',['project_id' => request()->project,'id' => $type->id])}}"> 
                               @csrf
                              {{ method_field('DELETE') }}

                              <button 
                                type="submit"
                                onclick="return confirm('Are you sure?')"
                                class="btn btn-neutral bg-transparent btn-icon" data-original-title="Delete Trade" title="Delete Bussiness Type"><i class="fa fa-trash text-danger"></i> </button>
                            </form>
                           </td>
                         </tr> 
                         @endforeach
                        <!-- Project Types Go Here -->
                        </tbody>
                    </table>

    </div>

</div>
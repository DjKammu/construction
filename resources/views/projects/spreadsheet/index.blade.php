@extends('layouts.admin-app')

@section('title', 'Edit Project')

@section('content')

<script src="{{ asset('js/spreadsheet.js') }}"></script>
<link href="{{ asset('css/spreadsheet.css') }}"  rel="stylesheet" />
<div class="row">
@include('includes.back', 
['url' => route("projects.show", ['project' => request()->project]),
'to' => 'to Project'])

</div>

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

              @if(session()->has('error'))
                <div class="alert alert-warning alert-dismissible fade show">
                  <strong>Error!</strong> {{ session()->get('error') }}
                  <button type="button" class="close" data-dismiss="alert">&times;</button>
              </div>
            @endif

            <div class="card-body">
               <div class="row">
                        <div class="col-md-12">
                               <div class="row">
                                <div class="col-6">
                                    <h4 class="mt-0 text-left">{{ @$project->name }} - Spreadsheet </h4>
                                </div>
                              
                                 <!-- <div class="col-6 text-right">
                                    <button type="button" class="btn btn-danger mt-0"  onclick="sendEmailPopup()">
                                      Send Email
                                    </button>

                                    <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='budget/pdf/download'">Download
                                    </button> 

                                     <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='budget/excel/download'">Download to Excel
                                    </button>
                                </div> -->
                            </div>
                             
                             <!-- <div class="row">
                                <div class="col-8">
                                    <form method="post" action="{{ route('projects.gantt.other.assign', [ 'project' => request()->project]) }}"> 
                                      @csrf
                                     <select style="height: 26px;" onchange="return window.location.href = '?project_type='+this.value" name="project_type"> 
                                      <option value="">Select Project Type</option>
                                      @foreach($projectTypes as $type)
                                         <option value="{{ $type->slug }}" {{ (@request()->project_type == $type->slug) ? 'selected' : ''}}> {{ $type->name }}</option>
                                      @endforeach
                                      </select>
                                    <select style="height: 26px;"  name="project_id"> 
                                      <option value=""> Select Project</option>
                                      @foreach($projects as $p)
                                       <option value="{{ $p->id }}" >{{ $p->name}}
                                       </option>
                                      @endforeach
                                    </select>
                                    <button >Add Gantt from other projects</button>
                                  </form>
                                </div>
                                <div class="col-4">
                                  
                                </div>
                            </div> -->
            <style>
      .dhx_sample-container__widget {
          max-width: 100%;
          
        }
        .dhx_sample-container__widget{
        max-height: 800px;
      }
      .dxi-save:before{

        content: "\f0c7";
        
      }
    </style>
            <div class="dhx_sample-container__widget" id="spreadsheet"></div>
                <script>

                   var data = '{!! json_encode($spreadsheet)  !!}';

                  const spreadsheet = new dhx.Spreadsheet("spreadsheet", {
                    menu: true // the menu is switched on, false - to switch it off
                  });

                    spreadsheet.toolbar.data.add({
                    type: "button",
                    icon: "dxi dxi-content-save",
                    tooltip: "Save",
                    id: "save"
                });
                   
                 spreadsheet.parse(JSON.parse(data));

                spreadsheet.toolbar.events.on("click", function (id) {
                  var state = spreadsheet.serialize();
                         if (id !== "save") {
                          return;
                         }

                        if(!state){
                          alert('Sheet cant be blank')
                          return
                        }
                        
                        let projectId = '{{ @$project->id }}';

                        let _token   =   "{{ csrf_token() }}";

                        let url = 'spreadsheet/do'

                       $.ajax({
                          url: url,
                          type:"POST",
                          data:{
                              state: JSON.stringify(state),
                             //state: state,
                            _token: _token
                          },
                          success:function(response){
                             alert(response.message); 
                             // $("#myModal").modal('hide');
                             // location.reload();
                          },
                          error: function(error) {
                            alert(error);
                          }
                         });
                })

                </script>


            </div>
        </div>
    </div>
</div>


@endsection

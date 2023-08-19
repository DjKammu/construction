@extends('layouts.admin-app')

@section('title', 'Edit Project')

@section('content')
  <link href="{{ asset('css/gantt.css') }}"  rel="stylesheet" />
  <script src="{{ asset('js/gantt.js') }}"></script>

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
                                    <h4 class="mt-0 text-left">{{ @$project->name }} - Gantt </h4>
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
                             
                             <div class="row">
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
                            </div>

                             <div id="gantt_here" style='width:100%; height:100%;'></div>
                              <script type="text/javascript">
                                gantt.config.date_format = "%Y-%m-%d %H:%i:%s";
                                gantt.config.order_branch = true;/*!*/
                                gantt.config.order_branch_free = true;/*!*/
                                gantt.init("gantt_here");

                                gantt.load("gantt/get/data");

                                const dp = gantt.createDataProcessor({
                                  url: "gantt/do",
                                  mode: "REST"
                                });
                                </script>

            </div>
        </div>
    </div>
</div>


@endsection

@section('pagescript')

<script type="text/javascript">

  function sendEmailPopup(){   
      $("#myModal").modal('show');
  }

   function sendMail(){
   
    var recipient = $('#recipient').val();
    var subject = $('#subject').val();
    var message = $('#message').val();
    var file = $('#file').val();
     var cc = $('#cc').val();
    var bcc = $('#bcc').val();


    const validateEmail = (email) => {
    return String(email)
      .toLowerCase()
      .match(
        /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
      );
  };


    if(!recipient){
      alert('Recipient cant be blank')
      return
    }else if(!validateEmail(recipient)) {
        alert('Recipient is invalid')
      return
  
    }else if(!subject){
      alert('Subject cant be blank')
      return
    } else if(!message){
      alert('Message cant be blank')
      return
    }
    
    let projectId = '{{ @$project->id }}';

    let _token   =   "{{ csrf_token() }}";

    let url = '/projects/'+projectId+'/budget/send-mail'

   $.ajax({
        url: url,
        type:"POST",
        data:{
          recipient:recipient,
          subject:subject,
          message:message,
          file:file,
          cc:cc,
          bcc:bcc,
          _token: _token
        },
        success:function(response){
           alert(response.message); 
           $("#myModal").modal('hide');
           location.reload();
        },
        error: function(error) {
          alert(error);
        }
       });

   }


</script>
<style type="text/css">
  
#gantt_here{
  max-height: 500px;
}

span.cross{
    position: absolute;
    z-index: 10;
    right: 30px;
    display: none;
}
tr:hover span.cross{
  display: block;
}
button.btn.btn-neutral.bg-transparent.btn-icon{
  background-color: transparent !important;
}
#documents td{
  width: 100%;
}

span.doc-type{
 font-size: 12px;
 padding-top: 8px;
 display: block;
}

span.doc_type_m{
 font-size: 10px;
 padding-top: 3px;
 display: block;
}

.btn-group-sm .btn{
    padding: .25rem .5rem;
    font-size: .875rem;
    line-height: 1.5;
    border-radius: .2rem;
}
.avatar.proposal_file{
    width: 30px;
    height: 30px;
}


.list span {
    
    text-align: left;
    display: table-cell;
    vertical-align: middle;
    border-bottom: 1px solid #dee2e6;
    border-top: 1px solid #dee2e6;
    border-left: 1px solid #dee2e6;
    
}

.list li span:last-child {    
    border-right: 1px solid #dee2e6;   
}

.list p {
    font-size: 16px;
    padding: 12px 7px;
    vertical-align: middle;
    border-top: 1px solid #dee2e6;
    border-left: 1px solid #dee2e6;
    margin: 0 !important;
    
}

.list .h6 {
    margin-bottom: 0;  
}

.list li p:last-child {    
    border-right: 1px solid #dee2e6;   
}

.list {
    
    list-style: none;
    margin: 0;
    padding: 0;
    display: grid;
    white-space: nowrap;
    width: 100%;
    
}

.list li {  
    color: #5c5c5c;
}

.list li.multi-line{
 display: inline-table;
}

.list li.single-line{
 /*display: table-caption;*/
}

span.awarded-green, span.awarded-green a{
    /*background: #38ef38;*/
    /*color: #fffdfa;*/
    color: #038303;
    font-weight: 800;
    text-decoration: none;
}

.list li span.bid-text{
      font-size: 12px;
      padding: 4px 0px;
} 


table.payments-table{
      font-size: 12px;
      font-family: Arial;
}

table.payments-table thead>tr>th{
   font-size: 12px;
}
i.fa.fa-sort-desc {
    position: relative;
    left: -8px;
    cursor: pointer;
    top: 1px;
}
i.fa.fa-sort-asc{
  position: relative;
    left: 4px;
    cursor: pointer;
    top: -2px;
}
.sorting-outer{
  position: absolute;
}

.sorting-outer a{
  color: #ef8157 ;
}
.table-responsive.table-payments{
  overflow: auto;
}

  #category-types-table{
    font-size: 12px;
  }
  .checkbox{
    margin-right: 4px;
  }

    .budget-image{
    float: left;
    margin-top: 5px;
  }
  .budget-image .avatar.proposal_file{
    height: 15px;
    width: 15px;
  }
  
</style>

@endsection
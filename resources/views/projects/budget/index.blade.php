@extends('layouts.admin-app')

@section('title', 'Edit Project')

@section('content')

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
                                    <h4 class="mt-0 text-left">{{ @$project->name }} - Budget </h4>
                                </div>
                              
                                 <div class="col-6 text-right">
                                    <button type="button" class="btn btn-danger mt-0"  onclick="sendEmailPopup()">
                                      Send Email
                                    </button>

                                    <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='ffe/budget/download'">Download
                                    </button>
                                </div>
                            </div>

                             <div class="row">
                                <div class="col-8">
                                    <form method="post" action="{{ route('projects.budget.other.assign', [ 'project' => request()->project]) }}"> 
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
                                    <button >Add Budget from other projects</button>
                                  </form>
                                </div>
                                <div class="col-4">
                                  <h6>Total Construction SQ Ft -
                                    {{ @$project->total_construction_sq_ft }}
                                  </h6>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                             <project-budget v-bind:project="{{ @json_encode($project) }}" total_construction_sq_ft="{{ @$project->total_construction_sq_ft }}" projectid="{{ @$project->id }}"></project-budget>
                        </div>


                    </div>
            </div>
        </div>
    </div>
</div>

@endsection


@section('pagescript')
@include('includes.vue-js')
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<script type="text/javascript">

  $(document).ready(function(){

    $('#search').click(function(){
          var search = $('#inputSearch').val();
          window.location.href = '?s='+search;
    });

    $(document).keyup(function(event) {
      if (event.keyCode === 13) {
          $("#search").click();
      }
    });
  
    $(".btn-close").click(function(){  
            $("#myModal").modal('hide');
        });

  });

$('.date').datetimepicker({
    format: 'Y-M-D'
});

var start =  '{{ Request::input("start")}}';
var end =  '{{ Request::input("end")}}';

 $('input[name="daterange"]').daterangepicker({

    startDate: (start) ? start :   moment().startOf('month'),
    endDate: (end) ? end :  moment().startOf('day'),
    locale: {
      format: 'YYYY-MM-DD'
    }
  }).on('apply.daterangepicker', function(ev, picker) {
      var fullUrl = window.location.href.split("#")[0];
      let isStart = fullUrl.includes('start') ;
      let isEnd = fullUrl.includes('end') ;
      
      var url = '/';
      if(isStart || isEnd){ 
          fullUrl = replaceUrlParam(fullUrl,'start',picker.startDate.format('YYYY-MM-DD'));
          fullUrl = replaceUrlParam(fullUrl,'end',picker.endDate.format('YYYY-MM-DD'));
          url = fullUrl;
      }
      else{
        url = fullUrl+(fullUrl.includes('?')?'&':'?')+'start='+picker.startDate.format('YYYY-MM-DD')+'&end='+picker.endDate.format('YYYY-MM-DD')
      }

      url = url+'#rfi';
       window.location.href = url;
  }); 
   
   var submittal_start =  '{{ Request::input("submittal_start")}}';
  var submittal_end =  '{{ Request::input("submittal_end")}}';


  $('input[name="daterange-submittal"]').daterangepicker({

    startDate: (submittal_start) ? submittal_start :   moment().startOf('month'),
    endDate: (submittal_end) ? submittal_end :  moment().startOf('day'),
    locale: {
      format: 'YYYY-MM-DD'
    }
  }).on('apply.daterangepicker', function(ev, picker) {
      var fullUrl = window.location.href.split("#")[0];
      let isStart = fullUrl.includes('submittal_start') ;
      let isEnd = fullUrl.includes('submittal_end') ;
      
      var url = '/';
      if(isStart || isEnd){ 
          fullUrl = replaceUrlParam(fullUrl,'submittal_start',picker.startDate.format('YYYY-MM-DD'));
          fullUrl = replaceUrlParam(fullUrl,'submittal_end',picker.endDate.format('YYYY-MM-DD'));
          url = fullUrl;
      }
      else{
        url = fullUrl+(fullUrl.includes('?')?'&':'?')+'submittal_start='+picker.startDate.format('YYYY-MM-DD')+'&submittal_end='+picker.endDate.format('YYYY-MM-DD')
      }

      url = url+'#submittal';
       window.location.href = url;
  });


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

    let url = '/projects/'+projectId+'/ffe/send-mail'

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

  function sendEmailLogsPopup(){   
      $("#myModalLogs").modal('show');
   }

   function sendMailLogs(){
   
    var recipient = $('#recipient2').val();
    var subject = $('#subject2').val();
    var message = $('#message2').val();
    var file = $('#file2').val();
     var cc = $('#cc2').val();
    var bcc = $('#bcc2').val();


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

    let url = '/projects/'+projectId+'/ffe/send-mail-logs'

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

  function selectPerpage(perPage){
       var fullUrl = window.location.href;
       let isPerpage = '{{ Request::input("per_page")}}';

       if(!isPerpage){
          let url = fullUrl;
         if(location.hash){
          fullUrl = location.href.replace(location.hash,"");
          url = fullUrl+(fullUrl.includes('?')?'&':'?')+'per_page='+perPage+location.hash;
         }else{
         url = fullUrl+(fullUrl.includes('?')?'&':'?')+'per_page='+perPage;
         }
         window.location.href = url;
       }
       else if(isPerpage != perPage){
         window.location.href = fullUrl.replace(isPerpage, perPage)
       }
  } 

  const loc = new URL(window.location.href) || null

  if (loc !== null) {
    if (loc.hash !== '') {
      $('.nav-tabs li a').removeClass('active')
      $(loc.hash).addClass('active')
       $(`a[href="${ loc.hash }"]`).tab('show')
    }
  }

  $('a[data-toggle="tab"]').on("click", function() {
    let url = location.href.replace(/\/$/, "");
    let newUrl;
    const hash = $(this).attr("href");
    if(hash == "#details") {
      newUrl = url.split("#")[0];
    } else {
      newUrl = url.split("#")[0] + hash;
    }
    history.replaceState(null, null, newUrl);
  });

 $('.add_file').click(function(){
   $(this).siblings('.uploadImage').click();
 });

 $(".uploadImage").change(function() {
    $(this).parent('.file_form').submit();
  });


  function sortOrderBy(orderBy,order){
       
      var fullUrl = window.location.href.split("#")[0];
      let isOrderBy = fullUrl.includes('orderby') ;
      let isSort = fullUrl.includes('order') ;
      
      var url = '/';
      if(isOrderBy || isSort){ 
          fullUrl = replaceUrlParam(fullUrl,'orderby',orderBy);
          fullUrl = replaceUrlParam(fullUrl,'order',order);
          url = fullUrl;
      }
      else{
         url = fullUrl+(fullUrl.includes('?')?'&':'?')+'orderby='+orderBy+'&order='+order
      }
       url = url+'#payments';
       window.location.href = url;

 } 

  function sortOrderByRFI(orderBy,order){
       
      var fullUrl = window.location.href.split("#")[0];
      let isOrderBy = fullUrl.includes('orderbyRFI') ;
      let isSort = fullUrl.includes('orderRFI') ;
      
      var url = '/';
      if(isOrderBy || isSort){ 
          fullUrl = replaceUrlParam(fullUrl,'orderbyRFI',orderBy);
          fullUrl = replaceUrlParam(fullUrl,'orderRFI',order);
          url = fullUrl;
      }
      else{
         url = fullUrl+(fullUrl.includes('?')?'&':'?')+'orderbyRFI='+orderBy+'&orderRFI='+order
      }
       url = url+'#rfi';
       window.location.href = url;

 }  

 function sortOrderBySubmittal(orderBy,order){
       
      var fullUrl = window.location.href.split("#")[0];
      let isOrderBy = fullUrl.includes('orderbySubmittal') ;
      let isSort = fullUrl.includes('orderSubmittal') ;
      
      var url = '/';
      if(isOrderBy || isSort){ 
          fullUrl = replaceUrlParam(fullUrl,'orderbySubmittal',orderBy);
          fullUrl = replaceUrlParam(fullUrl,'orderSubmittal',order);
          url = fullUrl;
      }
      else{
         url = fullUrl+(fullUrl.includes('?')?'&':'?')+'orderbySubmittal='+orderBy+'&orderSubmittal='+order
      }
       url = url+'#submittal';
       window.location.href = url;

 } 

 function sortOrderByLog(orderBy,order){
       
      var fullUrl = window.location.href.split("#")[0];
      let isOrderBy = fullUrl.includes('orderbySubmittal') ;
      let isSort = fullUrl.includes('orderLog') ;
      
      var url = '/';
      if(isOrderBy || isSort){ 
          fullUrl = replaceUrlParam(fullUrl,'orderbySubmittal',orderBy);
          fullUrl = replaceUrlParam(fullUrl,'orderLog',order);
          url = fullUrl;
      }
      else{
         url = fullUrl+(fullUrl.includes('?')?'&':'?')+'orderByLog='+orderBy+'&orderLog='+order
      }
       url = url+'#logs';
       window.location.href = url;

 }


  function replaceUrlParam(url, paramName, paramValue)
    {
        if (paramValue == null) {
            paramValue = '';
        }
        var pattern = new RegExp('\\b('+paramName+'=).*?(&|#|$)');
        if (url.search(pattern)>=0) {
            return url.replace(pattern,'$1' + paramValue + '$2');
        }
        url = url.replace(/[?#]$/,'');
        return url + (url.indexOf('?')>0 ? '&' : '?') + paramName + '=' + paramValue;
    }

function proposalPage(id){
        var fullUrl = window.location.href.split("?")[0];
        fullUrl = fullUrl.split("#")[0];
        url = fullUrl+'?trade='+id+'#proposals';
        window.location.href = url;
}
   
   var senders = [];
$('.subcontractor').click(function() {
    var checked = ($(this).val());
    if ($(this).is(':checked')) {
      senders.push(checked);
    } else {
      senders.splice($.inArray(checked, senders),1);
    }
  });
function sendMailTracker(){
   
   if(senders.length == 0 ){
      alert('Select atleast one');
      return;
    }

    let projectId =  '{{ @$project->id }}';
    let _token   =   "{{ csrf_token() }}";

   $.ajax({
        url: "{{ route('ffe.send.mail')}}",
        type:"POST",
        data:{
          projectId:projectId,
          senders:senders,
          _token: _token
        },
        success:function(response){
           alert(response.message); 
           location.reload();
        },
        error: function(error) {
          alert(error);
        }
       });
  
}

function selectSign(val, id){
 
   if(val == null ){
      alert('Select for Contract Sign');
      return;
    }

    let tracker_id = id;
    let _token   =   "{{ csrf_token() }}";

   $.ajax({
        url: "{{ route('ffe.contract.signed')}}",
        type:"POST",
        data:{
          tracker_id:tracker_id,
          value :val,
          _token: _token
        },
        success:function(response){
           alert(response.message); 
           // location.reload();
        },
        error: function(error) {
          alert(error);
        }
       });
  
}

function selectBid(val, id){
 
   if(val == null ){
      alert('Select for Bid Recieved');
      return;
    }

    let tracker_id = id;
    let _token   =   "{{ csrf_token() }}";

   $.ajax({
        url: "{{ route('ffe.bid.recieved')}}",
        type:"POST",
        data:{
          tracker_id:tracker_id,
          value :val,
          _token: _token
        },
        success:function(response){
           alert(response.message); 
           // location.reload();
        },
        error: function(error) {
          alert(error);
        }
       });
  
}

</script>
<style type="text/css">
  
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
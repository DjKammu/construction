@extends('layouts.admin-app')

@section('title', 'Edit Project')

@section('content')

<div class="row">

@include('includes.back')

</div>

@include('includes.favourite')
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
                            <div class="card-body">

                              <div class="nav-tabs-navigation">
                                    <div class="nav-tabs-wrapper">
                                        <ul id="tabs" class="nav nav-tabs" role="tablist">

                                            <li class="nav-item">
                                                <a class="nav-link text-dark active"  data-toggle="tab" href="#details" role="tab"
                                                   aria-expanded="true">Details</a>
                                            </li>

                                            <li class="nav-item">
                                                <a class="nav-link text-dark"  data-toggle="tab" href="#documents" role="tab"
                                                   aria-expanded="false">Documents</a>
                                            </li>

                                            <li class="nav-item">
                                                <a class="nav-link text-dark"  data-toggle="tab" href="#trades" role="tab"
                                                   aria-expanded="false">Trades</a>
                                            </li> 
                                             @if($trade)
                                              <li class="nav-item">
                                                  <a class="nav-link text-dark"  data-toggle="tab" href="#proposals" role="tab"
                                                     aria-expanded="false">Proposals</a>
                                              </li>
                                              @endif

                                              @if($allProposals->count() > 0)
                                              <li class="nav-item">
                                                  <a class="nav-link text-dark"  data-toggle="tab" href="#bids" role="tab"
                                                     aria-expanded="false">Bids Tabulation</a>
                                              </li>
                                              @endif 

                                              <li class="nav-item">
                                                  <a class="nav-link text-dark"  data-toggle="tab" href="#bills" role="tab"
                                                     aria-expanded="false">Bills</a>
                                              </li>

                                              <li class="nav-item">
                                                  <a class="nav-link text-dark"  data-toggle="tab" href="#payments" role="tab"
                                                     aria-expanded="false">Payments</a>
                                              </li>
                                              
                                              <li class="nav-item">
                                                  <a class="nav-link text-dark"  data-toggle="tab" href="#budget" role="tab"
                                                     aria-expanded="false">Hard Project Cost</a>
                                              </li>
                                              <li class="nav-item">
                                                  <a class="nav-link text-dark"   href="{{ url('projects/'.$project->id.'/soft-cost') }}" 
                                                     aria-expanded="false">Soft Project Cost</a>
                                              </li>
                                              <li class="nav-item">
                                                  <a class="nav-link text-dark"  data-toggle="tab" href="#construction-cost" role="tab"
                                                     aria-expanded="false">Total Construction Cost</a>
                                              </li>

                                               <li class="nav-item">
                                                  <a class="nav-link text-dark" href="{{ url('projects/'.$project->id.'/budget') }}"  role="tab"
                                                     aria-expanded="false">Project Budget </a>
                                              </li>

                                              <li class="nav-item">
                                                  <a class="nav-link text-dark" href="{{ url('projects/'.$project->id.'/gantt') }}"  role="tab"
                                                     aria-expanded="false">Gantt </a>
                                              </li>

                                             <li class="nav-item">
                                                  <a class="nav-link text-dark" href="{{ url('projects/'.$project->id.'/schedule') }}"  role="tab" aria-expanded="false">Schedule </a>
                                              </li>
                                              <li class="nav-item">
                                                  <a class="nav-link text-dark" href="{{ url('projects/'.$project->id.'/spreadsheet ') }}"  role="tab" aria-expanded="false">Spreadsheet  </a>
                                              </li>

                                              <li class="nav-item">
                                                  <a class="nav-link text-dark"  data-toggle="tab" href="#rfi" role="tab"
                                                     aria-expanded="false">RFI</a>
                                              </li>
                                              <li class="nav-item">
                                                  <a class="nav-link text-dark"  data-toggle="tab" href="#submittal" role="tab"
                                                     aria-expanded="false">Submittal</a>
                                              </li>
                                              <li class="nav-item">
                                                   <a class="nav-link text-dark"  data-toggle="tab" href="#logs" role="tab"
                                                     aria-expanded="false">Procurement Log</a>
                                              </li>

                                              <li class="nav-item">
                                                   <a class="nav-link text-dark"  data-toggle="tab" href="#inspection" role="tab"
                                                     aria-expanded="false">Inspection</a>
                                              </li>

                                              <li class="nav-item">
                                                  <a class="nav-link text-dark" href="{{ url('projects/'.$project->id.'/ffe') }}"  role="tab"
                                                     aria-expanded="false">FFE </a>
                                              </li>
                                               
                                              <li class="nav-item">
                                                  <a class="nav-link text-dark"   href="{{ url('projects/'.$project->id.'/aia-pay-app') }}" role="tab"
                                                     aria-expanded="false">AIA Pay App</a>
                                              </li> 
                                              <li class="nav-item">
                                                  <a class="nav-link text-dark"  data-toggle="tab" href="#tracker" role="tab"
                                                     aria-expanded="false">ITB Tracker</a>
                                              </li>
                                        </ul>
                                    </div>
                               </div>

                                <div id="my-tab-content" class="tab-content">

                                    @include('projects.includes.details')
                                    @include('projects.includes.documents')
                                    @include('projects.includes.trades')
                                    @if($trade)
                                    @include('projects.includes.proposals')
                                    @endif
                                    @if($allProposals->count() > 0)
                                    @include('projects.includes.bids')
                                    @endif 
                                    @include('projects.includes.payments')
                                    @include('projects.includes.bills')
                                    @include('projects.includes.budget')
                                    @include('projects.includes.construction-cost')
                                    @include('projects.includes.rfi')
                                    @include('projects.includes.submittal')
                                    @include('projects.includes.logs')
                                    @include('projects.includes.inspection')
                                    @include('projects.includes.tracker')
                              </div>

                            </div>
                            </div>
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

  var inspection_start =  '{{ Request::input("inspection_start")}}';
  var inspection_end =  '{{ Request::input("inspection_end")}}';


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


 $('input[name="daterange-inspection"]').daterangepicker({

    startDate: (inspection_start) ? inspection_start :   moment().startOf('month'),
    endDate: (inspection_end) ? inspection_end :  moment().startOf('day'),
    locale: {
      format: 'YYYY-MM-DD'
    }
  }).on('apply.daterangepicker', function(ev, picker) {
      var fullUrl = window.location.href.split("#")[0];
      let isStart = fullUrl.includes('inspection_start') ;
      let isEnd = fullUrl.includes('inspection_end') ;
      
      var url = '/';
      if(isStart || isEnd){ 
          fullUrl = replaceUrlParam(fullUrl,'inspection_start',picker.startDate.format('YYYY-MM-DD'));
          fullUrl = replaceUrlParam(fullUrl,'inspection_end',picker.endDate.format('YYYY-MM-DD'));
          url = fullUrl;
      }
      else{
        url = fullUrl+(fullUrl.includes('?')?'&':'?')+'inspection_start='+picker.startDate.format('YYYY-MM-DD')+'&inspection_end='+picker.endDate.format('YYYY-MM-DD')
      }

      url = url+'#inspection';
       window.location.href = url;
  });


   function sendEmailPopup(){   
      $("#myModal").modal('show');
   } 

   function sendEmailPopup2(type){
      $('#type').val(type);   
      $("#myModal2").modal('show');
   }

   function sendEmailLogsPopup(){
      $("#myModalLogs").modal('show');
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

    let url = '/projects/'+projectId+'/send-mail'

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


 function sendMail2(){
   
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

    let url = '/projects/'+projectId+'/total/send-mail'

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


 function sendMailLogs(){
   
    var recipient = $('#recipient3').val();
    var subject = $('#subject3').val();
    var message = $('#message3').val();
    var file = $('#file3').val();
    var cc = $('#cc3').val();
    var bcc = $('#bcc3').val();

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

    let url = '/projects/'+projectId+'/send-mail-logs'

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
           $("#myModalLogs").modal('hide');
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
      let isOrderBy = fullUrl.includes('orderByLog') ;
      let isSort = fullUrl.includes('orderLog') ;
      
      var url = '/';
      if(isOrderBy || isSort){ 
          fullUrl = replaceUrlParam(fullUrl,'orderByLog',orderBy);
          fullUrl = replaceUrlParam(fullUrl,'orderLog',order);
          url = fullUrl;
      }
      else{
         url = fullUrl+(fullUrl.includes('?')?'&':'?')+'orderByLog='+orderBy+'&orderLog='+order
      }
       url = url+'#logs';
       window.location.href = url;

 } 

 function sortOrderByInspection(orderBy,order){
       
      var fullUrl = window.location.href.split("#")[0];
      let isOrderBy = fullUrl.includes('orderByInspection') ;
      let isSort = fullUrl.includes('orderInspection') ;
      
      var url = '/';
      if(isOrderBy || isSort){ 
          fullUrl = replaceUrlParam(fullUrl,'orderByInspection',orderBy);
          fullUrl = replaceUrlParam(fullUrl,'orderInspection',order);
          url = fullUrl;
      }
      else{
         url = fullUrl+(fullUrl.includes('?')?'&':'?')+'orderByInspection='+orderBy+'&orderInspection='+order
      }
       url = url+'#inspection';
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
        let tradeId = '{{ request()->trade}}';
        if(tradeId == id){
           window.location = url;
           location.reload(true);
        }
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
        url: "{{ route('send.mail')}}",
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
        url: "{{ route('contract.signed')}}",
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
        url: "{{ route('bid.recieved')}}",
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
table .row-border{
    border: 2px solid;
}

  #category-types-table{
    font-size: 12px;
  }
  .checkbox{
    margin-right: 4px;
  }
  #construction-cost-content{
    table-layout: fixed;
  }

  #construction-cost-summary-content{
    table-layout: fixed;
  }

  table{
    table-layout: fixed;
  }

  .budget-image{
    float: left;
    margin-top: 5px;
  }
  .budget-image .avatar.proposal_file{
    height: 15px;
    width: 15px;
  }

  #payments-table thead>tr>th{
    font-size: 10px;
    padding: 10px 1px;
  }
  #construction-cost-summary-content thead>tr>th{
    font-size: 10px;
    padding: 10px 1px;
  }
  #construction-cost-content thead>tr>th{
    font-size: 10px;
    padding: 10px 1px;
  }

  .container {
  display: block;
  position: relative;
  padding-left: 35px;
  margin-bottom: 12px;
  cursor: pointer;
  font-size: 22px;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
  
}

/* Hide the browser's default checkbox */
.container input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

/* Create a custom checkbox */
.checkmark {
  position: absolute;
  top: 0;
  left: 0;
  height: 20px;
  width: 20px;
  background-color: #ddc3c3;
  border-radius: 15px;
}

/* On mouse-over, add a grey background color */
.container:hover input ~ .checkmark {
  background-color: #ccc;
}

/* When the checkbox is checked, add a blue background */
.container input:checked ~ .checkmark {
  background-color: #6bd098;
}

/* Create the checkmark/indicator (hidden when not checked) */
.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

/* Show the checkmark when checked */
.container input:checked ~ .checkmark:after {
  display: block;
}

/* Style the checkmark/indicator */
.container .checkmark:after {
  left: 8px;
  top: 4px;
  width: 5px;
  height: 10px;
  border: solid white;
  border-width: 0 3px 3px 0;
  -webkit-transform: rotate(45deg);
  -ms-transform: rotate(45deg);
  transform: rotate(45deg);
}

</style>

@endsection
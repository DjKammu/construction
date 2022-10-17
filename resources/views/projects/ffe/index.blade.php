@extends('layouts.admin-app')

@section('title', 'Edit Project')

@section('content')


<div class="row">
@include('includes.back', 
['url' => route("projects.show", ['project' => request()->project])])

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
                            <div class="card-body">

                              <div class="nav-tabs-navigation">
                                    <div class="nav-tabs-wrapper">
                                        <ul id="tabs" class="nav nav-tabs" role="tablist">
                                             <li class="nav-item">
                                                  <a class="nav-link text-dark active"  data-toggle="tab" role="tab" aria-expanded="true" href="#trades" >Trades</a>
                                              </li>
                                              @if(@$trade)
                                              <li class="nav-item">
                                                  <a class="nav-link text-dark"  data-toggle="tab" role="tab" aria-expanded="true" href="#proposals" >Proposals</a>
                                              </li>
                                              @endif

                                              @if(@$allProposals)
                                              <li class="nav-item">
                                                  <a class="nav-link text-dark"  data-toggle="tab" href="#bids" role="tab"
                                                     aria-expanded="false">Bids Tabulation</a>
                                              </li>
                                              @endif 

                                              <li class="nav-item">
                                                  <a class="nav-link text-dark"  data-toggle="tab" href="#payments" role="tab"
                                                     aria-expanded="false">Payments</a>
                                              </li>
                                              
                                              <li class="nav-item">
                                                  <a class="nav-link text-dark"  data-toggle="tab" href="#budget" role="tab"
                                                     aria-expanded="false">Budget</a>
                                              </li>

                                        </ul>
                                    </div>
                               </div>

                                <div id="my-tab-content" class="tab-content">
                                    @include('projects.ffe.trades')
                                    @if(@$trade)
                                    @include('projects.ffe.proposals')
                                    @endif
                                    @if(@$allProposals && @$allProposals->count() > 0)
                                     @include('projects.ffe.bids')
                                    @endif 
                                     @include('projects.ffe.payments')

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
</style>

@endsection
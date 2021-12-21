@extends('layouts.admin-app')

@section('title', 'Edit Project')

@section('content')

<div class="row">
       @include('includes.back')
      <!-- Start Main View -->

      <div class="col-6 text-right">
      <button type="button" class="btn btn-danger mt-0" onclick="return window.location.href='{{$project->id}}/add-project-lines'">Add Project Lines
      </button>
      </div>
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

                                              @if($awarded)
                                              <li class="nav-item">
                                                  <a class="nav-link text-dark"  data-toggle="tab" href="#payments" role="tab"
                                                     aria-expanded="false">Payments</a>
                                              </li>
                                              <li class="nav-item">
                                                  <a class="nav-link text-dark"  data-toggle="tab" href="#budget" role="tab"
                                                     aria-expanded="false">Budget</a>
                                              </li>
                                              @endif
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
                                    @if($awarded)
                                    @include('projects.includes.payments')
                                    @include('projects.includes.budget')
                                    @endif
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

  });

$('.date').datetimepicker({
    format: 'Y-M-D'
});


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

span.awarded-green{
    background: #38ef38;
    color: #fffdfa;
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
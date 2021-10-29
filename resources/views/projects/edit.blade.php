@extends('layouts.admin-app')

@section('title', 'Edit Project')

@section('content')

@include('includes.back')
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

                                              @if($proposals)
                                              <li class="nav-item">
                                                  <a class="nav-link text-dark"  data-toggle="tab" href="#bids" role="tab"
                                                     aria-expanded="false">Bids Tabulation</a>
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

                                    @if($proposals)
                                    @include('projects.includes.bids')
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
    padding: 6px;
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
    display: table;
    white-space: nowrap;
    width: 100%;
    
}

.list li {  
    color: #5c5c5c;
}

.list li.multi-line{
 display: table-row;
}

.list li.single-line{
 display: table-caption;
}


</style>

@endsection
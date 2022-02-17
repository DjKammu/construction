@extends('layouts.admin-app')

@section('title', 'Reports')


@section('content')
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

            @if(session()->has('error'))
                <div class="alert alert-warning alert-dismissible fade show">
                  <strong>Error!</strong> {{ session()->get('error') }}
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
                <div class="row mb-2">
                    <div class="col-6">
                        <h4 class="mt-0 text-left">Reports</h4>
                    </div>
                </div>


            <div class="card-body">
               <div class="row">
                  <div class="col-md-12">
                      <div class="card-body">

                        <div class="nav-tabs-navigation">
                              <div class="nav-tabs-wrapper">
                                  <ul id="tabs" class="nav nav-tabs" role="tablist">

                                      <li class="nav-item">
                                          <a class="nav-link text-dark active"  data-toggle="tab" href="#project-summary" role="tab"
                                             aria-expanded="true">Project Summary </a>
                                      </li>

                                      <li class="nav-item">
                                          <a class="nav-link text-dark"  data-toggle="tab" href="#subcontractor-payment" role="tab"
                                             aria-expanded="false">Sub Contractor / Vendor Payment</a>
                                      </li>

                                  </ul>
                              </div>
                         </div>

                          <div id="my-tab-content" class="tab-content">
                              @include('reports.project-summary')
                              @include('reports.subcontractor-payment')
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

function selectProject(project, cLass){
       let path = window.location.href.split('?')[0]
       path = path.split('#')[0]
       var projectType = $('.'+cLass).val();
        
       let url = ''; 
       if(projectType){
          url += 'pt='+projectType+'&';
       }
       url += 'p='+project;

       window.location.href = path+'?'+url;
      
       
  } 

function selectSubcontractor(trade, cLass, cLass2){
       let path = window.location.href.split('?')[0]
        path = path.split('#')[0]
       var projectType = $('.'+cLass).val();
       var project = $('.'+cLass2).val();
        
       let url = ''; 
       if(projectType){
          url += 'pt='+projectType+'&';
       }
       if(project){
          url += 'p='+project+'&';
       }
       url += 'sc='+trade;

       window.location.href = path+'?'+url;
         
  } 

function selectVendor(trade, cLass, cLass2){
       let path = window.location.href.split('?')[0]
        path = path.split('#')[0]
       var projectType = $('.'+cLass).val();
       var project = $('.'+cLass2).val();
        
       let url = ''; 
       if(projectType){
          url += 'pt='+projectType+'&';
       }
       if(project){
          url += 'p='+project+'&';
       }
       url += 'v='+trade;

       window.location.href = path+'?'+url;
      
       
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


</script>
@endsection
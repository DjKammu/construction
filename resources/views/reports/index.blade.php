@extends('layouts.admin-app')

@section('title', 'Reports')


@section('content')
  @include('includes.favourite')

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
                                      <li class="nav-item">
                                          <a class="nav-link text-dark"  data-toggle="tab" href="#project-by-status" role="tab"
                                             aria-expanded="false">Projects By Status</a>
                                      </li> 

                                      <li class="nav-item">
                                          <a class="nav-link text-dark"  data-toggle="tab" href="#unpaid-bills" role="tab"
                                             aria-expanded="false">Unpaid Bills</a>
                                      </li>
                                      <li class="nav-item">
                                          <a class="nav-link text-dark"  data-toggle="tab" href="#awarded-contracts" role="tab"
                                             aria-expanded="false">Awarded Contracts</a>
                                      </li>

                                  </ul>
                              </div>
                         </div>

                          <div id="my-tab-content" class="tab-content">
                              @include('reports.project-summary')
                              @include('reports.subcontractor-payment')
                              @include('reports.project-by-status')
                              @include('reports.unpaid-bills')
                              @include('reports.awarded-contracts')
                        </div>

                      </div>
                      </div>
                  </div>
              </div>
            </div>
        </div>
    </div>
</div>

 <div id="myModal" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
      <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
          <h3 id="myModalLabel">Send Mail</h3>
      </div>
      <div class="modal-body">
       
            <div class="form-group">
              <label for="recipient-name" class="col-form-label">Recipient:</label>
              <input type="email" class="form-control" id="recipient">
            </div>
            <div class="form-group">
            <label for="recipient-name" class="col-form-label">CC: <small>email with comma seperated
            </small></label>
            <input type="text" class="form-control" id="cc">
          </div>

          <div class="form-group">
            <label for="recipient-name" class="col-form-label">BCC: <small>email with comma seperated
            </small></label>
            <input type="text" class="form-control" id="bcc">
          </div>
            <div class="form-group">
              <label for="recipient-name" class="col-form-label">Subject:</label>
              <input type="text" class="form-control" id="subject">
            </div>
            <div class="form-group">
              <label for="message-text" class="col-form-label">Message:</label>
              <textarea class="form-control" id="message"></textarea>
            </div>
      
      </div>
      <div class="modal-footer">
          <button class="btn btn-close" data-dismiss="modal" aria-hidden="true">Close</button>
          <button class="btn btn-primary" onclick="sendMail()">Send</button>
      </div>
      </div>
      </div>

  </div>

@endsection

@section('pagescript')
<style type="text/css">
  
  table{
    table-layout: fixed;
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
    left: 2px;
    cursor: pointer;
    top: -2px;
}
.sorting-outer{
  position: absolute;
}

.sorting-outer a{
  color: #ef8157 ;
}
#awarded-contracts table th{
  border: 1px solid #000;
}

#awarded-contracts table img{
  height: 60px;
}

 table.payments-table thead>tr>th{
    font-size: 10px;
    padding: 10px 1px;
  }

</style>
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

function selectProperty(property, cLass){
       let path = window.location.href.split('?')[0]
       path = path.split('#')[0]
       var projectType = $('.'+cLass).val();
        
       let url = ''; 
       if(projectType){
          url += 'pt='+projectType+'&';
       }
       url += 'pr='+property;

       window.location.href = path+'?'+url;
       
  } 

function selectPropertyByStatus(property, cLass){
       let path = window.location.href.split('?')[0]
       path = path.split('#')[0]
       var status = $('.'+cLass).val();
        
       let url = ''; 
       if(status){
          url += 'st='+status+'&';
       }
       url += 'pr='+property;

       window.location.href = path+'?'+url;
       
  } 

  function selectProject(project, cLass, cLass2){
       let path = window.location.href.split('?')[0]
       path = path.split('#')[0]
       var projectType = $('.'+cLass).val();
       var property = $('.'+cLass2).val();
        
       let url = ''; 
       if(projectType){
          url += 'pt='+projectType+'&';
       }
       if(property){
          url += 'pr='+property+'&';
       }
       url += 'p='+project;

       window.location.href = path+'?'+url;
      
       
  }  

   function selectProjectByStatus(project, cLass, cLass2){
       let path = window.location.href.split('?')[0]
       path = path.split('#')[0]
       var status = $('.'+cLass).val();
       var property = $('.'+cLass2).val();
        
       let url = ''; 
       if(status){
          url += 'st='+status+'&';
       }

       if(property){
          url += 'pr='+property+'&';
       }
       url += 'p='+project;

       window.location.href = path+'?'+url;
      
  }   

   function selectByStatus(link, type = ''){
       let path = window.location.href.split('?')[0]
       path = path.split('#')[0]
       
       if(type){
        type = '-'+type;
       }
       var status = $('#status'+type).val();
       var property = $('#property'+type).val();
       var project = $('#project'+type).val();
       var manageby = $('#manage-by'+type).val();
       var propertygroup = $('#property-group'+type).val();
        
       let url = ''; 
       if(status){
          url += 'st='+status+'&';
       }

       if(property){
          url += 'pr='+property+'&';
       }
       if(project){
          url += 'p='+project+'&';
       }
       if(manageby){
          url += 'u='+manageby+'&';
       }
       if(propertygroup){
          url += 'pg='+propertygroup+'&';
       }
       url = url+link;

       window.location.href = path+'?'+url;
      
  } 

function selectSubcontractor(trade, cLass, cLass2, cLass3){
       let path = window.location.href.split('?')[0]
        path = path.split('#')[0]
       var projectType = $('.'+cLass).val();
       var property = $('.'+cLass2).val();
       var project = $('.'+cLass3).val();
        
       let url = ''; 
       if(projectType){
          url += 'pt='+projectType+'&';
       }
       if(property){
          url += 'pr='+property+'&';
       }
       if(project){
          url += 'p='+project+'&';
       }
       url += 'sc='+trade;

       window.location.href = path+'?'+url;
         
  } 

function selectVendor(trade, cLass, cLass2, cLass3){
       let path = window.location.href.split('?')[0]
        path = path.split('#')[0]
       var projectType = $('.'+cLass).val();
       var property = $('.'+cLass2).val();
       var project = $('.'+cLass3).val();
        
       let url = ''; 
       if(projectType){
          url += 'pt='+projectType+'&';
       }
        if(property){
          url += 'pr='+property+'&';
       }
       if(project){
          url += 'p='+project+'&';
       }
       url += 'v='+trade;

       window.location.href = path+'?'+url;
      
       
  }

   function selectUnbilledStatus(value, at, ps, pt, pr, p){
       let path = window.location.href.split('?')[0]
       path = path.split('#')[0]
       var at = $('.'+at).val();
       var ps = $('.'+ps).val();
       var pt = $('.'+pt).val();
       var pr = $('.'+pr).val();
       var p = $('.'+p).val();
        
       let url = ''; 
       if(at){
          url += 'at='+at+'&';
       }
       if(ps){
          url += 'ps='+ps+'&';
       }
       if(pt){
          url += 'pt='+pt+'&';
       }
       if(pr){
          url += 'pr='+pr+'&';
       }
       if(p){
          url += 'p='+p+'&';
       }
     
       url += value;

       window.location.href = path+'?'+url;
      
       
  }   

  function selectAwardedContracts(value, pt, pr){
       let path = window.location.href.split('?')[0]
       path = path.split('#')[0]
      var pt = $('.'+pt).val();
       var pr = $('.'+pr).val();
       var p = $('.'+p).val();
        
       let url = ''; 
      
       if(pt){
          url += 'pt='+pt+'&';
       }
       if(pr){
          url += 'pr='+pr+'&';
       }
       if(p){
          url += 'p='+p+'&';
       }
     
       url += value;

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


function projectPage(id){
        var fullUrl = '/projects/'+id;
        window.location.href = fullUrl;
}
 

   $(".btn-close").click(function(){  
            $("#myModal").modal('hide');
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

    let projectId = '{{ @$project->id }}';

    let _token   =   "{{ csrf_token() }}";
     
    let allParams = location.search

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

    let url = '/reports/'+projectId+'/send-mail'+allParams

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
      url = url+'#unpaid-bills';
      window.location.href = url;

 }     

  function sortOrderByStatus(orderBy,order){
       
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
      url = url+'#project-by-status';
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
@endsection
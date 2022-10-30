 <div class="tab-pane" id="construction-cost" role="tabpanel" aria-expanded="true">
   <div class="row mb-2">
        <div class="col-6">
            <h4 class="mt-0 text-left">{{ @$project->name }} - Total Construction Cost</h4>
        </div>
      
        <div class="card-body">   
         <div class="nav-tabs-navigation">
              <div class="nav-tabs-wrapper">
                  <ul id="tabs" class="nav nav-tabs" role="tablist">
                      <li class="nav-item">
                          <a class="nav-link text-dark active"  data-toggle="tab" href="#const-details" role="tab"
                             aria-expanded="true">Details</a>
                      </li>
                      <li class="nav-item">
                          <a class="nav-link text-dark"  data-toggle="tab" href="#summary" role="tab"
                             aria-expanded="false">Summary</a>
                      </li>
                  </ul>
              </div>
         </div>

           <div id="my-tab-content2" class="tab-content">
                  @include('projects.includes.construction-cost-details')
                  @include('projects.includes.construction-cost-summary')
            </div>

        </div>
    </div>

<div id="proposals-list" class="row py-3">

 <div id="myModal2" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
    <div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Send Mail</h3>
    </div>
    <div class="modal-body">
     
          <div class="form-group">
            <label for="recipient-name" class="col-form-label">Recipient:</label>
            <input type="email" class="form-control" id="recipient2">
          </div>
             <div class="form-group">
            <label for="recipient-name" class="col-form-label">CC: <small>email with comma seperated
            </small></label>
            <input type="text" class="form-control" id="cc2">
          </div>

          <div class="form-group">
            <label for="recipient-name" class="col-form-label">BCC: <small>email with comma seperated
            </small></label>
            <input type="text" class="form-control" id="bcc2">
          </div>


          <div class="form-group">
            <label for="recipient-name" class="col-form-label">Subject:</label>
            <input type="text" class="form-control" id="subject2">
          </div>
          <div class="form-group">
            <label for="message-text" class="col-form-label">Message:</label>
            <textarea class="form-control" id="message2"></textarea>
          </div>
    
    </div>
    <div class="modal-footer">
        <input type="hidden" name="type" id="type">
        <button class="btn btn-close" data-dismiss="modal" aria-hidden="true">Close</button>
        <button class="btn btn-primary" onclick="sendMail2()">Send</button>
    </div>
    </div>
    </div>

</div>


 </div>
</div>
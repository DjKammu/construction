 <div class="tab-pane" id="budget" role="tabpanel" aria-expanded="true">
   <div class="row mb-2">
        <div class="col-6">
            <h4 class="mt-0 text-left">{{ @$project->name }} - Budget </h4>
        </div>
      
         <div class="col-6 text-right">
            <button type="button" class="btn btn-danger mt-0"  onclick="sendEmailPopup()">
            	Send Email
            </button>

            <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='ffe/{{ @$project->id }}/download'">Download
            </button>
            <input type="hidden" id="file" value="{{ route('projects.download',@$project->id ) .'?v=1' }}">
        </div>
    </div>

<div id="proposals-list" class="row py-3">
 @include('projects.ffe.budget-content')

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


 </div>
</div>
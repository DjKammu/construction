 <div class="tab-pane active" id="const-details" role="tabpanel" aria-expanded="true">
   <div class="row mb-2">
        <div class="col-6">
            <h4 class="mt-0 text-left">Total Construction Details </h4>
        </div>
         <div class="col-6 text-right">
            <button type="button" class="btn btn-danger mt-0"  onclick="sendEmailPopup2(1)">
            	Send Email
            </button>
            <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='{{ @$project->id }}/total/download?t=1'">Download
            </button>
            <input type="hidden" id="file" value="{{ route('projects.total.download',@$project->id ) .'?v=1' }}">
        </div>
    </div>

<div id="proposals-list" class="row py-3">
 @include('projects.includes.construction-cost-content')
 </div>
</div>
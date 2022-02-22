 <div class="tab-pane" id="budget" role="tabpanel" aria-expanded="true">
   <div class="row mb-2">
        <div class="col-6">
            <h4 class="mt-0 text-left">{{ @$project->name }} - Budget </h4>
        </div>
      
         <div class="col-6 text-right">
            <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='{{ @$project->id }}/download'">Download
            </button>
        </div>
    </div>

<div id="proposals-list" class="row py-3">

 @include('projects.includes.budget-content')

</div>
</div>
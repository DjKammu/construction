@extends('layouts.admin-app')

@section('title', 'Edit Project')

@section('content')
  <link href="{{ asset('css/gantt.css') }}"  rel="stylesheet" />
  <script src="{{ asset('js/gantt.js') }}"></script>

<div class="row">
@include('includes.back', 
['url' => route("projects.show", ['project' => request()->project]),
'to' => 'to Project'])

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
                               <div class="row">
                                <div class="col-6">
                                    <h4 class="mt-0 text-left">{{ @$project->name }} - Gantt </h4>
                                </div>
                              
                                 <!-- <div class="col-6 text-right">
                                    <button type="button" class="btn btn-danger mt-0"  onclick="sendEmailPopup()">
                                      Send Email
                                    </button>

                                    <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='budget/pdf/download'">Download
                                    </button> 

                                     <button type="button" class="btn btn-danger mt-0"  onclick="return window.location.href='budget/excel/download'">Download to Excel
                                    </button>
                                </div> -->
                            </div>
                             
                             <div class="row">
                                <div class="col-8">
                                    <form method="post" action="{{ route('projects.gantt.other.assign', [ 'project' => request()->project]) }}"> 
                                      @csrf
                                     <select style="height: 26px;" onchange="return window.location.href = '?project_type='+this.value" name="project_type"> 
                                      <option value="">Select Project Type</option>
                                      @foreach($projectTypes as $type)
                                         <option value="{{ $type->slug }}" {{ (@request()->project_type == $type->slug) ? 'selected' : ''}}> {{ $type->name }}</option>
                                      @endforeach
                                      </select>
                                    <select style="height: 26px;"  name="project_id"> 
                                      <option value=""> Select Project</option>
                                      @foreach($projects as $p)
                                       <option value="{{ $p->id }}" >{{ $p->name}}
                                       </option>
                                      @endforeach
                                    </select>
                                    <button >Add Gantt from other projects</button>
                                  </form>
                                </div>
                                <div class="col-4">
                                  
                                </div>
                            </div>

                       <style>
                   

                      .demo-main-container {
                          display: -ms-flexbox;
                          display: -webkit-flex;
                          display: flex;
                          -webkit-flex-direction: column;
                          -ms-flex-direction: column;
                          flex-direction: column;

                          height: 90%;
                          min-height: 641px;

                          background-color: #fff;

                          z-index: 4;
                      }

                      .demo-main-content {
                          position: relative;

                          height: 100%;
                          margin: 10px 10px 0;

                          background-color: #fff;
                      }

                      #gantt_here {
                          position: absolute;

                          width: 100%;
                          height: 100%;

                          background-color: #fff;

                          overflow: auto;
                      }

                      .status_line {
                          background-color: #0ca30a;
                      }

                      .gantt_grid_wbs {
                          position: absolute;
                      }

                      .gantt_grid_scale {
                          position: relative;
                          z-index: 1;
                      }

                      .dnd_highlighter {
                          position: absolute;
                          height: 4px;
                          width: 500px;
                          background-color: #3498db;
                      }

                      .dnd_highlighter::before {
                          background: transparent;
                          width: 12px;
                          height: 12px;
                          box-sizing: border-box;
                          border: 3px solid #3498db;
                          border-radius: 6px;
                          content: "";
                          line-height: 1px;
                          display: block;
                          position: absolute;
                          margin-left: -11px;
                          margin-top: -4px;
                      }

                      .gantt_tooltip {
                          font-size: 12px;
                          line-height: 120%;
                          border-bottom: 1px solid #b3b3b3;
                          border-right: 1px solid #b3b3b3;
                            z-index: 1111;
                      }

                      .gantt_drag_marker {
                          opacity: 0.6;
                      }

                      .gantt_drag_marker.gantt_grid_resize_area {
                          z-index: 1;
                      }

                      .gantt_parent_row {
                          font-weight: bold;
                      }

                      .gantt_task_line div.gantt_side_content {
                          bottom: 0;
                      }

                      .gantt-top-panel {
                          position: relative;
                          color: #fff;
                          padding: 11px 16px;
                          background: #3d3d3d;
                      }

                      .gantt-top-panel__btn {
                          display: inline-block;
                          color: #fff;
                          padding: 7px 24px;
                          text-decoration: none;
                          border-radius: 20px;
                          background: #2095f3;

                          position: absolute;
                          right: 8px;
                          top: 50%;
                          margin-top: -16px;
                      }

                      .gantt-top-panel__btn:hover {
                          background: #03a9f4;
                      }

                      .gantt-top-panel__btn:focus {
                          outline: none;
                      }

                      .gantt-top-panel__btn:active {
                          transform: translateY(1px);
                          -webkit-transform: translateY(1px);
                      }

                      .status-control {
                          font-size: 0;
                      }

                      .status-control .status {
                          position: relative;
                          width: 37px;
                          height: 22px;
                          margin-right: 9px;
                          transition: all 0.4s ease;
                          border-radius: 11px;
                          background-color: #e6e6e6;
                      }

                      .status-control.checked .status {
                          background-color: #2095f3;
                      }

                      .dhx_checkbox_grip {
                          position: absolute;
                          top: 2px;
                          left: 2px;
                          width: 18px;
                          height: 18px;
                          transition: all 0.2s ease;
                          border-radius: 9px;
                          background-color: #fff;
                          box-shadow: 0 3px 9px 0 rgba(0, 0, 0, 0.2);
                      }

                      .status-control.checked .dhx_checkbox_grip {
                          left: 17px;
                      }

                      .dhx_checkbox_title {
                          color: #5f5f5f;
                          font: 500 12px/32px "Roboto", Arial, sans-serif;
                      }

                      .button-with-icon.active {
                          background-color: #e5e5e5;
                      }

                      .disabled {
                          opacity: 0.5;
                      }

                      .dhx_checkbox_title,
                      .status-control .status,
                      .dhx_demo_checkbox {
                          display: inline-block;
                          vertical-align: middle;
                      }

                      .dhx_demo_checkbox {
                          font-size: 14px;
                      }

                      .dhx_demo_checkbox_group {
                          position: relative;
                          white-space: nowrap;
                          margin-right: 24px;
                      }

                      .dhx_demo_checkbox {
                          margin-left: 10px;
                          cursor: pointer;
                          user-select: none;
                      }

                      .dhx_demo_checkbox:first-child {
                          margin-left: 0;
                      }

                      .demo-actions-container{
                          position: relative;

                          font-size: 0;
                          line-height: 0;

                          padding: 15px 10px 10px;

                          user-select: none;
                          top: -10px;
                      }

                      .demo-actions{
                          position: relative;

                          display: -ms-flexbox;
                          display: -webkit-flex;
                          display: flex;
                          -webkit-flex-wrap: wrap;
                          -ms-flex-wrap: wrap;
                          flex-wrap: wrap;
                          -webkit-align-items: center;
                          -ms-flex-align: center;
                          align-items: center;

                          padding: 18px 24px 18px 24px;

                          border: 1px solid #dfdfdf;
                      }

                      .demo-actions__row{
                          padding: 5px 0;
                      }

                      .demo-actions__col{
                          display: inline-block;
                          vertical-align: middle;

                          margin-right: 16px;
                      }

                      .demo-actions__last-elem{
                          position: absolute;
                          right: 24px;
                          top: 50%;
                          margin-top: -16px;
                      }

                      .demo-btn{
                          position: relative;

                          display: inline-block;
                          vertical-align: middle;

                          color: #0288d1;
                          font: 500 12px/20px "Roboto", Arial, sans-serif;
                          text-align: center;
                          text-transform: uppercase;
                          text-decoration: none;
                          white-space: nowrap;

                          margin-left: 10px;
                          padding: 7px 16px 5px;

                          border-radius: 32px;
                          border: 1px solid #0288d1;
                          background-color: transparent;

                          transition: background-color 0.2s ease-in, box-shadow 0.2s ease-in;

                          outline: none;
                          user-select: none;

                          cursor: pointer;
                      }

                      .demo-btn:first-child{
                          margin: 0;
                      }

                      .demo-btn:hover{
                          background-color: #d9edf8;
                      }

                      .demo-btn:active{
                          background-color: #b8def2;
                      }

                      .demo-btn.outline-btn{
                          color: #fff;

                          border: none;
                          background-color: #0288d1;
                      }

                      .demo-btn.outline-btn:hover{
                          background-color: #35a0da;
                      }

                      .demo-btn.outline-btn:active{
                          background-color: #0288d1;
                      }

                      .scale-combo{
                          display: block;

                          color: #5f5f5f;
                          font: 500 12px/32px "Roboto", Arial, sans-serif;

                          width: 120px;
                          padding: 0 20px 0 16px;

                          border-radius: 0;
                          border: 1px solid #ededed;

                          background: url('/images/arrow.png') 96% / 15% no-repeat #fbfbfb;

                          -webkit-appearance: none;
                          appearance: none;

                          outline: none;
                      }

                      .scale-combo:active,
                      .scale-combo:focus{
                          border-color: #2095F3;

                          outline: none;
                      }

                      .icon-btn{
                          display: block;

                          color: #5f5f5f;
                          font: 500 14px/32px "Roboto", Arial, sans-serif;
                          text-align: center;
                          text-decoration: none;

                          padding: 0;

                          cursor: pointer;
                      }

                      .icon-btn.disabled{
                          opacity: 0.6;

                          pointer-events: none;
                      }

                      .icon-btn img{
                          position: relative;
                          top: -1px;

                          display: inline-block;
                          vertical-align: middle;

                          width: 24px;
                          margin-right: 5px;
                      }
                      .gantt_grid_scale, .gantt_task_scale{
                        font-size: 11px;
                      }
                       .gantt_cell{
                        font-size: 11px;
                      }

                      @media screen and (max-width: 1280px){
                          .demo-actions{
                              padding: 4px 130px 10px 24px;
                          }
                      }

                      @media screen and (max-width: 1150px){
                          .demo-actions{
                              padding: 4px 120px 10px 15px;
                          }

                          .demo-actions__col{
                              margin-right: 10px;
                          }

                          .demo-actions__last-elem{
                              right: 15px;
                          }
                      }
                  </style>

                  <div class="demo-main-container">
                      <div class="demo-main-content">
                          <div id="gantt_here"></div>
                      </div>

                      <div class="demo-actions-container">
                          <div class="demo-actions">
                              <div class="demo-actions__row">
                                   <div class="demo-actions__col">
                                      <div class="dhx_demo_checkbox">
                                          <div id="collapse" class="status-control">
                                              <div class="status">
                                                  <div class="dhx_checkbox_grip"></div>
                                              </div>

                                              <div class="dhx_checkbox_title">Collapse Rows</div>
                                          </div>
                                      </div>
                                  </div>

                                  <div class="demo-actions__col">
                                      <div class="dhx_demo_checkbox">
                                          <div id="auto-scheduling" class="status-control">
                                              <div class="status">
                                                  <div class="dhx_checkbox_grip"></div>
                                              </div>

                                              <div class="dhx_checkbox_title">Auto Scheduling</div>
                                          </div>
                                      </div>
                                  </div>

                                  <div class="demo-actions__col">
                                      <span data-action="undo" class="icon-btn js-action-btn">
                                          <img src="/images/ic_undo_24.png" alt="Undo">
                                          Undo
                                      </span>
                                  </div>

                                  <div class="demo-actions__col">
                                      <span data-action="redo" class="icon-btn js-action-btn">
                                          <img src="/images/ic_redo_24.png" alt="Redo">
                                          Redo
                                      </span>
                                  </div>

                                  <div class="demo-actions__col">
                                      <div class="dhx_demo_checkbox">
                                          <div id="critical-path" class="status-control">
                                              <div class="status">
                                                  <div class="dhx_checkbox_grip"></div>
                                              </div>

                                              <div class="dhx_checkbox_title">Critical Path</div>
                                          </div>
                                      </div>
                                  </div>

                                  <div class="demo-actions__col">
                                      <div class="dhx_demo_checkbox">
                                          <div id="zoom-to-fit" class="status-control">
                                              <div class="status">
                                                  <div class="dhx_checkbox_grip"></div>
                                              </div>

                                              <div class="dhx_checkbox_title">Zoom to Fit</div>
                                          </div>
                                      </div>
                                  </div>

                                  <div class="demo-actions__col">
                                      <div class="scale-container">
                                          <select class="scale-combo" id="scale_combo" placeholder="">
                                              <option value="" disabled="" selected="">Zoom to:</option>
                                              <option value="years">Years</option>
                                              <option value="quarters">Quarters</option>
                                              <option value="months">Months</option>
                                              <option value="weeks">Weeks</option>
                                              <option value="days">Days</option>
                                              <option value="hours">Hours</option>
                                          </select>
                                      </div>
                                  </div>
                              </div>

                              <div class="demo-actions__row">
                                  <span class="demo-btn js-action-btn" data-action="toPDF">Export to PDF</span>

                                  <span class="demo-btn js-action-btn" data-action="toPNG">Export to PNG</span>

                                  <span class="demo-btn js-action-btn" data-action="toExcel">Export to Excel</span>

                                  <span class="demo-btn js-action-btn" data-action="toMSProject">Export to MS Project</span>
                                  <span class="demo-btn outline-btn js-action-btn" data-action="fullscreen">Fullscreen</span>
                              </div>
                          </div>
                      </div>
                  </div>

                               
                                  <style>
                                        
                                .weekend {
                                  background: #f4f7f4 !important;
                                }

                                .task_groups {
                                  background-color: orangered !important;
                                }

                                .task_groups .gantt_task_progress {
                                  background-color: red;
                                  opacity: 0.6;
                                }
                                .gantt_task_row.gantt_selected .weekend {
                                  background-color: #C0E8FF !important;
                                }
                                div#gantt_here{
                                  z-index: 1111 !important;
                                }
                              </style>

                              <script type="text/javascript">

                                 gantt.plugins({
                                      marker: true,
                                      fullscreen: true,
                                      critical_path: true,
                                      auto_scheduling: true,
                                      tooltip: true,
                                      undo: true,
                                      export_api: true,
                                    });

                                window.ganttModules = {};
                                

                                  gantt.templates.scale_cell_class = function (date) {
                                    if (!gantt.isWorkTime(date)) {
                                      return "weekend";
                                    }
                                  };
                                  gantt.templates.timeline_cell_class = function (item, date) {
                                    if (!gantt.isWorkTime({date: date, task: item})) {
                                      return "weekend";
                                    }
                                  };
                                  gantt.templates.rightside_text = function(start, end, task) {
                                    if (task.type === gantt.config.types.milestone)
                                      return task.text;
                                    return ""
                                  };
                
                             
                                gantt.templates.rightside_text = function (start, end, task) {
                                  if (task.type == gantt.config.types.milestone) {
                                    return task.text;
                                  }
                                  return "";
                                };
                                
                                function addClass(node, className) {
                                    node.classList.add(className);
                                }

                                function removeClass(node, className) {
                                    node.classList.remove(className);
                                }

                                function getButton(name) {
                                    return document.querySelector(".demo-actions [data-action='" + name + "']");
                                }

                                function highlightButton(name) {
                                    addClass(getButton(name), "menu-item-active");
                                }
                                function unhighlightButton(name) {
                                    removeClass(getButton(name), "menu-item-active");
                                }

                                function disableButton(name) {
                                    addClass(getButton(name), "disabled");
                                }

                                function enableButton(name) {
                                    removeClass(getButton(name), "disabled");
                                }
                                                              function refreshUndoBtns() {
                                  if (!gantt.getUndoStack || !gantt.getUndoStack().length) {
                                      disableButton("undo");
                                  } else {
                                      enableButton("undo");
                                  }

                                  if (!gantt.getRedoStack || !gantt.getRedoStack().length) {
                                      disableButton("redo");
                                  } else {
                                      enableButton("redo");
                                  }
                              }

                              setInterval(refreshUndoBtns, 1000);


                                const toolbarMenu = {
                                  undo: function () {
                                      gantt.undo();
                                      refreshUndoBtns();
                                  },
                                  redo: function () {
                                      gantt.redo();
                                      refreshUndoBtns();
                                  },
                                  zoomToFit: function () {
                                      ganttModules.zoomToFit.toggle();
                                      toggleCheckbox(zoomToFitCheckbox, config.zoom_to_fit);
                                      const scalesDropdown = document.querySelector("#scale_combo");
                                      const zoomLevelName = zoomConfig.levels[gantt.ext.zoom.getCurrentLevel()].name;
                                        scalesDropdown.value = zoomLevelName;
                                  },
                                  fullscreen: function () {
                                      gantt.ext.fullscreen.toggle();
                                  },
                                  collapseAll: function () {
                                      gantt.eachTask(function (task) {
                                          task.$open = false;
                                      });
                                      gantt.render();

                                  },
                                  expandAll: function () {
                                      gantt.eachTask(function (task) {
                                          task.$open = true;
                                      });
                                      gantt.render();
                                  },
                                  toggleAutoScheduling: function () {
                                      gantt.config.auto_scheduling = !gantt.config.auto_scheduling;

                                      if (gantt.config.auto_scheduling) {
                                          gantt.autoSchedule();
                                      }
                                  },
                                  toggleCriticalPath: function () {
                                      gantt.config.highlight_critical_path = !gantt.config.highlight_critical_path;

                                      gantt.render();
                                  },
                                  toPDF: function () {
                                      gantt.exportToPDF({
                                          header: `<style>.timeline_cell{width: ${gantt.$task_data.scrollWidth}px !important;}</style>`,
                                          raw: true
                                      });
                                  },
                                  toPNG: function () {
                                      gantt.exportToPNG({
                                          header: `<style>.timeline_cell{width: ${gantt.$task_data.scrollWidth}px !important;}</style>`,
                                          raw: true
                                      });
                                  },
                                  toExcel: function () {
                                      gantt.exportToExcel();
                                  },
                                  toMSProject: function () {
                                      gantt.exportToMSProject();
                                  }
                              };


                               const zoomConfig = {
    levels: [
        {
            name: "hours",
            scales: [
                { unit: "day", step: 1, format: "%j %M" },
                { unit: "hour", step: 1, format: "%H:%i" },
            ],
            round_dnd_dates: true,
            min_column_width: 30,
            scale_height: 60
        },
        {
            name: "days",
            // scales: [
            //     { unit: "week", step: 1, format: "%W" },
            //     { unit: "day", step: 1, format: "%j" },
            // ],
            scales:[
            {unit: "day", step: 1, format: "%d %M"}
            ],
            round_dnd_dates: true,
            min_column_width: 60,
            scale_height: 60
        },
        {
            name: "weeks",
            scales: [
                { unit: "month", step: 1, format: "%M" },
                {
                    unit: "week", step: 1, format: function (date) {
                        const dateToStr = gantt.date.date_to_str("%d %M");
                        const endDate = gantt.date.add(gantt.date.add(date, 1, "week"), -1, "day");
                        return dateToStr(date) + " - " + dateToStr(endDate);
                    }
                }
            ],
            round_dnd_dates: false,
            min_column_width: 60,
            scale_height: 60
        },
        {
            name: "months",
            scales: [
                { unit: "year", step: 1, format: "%Y" },
                { unit: "month", step: 1, format: "%M" }
            ],
            round_dnd_dates: false,
            min_column_width: 50,
            scale_height: 60
        },
        {
            name: "quarters",
            scales: [
                { unit: "year", step: 1, format: "%Y" },
                {
                    unit: "quarter", step: 1, format: function quarterLabel(date) {
                        const month = date.getMonth();
                        let q_num;

                        if (month >= 9) {
                            q_num = 4;
                        } else if (month >= 6) {
                            q_num = 3;
                        } else if (month >= 3) {
                            q_num = 2;
                        } else {
                            q_num = 1;
                        }

                        return "Q" + q_num;
                    }
                },
                { unit: "month", step: 1, format: "%M" }
            ],
            round_dnd_dates: false,
            min_column_width: 50,
            scale_height: 60
        },
        {
            name: "years",
            scales: [
                { unit: "year", step: 1, format: "%Y" },
                {
                    unit: "year", step: 5, format: function (date) {
                        const dateToStr = gantt.date.date_to_str("%Y");
                        const endDate = gantt.date.add(gantt.date.add(date, 5, "year"), -1, "day");
                        return dateToStr(date) + " - " + dateToStr(endDate);
                    }
                }
            ],
            round_dnd_dates: false,
            min_column_width: 50,
            scale_height: 60
        },
        {
            name: "years",
            scales: [
                {
                    unit: "year", step: 10, format: function (date) {
                        const dateToStr = gantt.date.date_to_str("%Y");
                        const endDate = gantt.date.add(gantt.date.add(date, 10, "year"), -1, "day");
                        return dateToStr(date) + " - " + dateToStr(endDate);
                    }
                },
                {
                    unit: "year", step: 100, format: function (date) {
                        const dateToStr = gantt.date.date_to_str("%Y");
                        const endDate = gantt.date.add(gantt.date.add(date, 100, "year"), -1, "day");
                        return dateToStr(date) + " - " + dateToStr(endDate);
                    }
                }
            ],
            round_dnd_dates: false,
            min_column_width: 50,
            scale_height: 60
        }
    ]
}

                                  
                                  gantt.config.fit_tasks = true;


                                  gantt.ext.zoom.init(zoomConfig);

                                  gantt.ext.zoom.setLevel("days");

                                  gantt.$zoomToFit = false;



                                let zoomToFitMode = false;
                                ganttModules.zoomToFit = (function (gantt) {
                                    let cachedSettings = {};

                                    function saveConfig() {
                                        const config = gantt.config;
                                        cachedSettings = {};
                                        cachedSettings.scales = config.scales;
                                        cachedSettings.template = gantt.templates.date_scale;
                                        cachedSettings.start_date = config.start_date;
                                        cachedSettings.end_date = config.end_date;
                                        cachedSettings.zoom_level = gantt.ext.zoom.getCurrentLevel();
                                    }

                                    function restoreConfig() {
                                        applyConfig(cachedSettings);
                                    }

                                    function applyConfig(config, dates) {
                                        if (config.scales[0].date) {
                                            gantt.templates.date_scale = null;
                                        }
                                        else {
                                            gantt.templates.date_scale = config.scales[0].template;
                                        }

                                        gantt.config.scales = config.scales;

                                        if (dates && dates.start_date && dates.start_date) {
                                            const unit = config.scales[config.scales.length - 1].unit
                                            gantt.config.start_date = gantt.date.add(dates.start_date, -1, unit);
                                            gantt.config.end_date = gantt.date.add(gantt.date[unit + "_start"](dates.end_date), 2, unit);
                                        } else {
                                            gantt.config.start_date = gantt.config.end_date = null;
                                        }

                                        if (config.zoom_level !== undefined) {
                                            gantt.ext.zoom.setLevel(config.zoom_level);
                                        }
                                    }

                                     function zoomToFit() {
                                      const project = gantt.getSubtaskDates(),
                                          areaWidth = gantt.$task.offsetWidth;
                                      const scaleConfigs = zoomConfig.levels

                                      let zoomLevel = 0;
                                      for (let i = 0; i < scaleConfigs.length; i++) {
                                          zoomLevel = i;
                                          const level = scaleConfigs[i].scales;
                                          const lowestScale = level[level.length - 1]
                                          const columnCount = getUnitsBetween(project.start_date, project.end_date, lowestScale.unit, lowestScale.step || 1);
                                          if ((columnCount + 2) * gantt.config.min_column_width <= areaWidth) {
                                              break;
                                          }
                                      }

                                      if (zoomLevel == scaleConfigs.length) {
                                          zoomLevel--;
                                      }

                                      gantt.ext.zoom.setLevel(scaleConfigs[zoomLevel].name);
                                      applyConfig(scaleConfigs[zoomLevel], project);

                                      gantt.render();
                                  }


                                    // get number of columns in timeline
                                    function getUnitsBetween(from, to, unit, step) {
                                        let start = new Date(from),
                                            end = new Date(to);
                                        let units = 0;
                                        while (start.valueOf() < end.valueOf()) {
                                            units++;
                                            start = gantt.date.add(start, step, unit);
                                        }
                                        return units;
                                    }

                                    return {
                                        enable: function () {
                                            zoomToFitMode = true;
                                            saveConfig();
                                            zoomToFit();
                                            gantt.render();
                                        },
                                        toggle: function () {
                                            if (zoomToFitMode) {
                                                this.disable();
                                            } else {
                                                this.enable();
                                            }
                                        },
                                        disable: function () {
                                            zoomToFitMode = false;
                                            restoreConfig();
                                            gantt.render();
                                        }
                                    };
                                })(gantt);

gantt.templates.grid_row_class = function (start, end, task) {
    return gantt.hasChild(task.id) ? "gantt_parent_row" : "";
};

const font_width_ratio = 7;

gantt.templates.leftside_text = function leftSideTextTemplate(start, end, task) {
    if (getTaskFitValue(task) === "left") {
        return task.text;
    }
    return "";
};

gantt.templates.rightside_text = function rightSideTextTemplate(start, end, task) {
    if (getTaskFitValue(task) === "right") {
        return task.text;
    }
    return "";
};

gantt.templates.task_text = function taskTextTemplate(start, end, task) {
    if (getTaskFitValue(task) === "center") {
        return task.text;
    }
    return "";
};

function getTaskFitValue(task) {
    let taskStartPos = gantt.posFromDate(task.start_date),
        taskEndPos = gantt.posFromDate(task.end_date);

    const width = taskEndPos - taskStartPos;
    const textWidth = (task.text || "").length * font_width_ratio;

    if (width < textWidth) {
        let ganttLastDate = gantt.getState().max_date;
        let ganttEndPos = gantt.posFromDate(ganttLastDate);
        if (ganttEndPos - taskEndPos < textWidth) {
            return "left"
        }
        else {
            return "right"
        }
    }
    else {
        return "center";
    }
}



const date_to_str = gantt.date.date_to_str(gantt.config.task_date);
const today = new Date();
gantt.addMarker({
    start_date: today,
    css: "today",
    text: "Today",
    title: "Today: " + date_to_str(today)
});

const start = new Date();
gantt.addMarker({
    start_date: start,
    css: "status_line",
    text: "Start project",
    title: "Start project: " + date_to_str(start)
});

gantt.attachEvent("onTaskCreated", function (item) {
    if (item.duration == 1) {
        // item.duration = 72;
    }
    return true;
});

gantt.ext.fullscreen.getFullscreenElement = function () {
    return document.querySelector(".demo-main-container");
};

const currentYear = new Date().getFullYear();

const durationFormatter = gantt.ext.formatters.durationFormatter({
    enter: "day",
    store: "day",
    format: "day",
    hoursPerDay: 24,
    hoursPerWeek: 40,
    daysPerMonth: 30,
    short: true
});

const linksFormatter = gantt.ext.formatters.linkFormatter({ durationFormatter: durationFormatter });

const hourFormatter = gantt.ext.formatters.durationFormatter({
    enter: "hour",
    store: "hour",
    format: "hour",
    short: true
});


const textEditor = { type: "text", map_to: "text" };
const dateEditor = { type: "date", map_to: "start_date"};
// const durationEditor = { type: "duration", map_to: "duration", formatter: durationFormatter, min: 0, max: 10000 };
// const hourDurationEditor = { type: "duration", map_to: "duration", formatter: hourFormatter, min: 0, max: 10000 };
const predecessorEditor = { type: "predecessor", map_to: "auto", formatter: linksFormatter };

 const durationEditor = {type: "duration", map_to: "duration", min:0, max: 100};


gantt.config.columns = [
    { name: "", width: 15, resize: false, template: function (task) { return "<span class='gantt_grid_wbs'>" + gantt.getWBSCode(task) + "</span>" } },
    { name: "text", tree: true, width: 180, resize: true, editor: textEditor },
    { name: "start_date", label: "Start", align: "center", resize: true, width: 80, editor: dateEditor },
    {name: "duration_formatted",label: "Duration", align: "center", width: 90, resize: true, editor: durationEditor, 
      template: function (task) {
            return durationFormatter.format(task.duration);
        }, editor: durationEditor},
    {
        name: "predecessors", label: "Predecessors", width: 80, align: "left", editor: predecessorEditor, resize: true, template: function (task) {
            const links = task.$target;
            const labels = [];
            for (let i = 0; i < links.length; i++) {
                const link = gantt.getLink(links[i]);
                labels.push(linksFormatter.format(link));
            }
            return labels.join(", ")
        }
    },
    { name: "add", "width": 44 }
];


gantt.config.lightbox.sections = [
  {name: "description", height: 70, map_to: "text", type: "textarea", focus: true},
  {name: "type", type: "typeselect", map_to: "type"},
  {name: "time", type: "duration", map_to: "auto"}
];  

//Make resize marker for two columns
gantt.attachEvent("onColumnResizeStart", function (ind, column) {
    if (!column.tree || ind == 0) return;

    setTimeout(function () {
        const marker = document.querySelector(".gantt_grid_resize_area");
        if (!marker) return;
        const cols = gantt.getGridColumns();
        const delta = cols[ind - 1].width || 0;
        if (!delta) return;

        marker.style.boxSizing = "content-box";
        marker.style.marginLeft = -delta + "px";
        marker.style.paddingRight = delta + "px";
    }, 1);
});

gantt.attachEvent("onGanttReady", function(){
  
gantt.templates.tooltip_text = function (start, end, task) {
    const links = task.$target;
    const labels = [];
    for (let i = 0; i < links.length; i++) {
        const link = gantt.getLink(links[i]);
        labels.push(linksFormatter.format(link));
    }
   
    const predecessors = labels.join(", ");

    let html = "<b>Task:</b> " + task.text + "<br/><b>Start:</b> " +
        gantt.templates.tooltip_date_format(start) +
        "<br/><b>End:</b> " + gantt.templates.tooltip_date_format(end) +
        "<br><b>Duration:</b> " + durationFormatter.format(task.duration);
    if (predecessors) {
        html += "<br><b>Predecessors:</b>" + predecessors;
    }
     console.log(html);
    return html;
};
});
gantt.config.date_format = "%Y-%m-%d %H:%i:%s";
// gantt.config.order_branch = true;/*!*/
// gantt.config.order_branch_free = true;/*!*/

// gantt.config.work_time = true;
// gantt.config.min_column_width = 60;

//gantt.config.auto_types = true;
// gantt.config.duration_unit = "hour";

gantt.config.row_height = 23;
gantt.config.order_branch = "marker";
gantt.config.order_branch_free = true;
gantt.config.grid_resize = true;
gantt.ext.zoom.setLevel("days");
gantt.config.auto_scheduling_strict = true;

  gantt.init("gantt_here");


  const navBar = document.querySelector(".demo-actions");

  gantt.event(navBar, "click", function (e) {
        let target = e.target || e.srcElement;

        while (!target.hasAttribute("data-action") && target !== document.body) {
            target = target.parentNode;
        }

        if (target && target.hasAttribute("data-action")) {
            const action = target.getAttribute("data-action");
            if (toolbarMenu[action]) {
                toolbarMenu[action]();
            }
        }
    });

    let config = {
        collapse: false,
        auto_scheduling: false,
        critical_path: false,
        zoom_to_fit: false
    };

    function toggleCheckbox(checkbox, state, disabled) {
        state
            ? checkbox.classList.add("checked")
            : checkbox.classList.remove("checked");
        disabled
            ? checkbox.classList.add("disabled")
            : checkbox.classList.remove("disabled");
    }
    

    let collapseCheckbox = document.querySelector("#collapse"),
        autoSchedulingCheckbox = document.querySelector("#auto-scheduling"),
        criticalPathCheckbox = document.querySelector("#critical-path"),
        zoomToFitCheckbox = document.querySelector("#zoom-to-fit"),
        scaleComboElement = document.getElementById("scale_combo");

if (collapseCheckbox) {
    collapseCheckbox.addEventListener("click", function () {
        let action = "expandAll";

        config.collapse = !config.collapse;
        toggleCheckbox(collapseCheckbox, config.collapse);

        if (config.collapse) {
            action = "collapseAll";
        }

        if (toolbarMenu[action]) {
            toolbarMenu[action]();
        }
    });
  }

if (criticalPathCheckbox) {
    criticalPathCheckbox.addEventListener("click", function () {
      let action = "toggleCriticalPath";

      config.critical_path = !config.critical_path;
      toggleCheckbox(criticalPathCheckbox, config.critical_path);

      if (toolbarMenu[action]) {
          toolbarMenu[action]();
      }
  });
  }
   
   if (autoSchedulingCheckbox) {
        autoSchedulingCheckbox.addEventListener("click", function () {
          let action = "toggleAutoScheduling";

          config.auto_scheduling = !config.auto_scheduling;
          toggleCheckbox(autoSchedulingCheckbox, config.auto_scheduling);

          if (toolbarMenu[action]) {
              toolbarMenu[action]();
          }
      });
    }
     if (scaleComboElement) {
        scaleComboElement.addEventListener("change", function () {
          let scaleValue = this.value;

          gantt.ext.zoom.setLevel(scaleValue);
          config.zoom_to_fit = false;
          zoomToFitMode = false;
          toggleCheckbox(zoomToFitCheckbox, false);
      });
    } 

    if (zoomToFitCheckbox) {
        zoomToFitCheckbox.addEventListener("click", function () {
        let action = "zoomToFit";

        config.zoom_to_fit = !config.zoom_to_fit;
        toggleCheckbox(zoomToFitCheckbox, config.zoom_to_fit);

        if (toolbarMenu[action]) {
            toolbarMenu[action]();
        }
    });
    }


  gantt.load("gantt/get/data");

  const dp = gantt.createDataProcessor({
    url: "gantt/do",
    mode: "REST"
  });
  </script>

            </div>
        </div>
    </div>
</div>


@endsection

@section('pagescript')
<script type="text/javascript">

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

    let url = '/projects/'+projectId+'/budget/send-mail'

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


</script>
<style type="text/css">
  .form-switch {
    padding-left: 2.5em;
}

.form-switch .form-check-input:checked {
    background-position: right center;
    background-image: url(data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e);
}
.form-check-input:checked[type=checkbox] {
    background-image: url(data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23fff' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='M6 10l3 3l6-6'/%3e%3c/svg%3e);
}
.form-switch .form-check-input {
    width: 2em;
    margin-left: -2.5em;
    background-image: url(data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='rgba%280, 0, 0, 0.25%29'/%3e%3c/svg%3e);
    background-position: left center;
    border-radius: 2em;
    transition: background-position .15s ease-in-out;
}
.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}
.form-check-input[type=checkbox] {
    border-radius: 0.25em;
}
.form-check .form-check-input {
    float: left;
    margin-left: -1.5em;
}
.form-check-input {
    width: 1em;
    height: 1em;
    margin-top: 0.25em;
    vertical-align: top;
    background-color: #fff;
    background-repeat: no-repeat;
    background-position: center;
    background-size: contain;
    border: 1px solid rgba(0,0,0,.25);
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    -webkit-print-color-adjust: exact;
    color-adjust: exact;
}


#gantt_here{
  max-height: 500px;
}

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

  #category-types-table{
    font-size: 12px;
  }
  .checkbox{
    margin-right: 4px;
  }

    .budget-image{
    float: left;
    margin-top: 5px;
  }
  .budget-image .avatar.proposal_file{
    height: 15px;
    width: 15px;
  }
  
</style>

@endsection
<template>
    <div id="proposals-list" class="row py-3">
        <div class="col-6">
            <div v-if="success" class="alert alert-success alert-dismissible fade show">
              <strong>Success!</strong> {{ successMsg }}
            </div>
    
            <div  v-else-if="error" class="alert alert-warning alert-dismissible fade show">
            <strong>Error!</strong> {{ errorMsg }}
            </div>
        </div>    
        <div class="table-responsive" v-if="projectLines">
          <table id="project-types-table" class="table table-hover text-center payments-table">
            <thead>
            <tr class="text-danger">
                <th style="width: 80px;">Item No.
                 <span class="sorting-outer">
                  <a href="javascript:void(0)" @click="sortOrderBy('account_number', 'ASC')"><i class="fa fa-sort-asc"></i></a>
                  <a href="javascript:void(0)" @click="sortOrderBy('account_number', 'DESC')"><i class="fa fa-sort-desc"></i> </a>
                </span>
            </th>
                <th>Description</th>
                <th>Scheduled Value</th>
                <th style="width: 80px;">Retainage %</th>
                <th>Delete </th>
            </tr>
            </thead>
            <tbody>

              <tr v-for="(project_line, index) in project_lines" :key='project_line.id' >
                <td>
                <input class="form-control" type="text"  v-model="project_lines[index].account_number"  /></td>
                <td>
                <input class="form-control" type="text"  v-model="project_lines[index].description"  /></td>
                <td><input class="form-control" type="number" :v-model="project_lines[index].value" :value="formatNumber(project_lines[index].value)" @input="project_lines[index].value = $event.target.value"  /></td>
                <td><input class="form-control" type="number" max="100" v-model="project_lines[index].retainage" /></td>
                <td><i @click="deleteLine(project_line.id)" style="cursor: pointer;" class="fa fa-trash"></i></td>
              </tr>

              <tr v-for="(addLineItem, index) in addLineItemHTML">
                <td><input class="form-control" type="text" v-model="form.account_number[index]"  /></td>
                <td><input class="form-control" type="text" v-model="form.description[index]"  /></td>
                <td><input class="form-control" type="number" v-model="form.value[index]"  /></td>
                <td><input class="form-control" type="number" max="100" v-model="form.retainage[index]" /></td>
                <td><i @click="deleteHTMLLine(index)" style="cursor: pointer;" class="fa fa-trash"></i></td>
              </tr>

            </tbody>
            </table> 

            <div class="col-12">
                 <button type="button" class="btn btn-danger mt-0" @click="addLineItem" >
                    Add Line Item
                </button>
            </div>
            <div class="col-12">
                <button type="button" class="btn btn-danger mt-0" @click="saveLineItem">Save Line Items
                </button>
                <button type="button" class="btn mt-0" @click="cancel" >Cancel
                </button>  

                <button type="button" v-if="project_lines" class="btn mt-0" @click="summaryProject" >
                    Summary
                </button> 
    
            </div>

          </br>
          </br>
          </br>

          <table v-if="isExcessOrShortfall" id="project-types-table" class="table table-hover payments-table">
            <thead>
            <tr >
                <th>Project Line Total</th>
                <th>${{ numbersFormat(total) }}</th>
            </tr>
            <tr >
                <th>Contract Original Scheduled Value</th>
                <th>${{  numbersFormat(original_amount)  }}</th>
            </tr>
            <tr style="color: red;" v-if="currentExcess">
                <th >Current Excess</th>
                <th>${{  numbersFormat(currentExcess)  }}</th>
            </tr>
            <tr style="color: red;" v-else>
                <th> Short Fall</th>
                <th>${{  numbersFormat(shortFall)   }}</th>
            </tr>
            </thead>
            <tbody>

            </tbody>
            </table> 
        </div>
        <div class="table-responsive" v-else>

             <div class="col-6">

               <table class="table table-hover payments-table">
                  <thead>
                  <tr >
                      <th> {{ project.name }}
                      <br>
                      {{project.address}} {{project.city}} 
                      <br>
                      {{ project.state }} , {{project.country}} {{ project.zip_code }}  
                       </th>
                  </tr>
                  </thead>
                  <tbody>

                  </tbody>
              </table> 

               <button type="button"  v-if="(!currentExcess) && (!shortFall) && (applications_count >= 0) && !isProjectClosed" class="btn btn-danger" @click="changeOrders" >Change Orders
              </button>

              <button type="button"  v-if="applications_count < 2" class="btn btn-danger" @click="editLineItem" > Edit Line Items
              </button>  
              <button type="button"  v-if="applications_count < 1" class="btn btn-danger" @click="importLineItems" >Import Items
              </button>
            </div>

            <div class="col-12" v-if="currentExcess > 0" >

                  <h6> Project Line Item Excess   </h6>
                
                The sum of the scheduled values for the project line items exceeds the original amount by  ${{  numbersFormat(currentExcess)  }} . Please update the project so that the total scheduled values of the line items equals the original contract amount.

             </div>

            <div class="col-12" v-else-if="shortFall > 0">

                <h6>Project Line Item Shortfall</h6> 
                The sum of the scheduled values for the project line items less than the original amount by ${{  numbersFormat(shortFall) }} . Please update the project so that the total scheduled values of the line items equals the original contract amount.

                You will not able to proceed with creating Application #1 until this is resolved.
               
                
            </div> 

    
            <div class="col-6 pull-right" v-else>
                
                <span v-if="!isProjectClosed">
                  
                <span v-if="applications_count == 0"> 

                  <button type="button" class="btn btn-danger mt-0" @click="createApplication" >
                            Create Application #1 
                </button>

                </span>
                <span v-else-if="applications_count >0" >

                  <button type="button" class="btn btn-danger mt-0" @click="editApplication" >
                            Edit Application #{{ (applications_count) }}
                  </button>
                  <button v-if="closeProject==false" type="button" class="btn btn-danger mt-0" @click="createApplication" >
                            Create Application #{{ parseInt(applications_count) + 1 }}
                  </button> 
                  <button v-else type="button" class="btn btn-danger mt-0" @click="projectClose" >
                            Close Project
                  </button>
                  
                  <button  v-if="applications_count == 1" type="button" class="btn btn-warning mt-0" @click="resetApplication" >
                           Reset Application
                  </button>

                  <button  v-if="applications_count > 1" type="button" class="btn btn-warning mt-0" @click="undoApplication" >
                           Undo
                  </button>

                </span>

                </span>

                <span v-else>
                 <h5 class="col-6 pull-left"> Project Closed </h5>

                  <button v-if="closeProject==true" type="button" class="col-5 pull-right btn btn-warning mt-0"
                   @click="undoFinal" >
                           Undo Final
                  </button> 
              
                </span>

                <table class="table table-bordered text-center">
                    <thead>
                      <tr class="">
                        <th :colspan="(changeOrdersTotal > 0) ? 6 : 5">
                        Application Document History</th>
                      </tr>
                      <tr>
                        <th>App #</th>
                        <th>Date</th>
                        <th>Application</th>
                        <th>Continuation Sheet</th>
                        <th v-if="changeOrdersTotal > 0">Change Orders</th>
                        <th>Archt. Reports</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-if="isProjectClosed">
                        <!-- <th scope="row">{{ applications.length + 1}}</th> -->
                        <th scope="row">Final</th>
                        <td>{{ isProjectClosed.application_date }}</td>
                        <td><img style="width:32px;cursor: pointer;" @click="redirectTo(isProjectClosed.id,'application-cp')" src="/img/pdf.png"></td>
                        <td><img style="width:32px;cursor: pointer;" @click="redirectTo(isProjectClosed.id,'continuation-sheet-cp')" src="/img/pdf.png"></td>
                        <td v-if="changeOrdersTotal > 0"><img style="width:32px;cursor: pointer;" @click="redirectTo(isProjectClosed.id,'change-order-cp')" src="/img/pdf.png"></td>
                        <td >
                        </td>
                      </tr>
                      <tr  v-if="applications.length > 0" v-for="(application, index) in applications"  >
                        <th scope="row">{{ applications.length - index}}</th>
                        <td>{{ application.application_date }}</td>
                        <td><img style="width:32px;cursor: pointer;" @click="redirectTo(application.id,'application')" src="/img/pdf.png"></td>
                        <td><img style="width:32px;cursor: pointer;" @click="redirectTo(application.id,'continuation-sheet')" src="/img/pdf.png"></td>
                        <td v-if="(changeOrdersTotal > 0)"><img v-if="application.has_change_order" style="width:32px;cursor: pointer;" @click="redirectTo(application.id,'change-order')" src="/img/pdf.png"></td>
                      <td>
                          <span v-if="application.archt_reports.length > 0" v-for="(archt_report, index) in application.archt_reports">
                           <a :href="'/'+archt_report.file" class="rep-img" target="_blank"> 
                             <span class="cross">
                                  <i class="fa fa-trash text-danger" @click="deleteFile(archt_report.id,$event)"></i> 
                              </span>
                          <img
                          :src="'/img/' + archt_report.extension + '.png' " /> </a>

                          </span>
                        <div class="clip-upload">
                           <label :for="`file-${application.id}`">
                            <i class="fa fa-paperclip fa-lg" aria-hidden="true"></i>
                           </label>
                           <input type="file" :id="`file-${application.id}`" multiple name="files" @change="uploadReport(application.id,$event)">
                          </div>
                      </td>
                      </tr>
                    </tbody>
                  </table>
                      
            </div>
           <div v-if="isExcessOrShortfall" class="col-6">

              <h5 class=""> Project Summary </h5>

              <table   id="project-types-table" class="table table-hover payments-table">
                    <thead>
                     <tr >
                        <th>Total Original Contract Amount</th>
                        <th>${{  numbersFormat(original_amount)  }}</th>
                    </tr>
                    
                    <tr >
                        <th>Project Line Item Total</th>
                        <th>${{ numbersFormat(total) }}</th>
                    </tr>
                   
                    <tr style="color: red;" >
                        <th >Project Line Item Excess/(Shortfall)</th>
                        <th v-if="currentExcess" >${{  numbersFormat(currentExcess)  }}</th>
                        <th     v-else>${{  numbersFormat(shortFall)   }}</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table> 
           </div>
           <div v-else class="col-6">

             <h5 class=""> Project Summary </h5>

            <table  id="project-types-table" class="table table-hover payments-table">
                <thead v-if="!isProjectClosed">
                 <tr >
                    <th>Original Contract Sum</th>
                    <th>${{  numbersFormat(original_amount)  }}</th>
                   
                </tr>
                <tr >
                    <th>Net Change from Change Order(s)</th>
                     <th>${{  numbersFormat(changeOrdersTotal)  }}</th>
                </tr>
                <tr >
                    <th>Subcontract Sum to Date</th>
                    <th>${{ numbersFormat( parseFloat(original_amount) + parseFloat(changeOrdersTotal)) }}</th>
                </tr>
                <tr >
                    <th>Total Completed & Stored to Date</th>
                    <th>${{ numbersFormat(totalStored) }}</th>
                </tr>  

                <tr >
                    <th>Retainage to Date</th>
                    <th>${{ numbersFormat(retainageToDate) }}</th>
                </tr>
                <tr >
                    <th>Total Earned Less Retainage</th>
                    <th>${{ numbersFormat(totalEarned) }}</th>
                </tr>
                <tr >
                    <th>Less Previous Applications & Certificates for Payment</th>
                    <th>${{ numbersFormat(lastApplicationsPayments) }}</th>
                </tr>
                <tr >
                    <th>Current Payment Due</th>
                    <th>${{ numbersFormat(currentDuePayment) }}</th>
                </tr>
                <tr >
                    <th>Balance to Finish Including Total Retainage</th>
                    <th>${{ numbersFormat(
                          (balance)) }}</th>
                </tr>
                
                 
                </thead>
                <thead v-else>
                    <tr >
                      <th>Original Contract Sum</th>
                      <th>${{  numbersFormat(original_amount)  }}</th>
                     
                    </tr>
                    <tr >
                      <th>Net Change from Change Order(s)</th>
                       <th>${{  numbersFormat(changeOrdersTotal)  }}</th>
                    </tr>
                    <tr >
                      <th>Subcontract Sum to Date</th>
                      <th>${{ numbersFormat( parseFloat(original_amount) + parseFloat(changeOrdersTotal)) }}</th>
                    </tr>
                    <tr >
                      <th>Total Completed & Stored to Date</th>
                      <th>${{ numbersFormat(totalStored) }}</th>
                    </tr>  

                     <tr >
                      <th>Balance to Finish Including Total Retainage</th>
                      <th>$0.00</th>
                    </tr>

                </thead>
            </table>
          </div>  
        </div> 
    

     <!-- Modal -->
      <div class="modal fade" id="closeProjectModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
          <div class="modal-content">
          
          <div class="modal-header">
             <div v-if="success2" class="alert alert-success alert-dismissible fade show">
              <strong>Success!</strong> {{ successMsg2 }}
            </div>
    
            <div  v-else-if="error2" class="alert alert-warning alert-dismissible fade show">
            <strong>Error!</strong> {{ errorMsg2 }}
            </div>

              <h5 class="modal-title" id="exampleModalLabel">Set Final Application Dates</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <div class="modal-body">  
                 <div  class="col-12">
                    <div class="form-group">
                        <label class="text-dark" for="password">Application Date 
                        </label>
                        <input  v-model="application_date" id="application_date" type="text" class="form-control date" placeholder="Application Date">

                    </div>
                </div>

                 <div class="col-12">
                    <div class="form-group">
                        <label class="text-dark" for="password">Period To 
                        </label>
                        <input  v-model="period_to" id="period_to" type="text" class="form-control date" placeholder="Period To">

                    </div>
                </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary mt-0" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-danger mt-0" @click="saveCloseProject" >Save changes</button>
          </div>
          </div>
      </div>
      </div>
 </div>

</template>

<script>
    import datetimepicker from '../../../public/js/plugins/bootstrap-datetimepicker.js' //import

   

    export default {
        props: ['project','retainage','projectid','original_amount'],

        mounted() {
            this.loadLines();
            
            let _vm = this

            $('#application_date').datetimepicker({
                format: 'Y-M-D'
            }).on('dp.change', function (e) {
              _vm.application_date = $(this).val()
            });

            $('#period_to').datetimepicker({
                format: 'Y-M-D'
            }).on('dp.change', function (e) {
              _vm.period_to = $(this).val()
            });

        },
        data() {
            return {
                lastApplicationsPayments :0,
                isExcessOrShortfall :false,
                applications_count : 0,
                application_date: null,
                period_to: null,
                closeProject : false,
                projectLines : true,
                currentDuePayment :0,
                changeOrdersTotal :0,
                archtReportsTotal :0,
                successMsg : null,
                successMsg2 : null,
                retainageToDate :0,
                firstTime : true,
                success : false,
                success2 : false,
                errorMsg : null,
                errorMsg2 : null,
                currentExcess :0,
                totalStored :0,
                totalEarned :0,
                error : false,
                error2 : false,
                isProjectClosed : false,
                lastLine : 0,
                shortFall :0,
                balance :0,
                total :0,
                lines : [],
                addLineItemHTML : [],
                project_lines : [],
                applications : [],
                form :{
                        account_number: [],
                        description: [],
                        retainage: [],
                        value: []
                }
            };
        },
        methods: {
            async loadLines(){
            
              let _vm = this;

              await axios.get('/projects/'+this.projectid+'/get-project-lines/')
                .then(function (response) {
                       let res = response.data
                       _vm.project_lines = res.data
                       _vm.excessOrShortfall();
                       _vm.loadSummary();
                       _vm.loadApplications();
                })
                .catch(function (error) {
                    console.log(error);
                });

                if(_vm.project_lines.length > 0 && _vm.firstTime == true){
                      _vm.projectLines = false 
                      _vm.firstTime = false
                }
            },
            async loadSummary(){
            
              let _vm = this;

              await axios.get('/projects/'+this.projectid+'/get-applications-summary/')
                .then(function (response) {
                       let res = response.data
                       _vm.lastApplicationsPayments = res.data.lastApplicationsPayments
                       _vm.applications_count = res.data.applicationsCount
                       _vm.changeOrdersTotal = res.data.changeOrdersTotal
                       _vm.currentDuePayment = res.data.currentDuePayment
                       _vm.retainageToDate = res.data.retainageToDate
                       _vm.isProjectClosed = res.data.isProjectClosed
                       _vm.closeProject = res.data.closeProject
                       _vm.totalStored = res.data.totalStored
                       _vm.totalEarned = res.data.totalEarned
                       _vm.balance = parseFloat(_vm.original_amount) +  parseFloat(res.data.changeOrdersTotal) - parseFloat(res.data.totalEarned)

                })
                .catch(function (error) {
                    console.log(error);
                });

            },
            async loadApplications(){
            
              let _vm = this;

              await axios.get('/projects/'+this.projectid+'/get-all-applications/')
                .then(function (response) {
                       let res = response.data
                       _vm.applications = res.data

                })
                .catch(function (error) {
                    console.log(error);
                });

            },
            addLineItem(){
                     this.form.retainage[this.addLineItemHTML.length] = this.retainage;
                     this.addLineItemHTML.push(this.addLineItemHTML.length+1)
            },
            deleteLine($id){
                 
                if (!confirm("Are you sure to delete!")) {
                  return;
                } 
 
                let _vm = this;

                axios.delete('/projects/'+$id+'/project-lines/') 
                   .then(function (response) {
                       let res = response.data
                       if(res.error){
                            _vm.error = true
                            _vm.errorMsg = res.message
                       }else{
                          _vm.success = true
                          _vm.successMsg = res.message
                       }
                      _vm.resetLines();
                })
                .catch(function (error) {
                    console.log(error);
                });

            },
            formatNumber(value){
               return value
            },
           numbersFormat(value){
            return  new Intl.NumberFormat('en-US', {
                        maximumFractionDigits: 2,
                }).format(value);
            },
            deleteHTMLLine(index){
              this.addLineItemHTML.splice(index, 1);
            },          
           async  saveLineItem() {

              let _vm = this;

              var lines = [];

              this.project_lines.map(function(value, key) {
                  lines.push({
                        account_number: value.account_number, 
                        description: value.description, 
                        retainage: value.retainage, 
                        value: value.value, 
                        id:  value.id
                    });
               }); 
                 
               if((!lines) && (!this.form.account_number[0] || !this.form.description[0] || !this.form.value[0] || !this.form.retainage[0])){
                  this.error = true
                  this.errorMsg = 'Enter lines to save data!'

                   setTimeout(()=>{
                       this.clearMsg()
                    },2000);

                  return;
               }  
               
              await axios.post('/projects/'+this.projectid+'/add-project-lines/',{
                    data : this.form,
                    lines: lines
                })
                .then(function (response) {
                       let res = response.data
                        if(res.error){
                            _vm.error = true
                            _vm.errorMsg = res.message
                       }else{
                          _vm.success = true
                          _vm.successMsg = res.message
                       }
                       _vm.resetLines();
                     
                })
                .catch(function (error) {
                    console.log(error);
                });
                
                if(!_vm.error){
                    _vm.projectLines = false;
                 }
            },
            sortOrderBy(orderBy,order){
                 let _vm = this;

                  axios.get('/projects/'+this.projectid+'/get-project-lines/?order='+order+'&orderBy='+orderBy)
                    .then(function (response) {
                           let res = response.data
                           _vm.project_lines = res.data
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            resetLines(){
            
             this.addLineItemHTML = [];
             this.form = {
                        account_number: [],
                        description: [],
                        value: [],
                        retainage: []
                };
         
            this.loadLines();

            setTimeout(()=>{
               this.clearMsg()
            },3000);
                 
            },
            clearMsg(){
                this.error = this.success = false
                this.error2 = this.success2 = false
                this.errorMsg = this.successMsg = null
                this.errorMsg2 = this.successMsg2 = null
            },

            cancel(){
                this.projectLines = false; 

            },
            editLineItem(){
                this.projectLines = true;
                this.loadLines();
            },
            summaryProject(){
                this.projectLines = false;
                this.loadLines();
            }, 
            projectClose(){
             
               if(!confirm("This project has been 100% billed, less retainage. Closing the project will mark the current application as Complete. The final application for the retainage will also be created. You will not be able to create any additional applications or make any other updates to the project.\n\nAre you sure you want to close the project?")){
                    return
               }

              $('#closeProjectModal').modal('show')

            },
            async undoApplication(){
               let applications_count = this.applications_count
               if (!confirm("Are you sure to undo application "+ applications_count +"!")) {
                  return;
                }

                  let _vm = this;

                await axios.get('/projects/undo/'+_vm.projectid+'/project-lines')
                .then(function (response) {
                           let res = response.data
                          if(res.error){
                                _vm.error = true
                                _vm.errorMsg = res.message
                           }else{
                              _vm.success = true
                              _vm.successMsg = res.message
                               _vm.loadLines();
                           }

                           setTimeout(()=>{
                             _vm.clearMsg()
                          },2000);
                    })
                    .catch(function (error) {
                        console.log(error);
                    });

            },
            async undoFinal(){
             
               if (!confirm("Are you sure to undo fianl!")) {
                  return;
                }

                  let _vm = this;

                await axios.get('/projects/'+_vm.projectid+'/close-project-undo')
                .then(function (response) {
                           let res = response.data
                          if(res.error){
                                _vm.error = true
                                _vm.errorMsg = res.message
                           }else{
                              _vm.success = true
                              _vm.successMsg = res.message
                               _vm.loadLines();
                           }
                          setTimeout(()=>{
                             _vm.clearMsg()
                          },2000);
                    })
                    .catch(function (error) {
                        console.log(error);
                    });

            },
            async importLineItems(){
              let password = prompt("By doing import the items, line items will be deleted.\n\nAre you sure you want to  import the items?\n\nEnter user password for import items");
               
               if(!password){
                    return
               }

               let _vm = this;

              await axios.post('/projects/import/'+_vm.projectid+'/project-lines',{
                     'password' : password
                  }).then(function (response) {
                           let res = response.data
                          if(res.error){
                                _vm.error = true
                                _vm.errorMsg = res.message
                           }else{
                              _vm.success = true
                              _vm.successMsg = res.message
                               _vm.loadLines();
                           }

                           setTimeout(()=>{
                             _vm.clearMsg()
                          },2000);
                    })
                    .catch(function (error) {
                        console.log(error);
                    });

             },
              async resetApplication(){
              let password = prompt("By doing reset application line will be deleted.\n\nAre you sure you want to  reset the project?\n\nEnter user password for reset application");
               
               if(!password){
                    return
               }

              let _vm = this;

              await axios.post('/projects/delete/'+_vm.projectid+'/project-lines',{
                     'password' : password
                  }).then(function (response) {
                           let res = response.data
                          if(res.error){
                                _vm.error = true
                                _vm.errorMsg = res.message
                           }else{
                              _vm.success = true
                              _vm.successMsg = res.message
                               _vm.loadLines();
                           }

                           setTimeout(()=>{
                             _vm.clearMsg()
                          },2000);
                    })
                    .catch(function (error) {
                        console.log(error);
                    });


            },
            async  saveCloseProject() {

              let _vm = this;


               if(!this.application_date || !this.period_to ){
                  this.error2 = true
                  this.errorMsg2 = 'Application date or Period to is missing!'

                   setTimeout(()=>{
                       this.clearMsg()
                    },2000);

                  return;
               }

              await axios.post('/projects/'+this.projectid+'/close-project/',{
                    application_date: this.application_date,
                    retainage_value: this.balance,
                    period_to: this.period_to
                })
                .then(function (response) {

                       let res = response.data

                      if(res.error){
                            _vm.error2 = true
                            _vm.errorMsg2 = res.message
                       }else{
                          _vm.success = true
                          _vm.successMsg = res.message
                           $('#closeProjectModal').modal('hide')
                           _vm.loadLines();
                       }

                       setTimeout(()=>{
                         this.clearMsg()
                      },2000);
                      
                })
                .catch(function (error) {
                    console.log(error);
                });
            },

            createApplication(){
              let applications_count = this.applications_count
              if(applications_count == 1){
  
                 if(!confirm("If you proceed with creating application #2, you will no longer be able to make any changes to the original contract amount or the original project line items. Any subsequent changes to the contract value and contract line items will need to be made via change orders.\n\nDo you want to proceed with creating Application #2?")){
                      return
                 }
              }
              window.location.href =  'applications';
            },
            editApplication(){
              window.location.href =  'applications/edit';
            },
            changeOrders(){
              window.location.href =  'change-orders';
            },
            redirectTo($id,$to){
                 let a= document.createElement('a');
                 a.target= '_blank';
                 a.href= '/projects/'+this.projectid+'/'+$to+'/'+$id;
                 a.click();
            },
            excessOrShortfall(){
                let totalValues= 0 ;

                //let retainageTotal = 0

                this.currentExcess = 0
                this.shortFall = 0


                $.each(this.project_lines, function(key, value) {
                     totalValues = parseFloat(totalValues) + parseFloat(value.value);
                     //retainageTotal = parseFloat(retainageTotal) + (parseFloat(value.value * value.retainage/100) )
                });

                // this.retainageToDate = retainageTotal;

                this.total = totalValues;
                  
               // this.totalEarned =    parseFloat(this.total) -  parseFloat(this.retainageToDate);
                  
                if(this.total > this.original_amount || this.total < this.original_amount){
                    this.isExcessOrShortfall = true;
                    if(this.total > this.original_amount){
                       this.currentExcess = parseFloat(this.total) - parseFloat(this.original_amount);
                    } else{
                       this.shortFall = parseFloat(this.original_amount) - parseFloat(this.total);
                    }
                }else{
                  this.isExcessOrShortfall = false;  
                }
            },
           
            async uploadReport(id,e) {

              let _vm = this;
              var form = new FormData();

              if(Array.from(e.target.files) && Array.from(e.target.files).length > 0){
                  let files = Array.from(e.target.files);
                  files.map((file) => {
                       form.append('files[]', file);
                  });  
              }

              var config = {
                  header: { "Contect-type": "multipart/form-data" },
              };

              await axios.post('/projects/'+this.projectid+'/archt-reports/'+id,form)
                .then(function (response) {

                       let res = response.data
                    
                      if(res.error){
                            _vm.error = true
                            _vm.errorMsg = res.message
                       }else{
                          _vm.success = true
                          _vm.successMsg = res.message
                          _vm.loadApplications();
                       }
                      
                       setTimeout(()=>{
                         _vm.clearMsg();
                      },2000);
                })
                .catch(function (error) {
                    console.log(error);
                });


            },

            deleteFile(id, e) {
              e.preventDefault();

              if (!confirm("Are you sure to delete Report!")) {
                  return;
                } 
 
                let _vm = this;

                axios.delete('/projects/'+this.projectid+'/archt-reports/'+id) 
                   .then(function (response) {
                      let res = response.data
                    
                      if(res.error){
                            _vm.error = true
                            _vm.errorMsg = res.message
                       }else{
                          _vm.success = true
                          _vm.successMsg = res.message
                          _vm.loadApplications();
                       }
                      
                       setTimeout(()=>{
                         _vm.clearMsg();
                      },2000);
                })
                .catch(function (error) {
                    console.log(error);
                });

            }

        }

    }
</script>

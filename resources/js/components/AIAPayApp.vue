<template>
    <div id="proposals-list" class="row py-3">
            <div v-if="success" class="alert alert-success alert-dismissible fade show">
              <strong>Success!</strong> {{ successMsg }}
            </div>
    
            <div  v-else-if="error" class="alert alert-warning alert-dismissible fade show">
            <strong>Error!</strong> {{ errorMsg }}
            </div>
        <div class="table-responsive" v-if="projectLines">

          <table id="project-types-table" class="table table-hover text-center payments-table">
            <thead>
            <tr class="text-danger">
                <th style="width: 80px;">Item No.
                 <span class="sorting-outer">
                  <a href="javascript:void(0)" @click="sortOrderBy('account_number', 'ASC')"><i class="fa fa-sort-asc" o ></i></a>
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
                <th>${{ new Intl.NumberFormat().format(total) }}</th>
            </tr>
            <tr >
                <th>Contract Original Scheduled Value</th>
                <th>${{  new Intl.NumberFormat().format(original_amount)  }}</th>
            </tr>
            <tr style="color: red;" v-if="currentExcess">
                <th >Current Excess</th>
                <th>${{  new Intl.NumberFormat().format(currentExcess)  }}</th>
            </tr>
            <tr style="color: red;" v-else>
                <th> Short Fall</th>
                <th>${{  new Intl.NumberFormat().format(shortFall)   }}</th>
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

               <button type="button"  class="btn btn-danger" @click="changeOrders" >Change Orders
              </button>

              <button type="button"  v-if="applications_count < 2" class="btn btn-danger" @click="editLineItem" >Edit Line Items
              </button>

            </div>

            <div class="col-12" v-if="currentExcess > 0" >

                  <h6> Project Line Item Excess   </h6>
                
                The sum of the scheduled values for the project line items exceeds the original amount by  ${{  new Intl.NumberFormat().format(currentExcess)  }} . Please update the project so that the total scheduled values of the line items equals the original contract amount.

             </div>

            <div class="col-12" v-else-if="shortFall > 0">

                <h6>Project Line Item Shortfall</h6> 
                The sum of the scheduled values for the project line items less than the original amount by ${{  new Intl.NumberFormat().format(shortFall) }} . Please update the project so that the total scheduled values of the line items equals the original contract amount.

                You will not able to proceed with creating Application #1 until this is resolved.
               
                
            </div> 

    
            <div class="col-6 pull-right" v-else>

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
                  
                </span>

                <table class="table table-bordered text-center">
                    <thead>
                      <tr class="">
                        <th colspan="4">Application Document History</th>
                      </tr>
                      <tr>
                        <th>App #</th>
                        <th>Date</th>
                        <th>Application</th>
                        <th>Continuation Sheet</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr  v-if="applications.length > 0" v-for="(application, index) in applications"  >
                        <th scope="row">{{ applications.length - index}}</th>
                        <td>{{ application.application_date }}</td>
                        <td><img style="width:32px;cursor: pointer;" @click="redirectTo(application.id,'application')" src="/img/pdf.png"></td>
                        <td><img style="width:32px;cursor: pointer;" @click="redirectTo(application.id,'continuation-sheet')" src="/img/pdf.png"></td>
                      </tr>
                      <tr v-else colspan="2">
                        <th>No Applicayions</th>
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
                        <th>${{  new Intl.NumberFormat().format(original_amount)  }}</th>
                    </tr>
                    
                    <tr >
                        <th>Project Line Item Total</th>
                        <th>${{ new Intl.NumberFormat().format(total) }}</th>
                    </tr>
                   
                    <tr style="color: red;" >
                        <th >Project Line Item Excess/(Shortfall)</th>
                        <th v-if="currentExcess" >${{  new Intl.NumberFormat().format(currentExcess)  }}</th>
                        <th     v-else>${{  new Intl.NumberFormat().format(shortFall)   }}</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table> 
           </div>
           <div v-else class="col-6">

             <h5 class=""> Project Summary </h5>

            <table  id="project-types-table" class="table table-hover payments-table">
                <thead>
                 <tr >
                    <th>Original Contract Sum</th>
                    <th>${{  new Intl.NumberFormat().format(original_amount)  }}</th>
                   
                </tr>
                <tr >
                    <th>Net Change from Change Order(s)</th>
                     <th>${{  new Intl.NumberFormat().format(changeOrdersTotal)  }}</th>
                </tr>
                <tr >
                    <th>Subcontract Sum to Date</th>
                    <th>${{ new Intl.NumberFormat().format( parseFloat(original_amount) + parseFloat(changeOrdersTotal)) }}</th>
                </tr>
                <tr >
                    <th>Total Completed & Stored to Date</th>
                    <th>${{ new Intl.NumberFormat().format(totalStored) }}</th>
                </tr>  
                <tr >
                    <th>Retainage to Date</th>
                    <th>${{ new Intl.NumberFormat().format(retainageToDate) }}</th>
                </tr>
                <tr >
                    <th>Total Earned Less Retainage</th>
                    <th>${{ new Intl.NumberFormat().format(totalEarned) }}</th>
                </tr>
                <tr >
                    <th>Less Previous Applications & Certificates for Payment</th>
                    <th>${{ new Intl.NumberFormat().format(lastApplicationsPayments) }}</th>
                </tr>
                <tr >
                    <th>Current Payment Due</th>
                    <th>${{ new Intl.NumberFormat().format(currentDuePayment) }}</th>
                </tr>
                <tr >
                    <th>Balance to Finish Including Total Retainage</th>
                    <th>${{ new Intl.NumberFormat().format(
                          (balance)) }}</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
          </div>  
        </div> 
    </div>
</template>

<script>

    export default {
        props: ['project','retainage','projectid','original_amount'],

        mounted() {
            this.loadLines();
        },
        data() {
            return {
                lastApplicationsPayments :0,
                isExcessOrShortfall :false,
                applications_count : 0,
                closeProject : false,
                projectLines : true,
                currentDuePayment :0,
                changeOrdersTotal :0,
                successMsg : null,
                retainageToDate :0,
                firstTime : true,
                success : false,
                errorMsg : null,
                currentExcess :0,
                totalStored :0,
                totalEarned :0,
                error : false,
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
                this.errorMsg = this.successMsg = null
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
                alert('closeProject')
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
            }
        }

    }
</script>

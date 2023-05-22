<template>
    <div id="proposals-list" class="row">
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
                <th>Trade</th>
                <th>Delete </th>
            </tr>
            </thead>
            <tbody>

              <tr v-for="(project_line, index) in budget_lines" :key='project_line.id' >
                <td>
                <input class="form-control" type="text"  v-model="budget_lines[index].account_number"  /></td>
                <td>
                <input class="form-control" type="text"  v-model="budget_lines[index].trade"  /></td>
                <td><i @click="deleteLine(project_line.id)" style="cursor: pointer;" class="fa fa-trash"></i></td>
              </tr>

              <tr v-for="(addLineItem, index) in addLineItemHTML">
                <td><input class="form-control" type="text" v-model="form.account_number[index]"  /></td>
                <td><input class="form-control" type="text" v-model="form.trade[index]"  /></td>
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
                <button type="button" v-if="budget_lines" class="btn mt-0" @click="summaryProject" >
                    Summary
                </button> 
    
            </div>

        </div>
        <div class="table-responsive" v-else>
             <div class="col-12">
              <button type="button" class="btn btn-danger" @click="editLineItem" >Edit Line Items
              </button>
            </div>
            <div class="col-12">
              <h6 style="text-align: center;"> {{ project.name }} Construction Budget   </h6>

              <table id="project-types-table" class="table table-hover text-center payments-table">
                <thead>
                <tr class="text-danger">
                    <th style="width: 80px;">Item No.
                     <span class="sorting-outer">
                      <a href="javascript:void(0)" @click="sortOrderBy('account_number', 'ASC')"><i class="fa fa-sort-asc"></i></a>
                      <a href="javascript:void(0)" @click="sortOrderBy('account_number', 'DESC')"><i class="fa fa-sort-desc"></i> </a>
                    </span>
                </th>
                    <th>Trade</th>
                    <th>Price /Sq Ft </th>
                    <th>Budget </th>
                </tr>
                </thead>
                <tbody>
                  <tr v-for="(project_line, index) in budget_lines" :key='project_line.id' >
                    <td>
                   {{ budget_lines[index].account_number }}
                    <td>
                    {{ budget_lines[index].trade }}</td>
                    <td><input class="form-control" @input="fillPriceSqFt(index)" type="number"  v-model="budget_lines[index].price_sq_ft" /></td>
                    <td> <input class="form-control" @input="fillBudget(index)" type="number" v-model="budget_lines[index].budget" /></td>
                  </tr>
                  
                  <tr>
                    <td colspan="4"></td>
                  </tr>
                  <tr>
                    <td></td>
                    <th>Total</th>
                    <th>{{total_price_sq_ft}}</th>
                    <th>{{total_budget}}</th>
                  </tr>
                  <tr>
                    <td colspan="4"></td>
                  </tr>
                  <tr>
                    <td></td>
                    <th>Total Budget / Sq Ft </th>
                    <th>{{(total_price_sq_ft / total_construction_sq_ft).toFixed(2)}}</th>
                    <th>{{(total_budget / total_construction_sq_ft).toFixed(2)}}</th>
                  </tr>
                </tbody>
                </table> 
          </div>

          <div class="col-12 align-right">
                <button type="button" class="btn btn-danger mt-0" @click="saveLineItem">Save
                </button>
    
            </div>

        </div> 
  
 </div>

</template>

<script>
    export default {
        props: ['project','projectid','total_construction_sq_ft'],

        mounted() {
            this.loadLines();        
            let _vm = this
        },
        data() {
            return {
                projectLines : true,
                successMsg : null,
                successMsg2 : null,
                firstTime : true,
                success : false,
                success2 : false,
                errorMsg : null,
                errorMsg2 : null,
                error : false,
                error2 : false,
                lines : [],
                total_budget:0,
                total_price_sq_ft:0,
                addLineItemHTML : [],
                budget_lines : [],
                applications : [],
                form :{
                        account_number: [],
                        trade: [],
                        price_sq_ft: [],
                        budget: []
                }
            };
        },
        methods: {
            async loadLines(){
            
              let _vm = this;

              await axios.get('/projects/'+this.projectid+'/budget/lines/get')
                .then(function (response) {
                       let res = response.data
                       _vm.budget_lines = res.data.lines
                       _vm.total_budget = res.data.total_budget
                       _vm.total_price_sq_ft = res.data.total_price_sq_ft
                       _vm.loadSummary();
                })
                .catch(function (error) {
                    console.log(error);
                });

                if(_vm.budget_lines.length > 0 && _vm.firstTime == true){
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
                     // this.form.retainage[this.addLineItemHTML.length] = this.retainage;
                     this.addLineItemHTML.push(this.addLineItemHTML.length+1)
            },
            deleteLine($id){
                 
                if (!confirm("Are you sure to delete!")) {
                  return;
                } 
 
                let _vm = this;

                axios.delete('/projects/'+$id+'/budget/lines/') 
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

              this.budget_lines.map(function(value, key) {
                  lines.push({
                        account_number: value.account_number, 
                        price_sq_ft: value.price_sq_ft, 
                        trade: value.trade, 
                        budget: value.budget, 
                        id:  value.id
                    });
               }); 
                 
               if((!lines) && (!this.form.account_number[0] || !this.form.trade[0])){
                  this.error = true
                  this.errorMsg = 'Enter lines to save data!'

                   setTimeout(()=>{
                       this.clearMsg()
                    },2000);

                  return;
               }  
               
              await axios.post('/projects/'+this.projectid+'/budget/lines/',{
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
                           _vm.budget_lines = res.data
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            resetLines(){
            
             this.addLineItemHTML = [];
             this.form = {
                        account_number: [],
                        trade: []
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

            fillPriceSqFt(index){

              let total = parseFloat(this.total_construction_sq_ft)
            
              if(parseFloat(total) == 0 ){
                 this.budget_lines[index].budget = 0
              } 

              if(parseFloat(total) > 0 ){
                 this.budget_lines[index].budget = parseFloat(this.budget_lines[index].price_sq_ft)*total
              }

              this.budget_lines[index].budget = this.budget_lines[index].budget.toFixed(2)

                var total_budget = 0;
                var total_price_sq_ft = 0;

               this.budget_lines.map(function(value, key) {
                total_budget += parseFloat(value.budget)
                total_price_sq_ft += parseFloat(value.price_sq_ft)
               }); 
              this.total_budget = total_budget.toFixed(2)
              this.total_price_sq_ft = total_price_sq_ft.toFixed(2)

                
            },
            fillBudget(index){ 

               let total = parseFloat(this.total_construction_sq_ft)
            
              if(parseFloat(total) == 0 ){
                 this.budget_lines[index].price_sq_ft = 0
              } 

              if(parseFloat(total) > 0 ){
                 this.budget_lines[index].price_sq_ft = total/parseFloat(this.budget_lines[index].budget)
              }

              this.budget_lines[index].price_sq_ft = this.budget_lines[index].price_sq_ft.toFixed(2) 

                var total_budget = 0;
                var total_price_sq_ft = 0;

               this.budget_lines.map(function(value, key) {
                total_budget += parseFloat(value.budget)
                total_price_sq_ft += parseFloat(value.price_sq_ft)
               }); 
              
              this.total_budget = total_budget.toFixed(2)
              this.total_price_sq_ft = total_price_sq_ft.toFixed(2)

            }
        }

    }
</script>

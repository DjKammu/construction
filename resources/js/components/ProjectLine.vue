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
                <th style="width: 80px;">Item No.</th>
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
           <h3> Project Summary </h3>

            <table v-if="isExcessOrShortfall"  id="project-types-table" class="table table-hover payments-table">
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
                    <th v-else>${{  new Intl.NumberFormat().format(shortFall)   }}</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
                </table> 

                <table v-else id="project-types-table" class="table table-hover payments-table">
                <thead>
                 <tr >
                    <th>Original Contract Sum</th>
                    <th>${{  new Intl.NumberFormat().format(original_amount)  }}</th>
                </tr>
                <tr >
                    <th>Net Change from Change Order(s)</th>
                    <th>$0 </th>
                </tr>
                <tr >
                    <th>Subcontract Sum to Date</th>
                    <th>${{ new Intl.NumberFormat().format(total) }}</th>
                </tr>
                <tr >
                    <th>Total Completed & Stored to Date</th>
                    <th>${{ new Intl.NumberFormat().format(total) }}</th>
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
                    <th>$0</th>
                </tr>
                <tr >
                    <th>Current Payment Due</th>
                    <th>${{ new Intl.NumberFormat().format(totalEarned) }}</th>
                </tr>
                <tr >
                    <th>Balance to Finish Including Total Retainage</th>
                    <th>${{ new Intl.NumberFormat().format(retainageToDate) }}</th>
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

               <div>
                   
                  
               <button type="button" class="btn btn-danger mt-0" @click="editLineItem" >Edit Line Items
                </button>

               </div>
               
        </div> 
    </div>
</template>

<script>
    export default {
        props: ['retainage','projectid','original_amount'],

        mounted() {
            this.loadLines();
        },
        data() {
            return {
                projectLines : true,
                error : false,
                success : false,
                errorMsg : null,
                successMsg : null,
                currentExcess:0,
                shortFall:0,
                retainageToDate:0,
                totalEarned:0,
                isExcessOrShortfall:false,
                total:0,
                lastLine: 0,
                lines: [],
                addLineItemHTML: [],
                project_lines: [],
                project_line : {},
                form :{
                        account_number: [],
                        description: [],
                        retainage: [],
                        value: []
                },
                itemNumber: 1,
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
                      _vm.success = true
                      _vm.successMsg = res.message
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
                      _vm.success = true
                      _vm.successMsg = res.message
                      _vm.resetLines();
                })
                .catch(function (error) {
                    console.log(error);
                });

                _vm.projectLines = false;
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
            excessOrShortfall(){
                let totalValues= 0 ;

                let retainageTotal = 0

                $.each(this.project_lines, function(key, value) {
                     totalValues = parseFloat(totalValues) + parseFloat(value.value);
                    retainageTotal = parseFloat(retainageTotal) + (parseFloat(value.value * value.retainage/100) )
                });
                this.retainageToDate = retainageTotal;

                this.total = totalValues;
                  
                this.totalEarned =    parseFloat(this.total) -  parseFloat(this.retainageToDate);
                  
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

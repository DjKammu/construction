<template>
    <div id="proposals-list" class="row py-3">
            <div v-if="success" class="alert alert-success alert-dismissible fade show">
              <strong>Success!</strong> {{ successMsg }}
            </div>
    
            <div  v-else-if="error" class="alert alert-warning alert-dismissible fade show">
            <strong>Error!</strong> {{ errorMsg }}
            </div>

    <div class="table-responsive" v-if="projectLines">
        
        <div class="row">
         <div  class="col-lg-6 col-md-6">
            <div class="form-group">
                <label class="text-dark" for="password">Application Date 
                </label>
                <input  v-model="application_date" id="application_date" type="text" class="form-control date" placeholder="Application Date">

            </div>
        </div>

         <div class="col-lg-6 col-md-6">
            <div class="form-group">
                <label class="text-dark" for="password">Period To 
                </label>
                <input  v-model="period_to" id="period_to" type="text" class="form-control date" placeholder="Period To">

            </div>
        </div>
        
        </div>

          <table id="project-types-table" class="table table-hover text-center payments-table">
            <thead>
            <tr class="text-danger">
                <th style="width: 80px;">Item No.
                 <!-- <span class="sorting-outer">
                  <a href="javascript:void(0)" @click="sortOrderBy('account_number', 'ASC')"><i class="fa fa-sort-asc" o ></i></a>
                  <a href="javascript:void(0)" @click="sortOrderBy('account_number', 'DESC')"><i class="fa fa-sort-desc"></i> </a>
                </span> -->
            </th>
                <th>Description</th>
                <th>Scheduled Value</th>
                <th>Billed to Date</th>
                <th>Stored to Date</th>
                <th>Work Completed This Period </th>
                <th>Materials Stored/(Used) This Period </th>
                <th>Total Percent of Completion %</th>
            </tr>
            </thead>
            <tbody>

              <tr v-for="(project_line, index) in applications"  >
                <td>{{ index+1 }}</td>
                <td>
                {{ applications[index].description }} </td> 
                <td> ${{ new Intl.NumberFormat().format(applications[index].value) }} </td>         
                <td>
                ${{ (applications[index].billed_to_date) ? 
                new Intl.NumberFormat().format(applications[index].billed_to_date) : 0 }}                
                <td>
                ${{ (applications[index].billed_to_date) ? 
                new Intl.NumberFormat().format(applications[index].stored_to_date) : 0 }}   
                <td><input class="form-control" @input="fillValue(index)" type="number" v-model="applications[index].work_completed" /></td>
                <td><input class="form-control" @input="fillMaterial(index)" type="number" max="100" v-model="applications[index].materials_stored" /></td>
                <td> <input class="form-control" @input="fillPercent(index)" type="number" max="100" v-model="applications[index].total_percentage" /></td>
              </tr>

            </tbody>
            </table> 

            <div class="col-12">
                <button type="button" class="btn btn-danger mt-0" @click="saveApplication">Save Application
                </button>
                <button type="button" class="btn mt-0" @click="cancel" >Cancel
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
          
            <div class="col-12" v-if="currentExcess > 0" >

                  <h6> Project Line Item Excess   </h6>
                
                The sum of the scheduled values for the project line items exceeds the original amount by  ${{  new Intl.NumberFormat().format(currentExcess)  }} . Please update the project so that the total scheduled values of the line items equals the original contract amount.

             </div>

            <div class="col-12" v-else-if="shortFall > 0">

                <h6>Project Line Item Shortfall</h6> 
                The sum of the scheduled values for the project line items less than the original amount by ${{  new Intl.NumberFormat().format(shortFall) }} . Please update the project so that the total scheduled values of the line items equals the original contract amount.

                You will not able to proceed with creating Application #1 until this is resolved.
               
                
            </div> 

            <div class="col-12" v-else>

                <button type="button" class="btn btn-danger mt-0" @click="createApplication" >
                            Create Application #1
                </button>
                    
                
            </div>
        
            <h5 class="col-12"> Project Summary </h5>

            <table v-if="isExcessOrShortfall"  id="project-types-table" class="table table-hover payments-table col-12">
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

                <table v-else id="project-types-table" class="table table-hover payments-table col-12">
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
    import datetimepicker from '../../../public/js/plugins/bootstrap-datetimepicker.js' //import

    export default {
        props: ['retainage','projectid','original_amount','application_id','edit'],

        mounted() {

            let _vm = this
            this.loadLines();

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
                projectLines : true,
                error : false,
                success : false,
                errorMsg : null,
                successMsg : null,
                application_date: null,
                period_to: null,
                isExcessOrShortfall:false,
                total:0,
                lastLine: 0,
                lastLine: 0,
                lines: [],
                addLineItemHTML: [],
                applications: [],
                project_line : {},
                itemNumber: 1,
            };
        },

        methods: {
             setStartDate(value){
                this.start_date = value
             },

            async loadLines(){
            
              let _vm = this;

              await axios.get('/projects/'+this.projectid+'/get-project-applications/?edit='+this.edit)
                .then(function (response) {
                       let res = response.data
                       _vm.applications = res.data
                       _vm.application_date = res.application_date
                       _vm.period_to = res.period_to
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            formatNumber(value){
               return value
            },          
           async  saveApplication() {

              let _vm = this;

              var lines = [];

               if(!this.application_date || !this.period_to ){
                  this.error = true
                  this.errorMsg = 'Application date or Period to is missing!'

                   setTimeout(()=>{
                       this.clearMsg()
                    },2000);

                  return;
               }

              await axios.post('/projects/'+this.projectid+'/applications/',{
                    data : this.applications,
                    application_date: this.application_date,
                    period_to: this.period_to,
                    application_id: this.application_id,
                    edit: this.edit
                })
                .then(function (response) {
                       console.log(response)
                       let res = response.data
                      _vm.success = true
                      _vm.successMsg = res.message
                      
                       setTimeout(()=>{
                         _vm.cancel();
                      },2000);

                      
                })
                .catch(function (error) {
                    console.log(error);
                });

                // _vm.projectLines = false;
            },
            sortOrderBy(orderBy,order){
                 let _vm = this;

                  axios.get('/projects/'+this.projectid+'/get-project-lines/?order='+order+'&orderBy='+orderBy)
                    .then(function (response) {
                           let res = response.data
                           _vm.applications = res.data
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            // resetLines(){
            
            //  this.addLineItemHTML = [];
            //  this.form = {
            //             account_number: [],
            //             description: [],
            //             value: [],
            //             retainage: []
            //     };
         
            // this.loadLines();

            // setTimeout(()=>{
            //    this.clearMsg()
            // },3000);
                 
            // },
            clearMsg(){
                this.error = this.success = false
                this.errorMsg = this.successMsg = null
            },
            cancel(){
               window.location.href =  '/projects/'+this.projectid+'/aia-pay-app';
            },
            fillValue(index){

              let total = parseFloat(this.applications[index].work_completed) + parseFloat(this.applications[index].billed_to_date)
            
              this.applications[index].total_percentage = (total/ this.applications[index].value*100).toFixed(2);

              if(parseFloat(total) >= parseFloat(this.applications[index].value) ){
                 this.applications[index].total_percentage = 100
                 this.applications[index].work_completed = parseFloat(this.applications[index].value) - parseFloat(this.applications[index].billed_to_date)
              }

              if(total + parseFloat(this.applications[index].stored_to_date) >= parseFloat(this.applications[index].value) ){
               
                this.applications[index].materials_stored =  parseFloat(this.applications[index].value) - parseFloat(this.applications[index].work_completed) - parseFloat(this.applications[index].billed_to_date) - parseFloat(this.applications[index].stored_to_date)

              }else{
                  this.applications[index].materials_stored =  0
              }
                
            },

            fillMaterial(index){

              let total = parseFloat(this.applications[index].work_completed) + parseFloat(this.applications[index].billed_to_date)
            
              if(parseFloat(total) >= parseFloat(this.applications[index].value) ){
                 this.applications[index].materials_stored = 0
              }

             if(parseFloat(this.applications[index].total_percentage) >= parseFloat(100) ){
                 this.applications[index].materials_stored = 0
              }

              if(parseFloat(this.applications[index].materials_stored) >= (parseFloat(this.applications[index].value) - total) ){
                 this.applications[index].materials_stored = (parseFloat(this.applications[index].value) - total)
              }
                
            },
            fillPercent(index){ 

              this.applications[index].work_completed = parseFloat(this.applications[index].total_percentage* this.applications[index].value/100) - parseFloat(this.applications[index].billed_to_date);

              if(parseFloat(this.applications[index].work_completed) < 0){
                 this.applications[index].work_completed = 0
               }
              
              if(parseFloat(this.applications[index].total_percentage) >= parseFloat(100) ){
                 this.applications[index].total_percentage = 100
                 this.applications[index].work_completed = parseFloat(this.applications[index].value) - parseFloat(this.applications[index].billed_to_date)
              }
               
             
              if(parseFloat(this.applications[index].work_completed) + parseFloat(this.applications[index].billed_to_date)  + parseFloat(this.applications[index].stored_to_date) >= parseFloat(this.applications[index].value) ){
               
                this.applications[index].materials_stored =  parseFloat(this.applications[index].value) - parseFloat(this.applications[index].work_completed) - parseFloat(this.applications[index].billed_to_date) - parseFloat(this.applications[index].stored_to_date)

              }else{
                  this.applications[index].materials_stored =  0
              }

               this.applications[index].work_completed = this.applications[index].work_completed.toFixed(2) 


            },
            // excessOrShortfall(){
            //     let totalValues= 0 ;

            //     let retainageTotal = 0

            //     this.currentExcess = 0
            //     this.shortFall = 0


            //     $.each(this.applications, function(key, value) {
            //          totalValues = parseFloat(totalValues) + parseFloat(value.value);
            //         retainageTotal = parseFloat(retainageTotal) + (parseFloat(value.value * value.retainage/100) )
            //     });

            //     this.retainageToDate = retainageTotal;

            //     this.total = totalValues;
                  
            //     this.totalEarned =    parseFloat(this.total) -  parseFloat(this.retainageToDate);
                  
            //     if(this.total > this.original_amount || this.total < this.original_amount){
            //         this.isExcessOrShortfall = true;
            //         if(this.total > this.original_amount){
            //            this.currentExcess = parseFloat(this.total) - parseFloat(this.original_amount);
            //         } else{
            //            this.shortFall = parseFloat(this.original_amount) - parseFloat(this.total);
            //         }
            //     }else{
            //       this.isExcessOrShortfall = false;  
            //     }
            // }
        }

    }
</script>

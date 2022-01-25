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
          <h5> Project Line Items  for Application # {{ applications_count }}</h5>
          <table id="project-types-table" class="table table-hover text-center payments-table">
            <thead>
            <tr class="text-danger">
                <th style="width: 80px;">Item No.
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

             <h5 v-if="change_orders.length > 0" > Change Orders  for Application # {{ applications_count }}</h5>
             <table id="project-types-table"  v-if="change_orders.length > 0" class="table table-hover text-center payments-table">
            <thead>
            <tr class="text-danger">
                <th style="width: 80px;">Item No.
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

              <tr v-for="(change_order, index) in change_orders"  >
                <td>{{ index+1 }}</td>
                <td>
                {{ change_orders[index].description }} </td> 
                <td> ${{ new Intl.NumberFormat().format(change_orders[index].value) }} </td>         
                <td>
                ${{ (change_orders[index].billed_to_date) ? 
                new Intl.NumberFormat().format(change_orders[index].billed_to_date) : 0 }}                
                <td>
                ${{ (change_orders[index].billed_to_date) ? 
                new Intl.NumberFormat().format(change_orders[index].stored_to_date) : 0 }}   
                <td><input class="form-control" @input="COfillValue(index)" type="number" v-model="change_orders[index].work_completed" /></td>
                <td><input class="form-control" @input="COfillMaterial(index)" type="number" max="100" v-model="change_orders[index].materials_stored" /></td>
                <td> <input class="form-control" @input="COfillPercent(index)" type="number" max="100" v-model="change_orders[index].total_percentage" /></td>
              </tr>

            </tbody>
            </table> 

            <div class="col-12">
                <button type="button" class="btn btn-danger mt-0" @click="saveApplication">Save Application
                </button>
                <button type="button" class="btn mt-0" @click="cancel" >Cancel
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
                applications_count: null,
                period_to: null,
                total:0,
                lastLine: 0,
                lastLine: 0,
                lines: [],
                addLineItemHTML: [],
                applications: [],
                change_orders: [],
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
                       _vm.applications_count = res.applications_count
                       _vm.application_date = res.application_date
                       _vm.change_orders = res.change_orders
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
                    change_orders : this.change_orders,
                    application_date: this.application_date,
                    period_to: this.period_to,
                    application_id: this.application_id,
                    edit: this.edit
                })
                .then(function (response) {

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
            COfillValue(index){

              let total = parseFloat(this.change_orders[index].work_completed) + parseFloat(this.change_orders[index].billed_to_date)
            
              this.change_orders[index].total_percentage = (total/ this.change_orders[index].value*100).toFixed(2);

              if(parseFloat(total) >= parseFloat(this.change_orders[index].value) ){
                 this.change_orders[index].total_percentage = 100
                 this.change_orders[index].work_completed = parseFloat(this.change_orders[index].value) - parseFloat(this.change_orders[index].billed_to_date)
              }

              if(total + parseFloat(this.change_orders[index].stored_to_date) >= parseFloat(this.change_orders[index].value) ){
               
                this.change_orders[index].materials_stored =  parseFloat(this.change_orders[index].value) - parseFloat(this.change_orders[index].work_completed) - parseFloat(this.change_orders[index].billed_to_date) - parseFloat(this.applications[index].stored_to_date)
              }else{
                  this.change_orders[index].materials_stored =  0
              }
                
            },

            COfillMaterial(index){

              let total = parseFloat(this.change_orders[index].work_completed) + parseFloat(this.change_orders[index].billed_to_date)
            
              if(parseFloat(total) >= parseFloat(this.change_orders[index].value) ){
                 this.change_orders[index].materials_stored = 0
              }

             if(parseFloat(this.change_orders[index].total_percentage) >= parseFloat(100) ){
                 this.change_orders[index].materials_stored = 0
              }

              if(parseFloat(this.change_orders[index].materials_stored) >= (parseFloat(this.change_orders[index].value) - total) ){
                 this.change_orders[index].materials_stored = (parseFloat(this.change_orders[index].value) - total)
              }
                
            },
            COfillPercent(index){ 

              this.change_orders[index].work_completed = parseFloat(this.change_orders[index].total_percentage* this.change_orders[index].value/100) - parseFloat(this.change_orders[index].billed_to_date);

              if(parseFloat(this.change_orders[index].work_completed) < 0){
                 this.change_orders[index].work_completed = 0
               }
              
              if(parseFloat(this.change_orders[index].total_percentage) >= parseFloat(100) ){
                 this.change_orders[index].total_percentage = 100
                 this.change_orders[index].work_completed = parseFloat(this.change_orders[index].value) - parseFloat(this.change_orders[index].billed_to_date)
              }
               
             
              if(parseFloat(this.change_orders[index].work_completed) + parseFloat(this.change_orders[index].billed_to_date)  + parseFloat(this.change_orders[index].stored_to_date) >= parseFloat(this.change_orders[index].value) ){
               
                this.change_orders[index].materials_stored =  parseFloat(this.change_orders[index].value) - parseFloat(this.change_orders[index].work_completed) - parseFloat(this.change_orders[index].billed_to_date) - parseFloat(this.change_orders[index].stored_to_date)

              }else{
                  this.change_orders[index].materials_stored =  0
              }

               this.change_orders[index].work_completed = this.change_orders[index].work_completed.toFixed(2) 

            }
        }

    }
</script>

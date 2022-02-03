<template>
    <div id="proposals-list" class="row py-3">
            <div v-if="success" class="alert alert-success alert-dismissible fade show">
              <strong>Success!</strong> {{ successMsg }}
            </div>
    
            <div  v-else-if="error" class="alert alert-warning alert-dismissible fade show">
            <strong>Error!</strong> {{ errorMsg }}
            </div>

      <div class="table-responsive" v-if="!projectLines">
         <div class="col-lg-6 col-md-6">     
             <button type="button" class="btn btn-danger mt-0"
            @click=cancel >Project Summary</button>

             <button type="button" class="btn btn-danger mt-0"
             v-if="changeOrders.length > 0" @click=swapChangeOrder >Change Orders </button>
         </div>
        
        <div  v-if="isSelect == false">
          <div  class="col-lg-6 col-md-6">
            <div class="form-group">
              <h6>Is this change order for an existing project line?</h6>
              <label class="radio-inline" >
                  <input type="radio" value="0" @change="selectline(0)" v-model="existLine"> No
              </label >
              <label class="radio-inline">
              <input type="radio" value="1" @change="selectline(1)" v-model="existLine"> Yes
              </label>
            </div>
        </div>       
        </div> 
        <div  v-else-if="isSelect == true">
          <div  class="col-lg-6 col-md-6">
            <div class="form-group">
              <h6>Change change order type</h6>
               <button type="button" class="btn mt-0" @click="changeType" >
                Change </button>  
            </div>
          </div>
        </div>

        <div  v-if="existLine == false">
          <div  class="col-lg-12 col-md-12">

               
              <div class="row">
                   <div class="col-lg-5 col-md-6 mx-auto">
                     <div class="form-group">
                          <label class="text-dark" for="password">App #  
                          </label>
                          <input  type="number" class="form-control" placeholder="App Number" v-model="form.app" >
                      </div>
                  </div>
              </div> 
              <div class="row">
                   <div class="col-lg-5 col-md-6 mx-auto">
                     <div class="form-group">
                          <label class="text-dark" for="password">Item No. 
                          </label>
                          <input  type="text" class="form-control" :disabled="account_number != null"  v-model="form.account_number" placeholder="Account Number" >
                      </div>
                  </div>
              </div> 
              <div class="row">
                   <div class="col-lg-5 col-md-6 mx-auto">
                     <div class="form-group">
                          <label class="text-dark" for="password">Description 
                          </label>
                          <input  type="text" class="form-control" :disabled="description != null"  v-model="form.description" placeholder="Description" >
                      </div>
                  </div>
              </div>
             <div class="row">
                   <div class="col-lg-5 col-md-6 mx-auto">
                     <div class="form-group">
                          <label class="text-dark" for="password">Scheduled Value 
                          </label>
                          <input  type="text" class="form-control" v-model="form.value" placeholder="Scheduled Value" >
                      </div>
                  </div>
              </div>

              <div class="row">
                   <div class="col-lg-5 col-md-6 mx-auto">
                     <div class="form-group">
                          <label class="text-dark" for="password">Retainage
                          </label>
                          <input   type="number" class="form-control" v-model="form.retainage" placeholder="Retainage" step="any">
                      </div>
                  </div>
              </div> 

             <div v-if="edit" class="col-12">
                <button type="button" class="btn btn-danger mt-0" @click="saveChangeOrder">
                  Update Change Order
                </button>
                <button type="button" class="btn mt-0"  @click="editCancel" >Cancel
                </button> 
            </div>
            <div v-else class="col-12">
                <button type="button" class="btn btn-danger mt-0" @click="saveChangeOrder">
                  Save Change Order
                </button>
                <button type="button" class="btn mt-0" v-if="description != null" @click="changeOrder" >Cancel
                </button> 
                <button type="button" class="btn mt-0" v-if="description == null" @click="changeType" >Cancel
                </button> 
            </div>

         </div>
        
        </div> 
        <div  v-else-if="existLine == true">
          <div  class="col-lg-12 col-md-12">
            
          <table id="project-types-table" class="table table-hover text-center payments-table">
            <thead>
            <tr class="text-danger">
                <th> # </th>
                <th>Description</th>
                <th>Scheduled Value</th>
                <th>Retainage</th>
            </tr>
            </thead>
            <tbody>

              <tr v-for="(project_line, index) in applications"  >
                <td> <input style="cursor: pointer;" type="radio" :value="index" @change="selectOrder" v-model="selectOrderLine"> </td>
                <td>
                {{ applications[index].description }} </td> 
                <td> ${{ new Intl.NumberFormat().format(applications[index].value) }} </td>         
                <td>{{ retainage}}</td>
              </tr>

            </tbody>
            </table> 
           </div>

           <div class="col-12">
                <button type="button" class="btn btn-danger mt-0" @click="continueOrder">Continue
                </button>
                <button type="button" class="btn mt-0" @click="changeType" >Cancel
                </button>  
            </div>
        </div>

        </div>

        <div class="table-responsive" v-else>

              <table id="project-types-table" class="table table-hover text-center payments-table">
                <thead>
                <tr class="text-danger">
                    <th >App #</th>
                    <th>Item No.</th>
                    <th>Description</th>
                    <th>Scheduled Value</th>
                    <th>Retainage</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                  <tr v-for="(cOrder, index) in changeOrders"  >
                    <td>{{ cOrder.app }} </td> 
                    <td>{{ cOrder.account_number }} </td> 
                    <td>{{ cOrder.description }} </td> 
                    <td> ${{ new Intl.NumberFormat().format(cOrder.value) }} </td>          
                    <td>{{ cOrder.retainage }} </td> 
                    <td><i @click="editOrder(index)" style="cursor: pointer;" class="fa fa-pencil"></i> <i @click="deleteOrder(cOrder.id)" style="cursor: pointer;" class="fa fa-trash"></i> </td>
                  </tr>

                </tbody>
                </table> 
              
               <div class="col-lg-6 col-md-6">
               <button type="button" class="btn btn-danger mt-0" @click=cancel >Project Summary
               </button>

               <button type="button" class="btn btn-danger mt-0" @click=swapChangeOrder >New Change Order </button>

               </div>
               
        </div> 
    </div>
</template>

<script>

    export default {
        props: ['retainage','projectid','project','applications_count'],

        mounted() {
            this.loadLines();
        },
        data() {
            return {
                projectLines : false,
                isSelect: false,
                existLine : null,
                edit : null,
                error : false,
                success : false,
                errorMsg : null,
                successMsg : null,
                applications: [],
                changeOrders: [],
                description: null,
                account_number: null,
                selectOrderLine: null,
                isRevised: false,
                form:{
                   'id' : null,
                   'app' : (this.applications_count != 0) ? this.applications_count : 1,
                   'value' : null,
                   'description' : null,
                   'account_number' : null,
                   'retainage' : this.retainage
                },
                
            }
        },
       
        methods: {
             selectline(val){
              this.existLine = val
              this.isSelect = true
             },
             changeType(){
                this.form.description = null
                this.form.account_number = null
                this.description = null
                this.account_number = null
                this.isSelect = false
                this.existLine = null
                this.isRevised = false
             },
             selectOrder(){
               this.form.description = this.applications[this.selectOrderLine].description
               this.form.account_number = this.applications[this.selectOrderLine].account_number
               this.description = true
               this.account_number = true
            },
            continueOrder(){
                this.existLine = false
                this.isRevised = true
            },
            changeOrder(){
                this.existLine = true
                this.isRevised = false
            },
            swapChangeOrder(){
               this.projectLines = !this.projectLines
               this.existLine = null
               this.isSelect = false
            },
            editCancel(){
               this.projectLines = !this.projectLines
               this.edit = null
               this.form = {}
               this.form.retainage = this.retainage
               this.existLine = null
               this.isSelect = false

            },
            async loadLines(){
            
              let _vm = this;

              await axios.get('/projects/'+this.projectid+'/get-project-applications/?edit='+this.edit)
                .then(function (response) {
                       let res = response.data
                       _vm.applications = res.data
                       _vm.loadChangeOrders()

                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            async loadChangeOrders(){

                 let _vm = this;

                  axios.get('/projects/'+this.projectid+'/get-change-orders/')
                    .then(function (response) {
                           let res = response.data
                           _vm.changeOrders = res.data

                          if(_vm.changeOrders.length > 0) {
                             _vm.projectLines = true;
                          }

                    })
                    .catch(function (error) {
                        console.log(error);
                    });
                   
            },
           async  saveChangeOrder() {

              let _vm = this;

              var lines = [];

               if(!this.form.app || !this.form.description || !this.form.account_number || !this.form.retainage || !this.form.value ){
                  this.error = true
                  this.errorMsg = 'Fill all details!'

                   setTimeout(()=>{
                       this.clearMsg()
                    },2000);

                  return;
               }

               if(parseInt(this.form.app) > ( parseInt(this.applications_count) > 0  ? parseInt(this.applications_count) : 1)){
                  this.error = true
                  this.errorMsg = 'App can`t be greater!'

                   setTimeout(()=>{
                       this.clearMsg()
                    },2000);

                  return;
               }

              await axios.post('/projects/'+this.projectid+'/change-orders/',{
                    revised : this.isRevised,
                    data : this.form,
                    edit: this.edit
                })
                .then(function (response) {
                       let res = response.data
                      _vm.success = true
                      _vm.successMsg = res.message
                      _vm.loadChangeOrders()
                      _vm.clearMsg()
                      
                })
                .catch(function (error) {
                    console.log(error);
                });
                _vm.edit = null
                _vm.form = {}
                _vm.form.retainage = _vm.retainage
                _vm.projectLines = true;
            },
            clearMsg(){
                this.error = this.success = false
                this.errorMsg = this.successMsg = null
            },
            cancel(){
               window.location.href =  '/projects/'+this.projectid+'/aia-pay-app';
            },
            editOrder(index){
              let  _vm = this
              let orderEdit = _vm.changeOrders[index]
  
              $.each(orderEdit, function(i, val) {
                   _vm.form[i] = val
               });

              _vm.edit = true
              _vm.existLine = false
              _vm.isSelect = null
              _vm.projectLines = false

            },
            deleteOrder($id){
                 
                if (!confirm("Are you sure to delete!")) {
                  return;
                } 
 
                let _vm = this;

                axios.delete('/projects/'+$id+'/change-orders/') 
                   .then(function (response) {
                       let res = response.data
                       if(res.error){
                            _vm.error = true
                            _vm.errorMsg = res.message
                       }else{
                          _vm.success = true
                          _vm.successMsg = res.message
                       }
                      _vm.loadChangeOrders();
                      _vm.clearMsg();
                })
                .catch(function (error) {
                    console.log(error);
                });

            }
        }

    }
</script>

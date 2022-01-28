<template>
    <div id="proposals-list" class="row py-3">
      <div v-if="success" class="alert alert-success alert-dismissible fade show">
        <strong>Success!</strong> {{ successMsg }}
      </div>

      <div  v-else-if="error" class="alert alert-warning alert-dismissible fade show">
      <strong>Error!</strong> {{ errorMsg }}
      </div>

    <div class="table-responsive" v-if="projectLines">
        <div class="row col-12">
        <h5 class="col-lg-12 col-md-12"> Set Final Application Dates </h5>
      
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
        
        <div class="col-12">
                <button type="button" class="btn btn-danger mt-0" @click="saveCloseProject">Close Project
                </button>
                <button type="button" class="btn mt-0" @click="cancel" >Cancel
                </button>  
    
        </div>
      </div>
            
        </div>
        
    </div>
</template>

<script>
    import datetimepicker from '../../../public/js/plugins/bootstrap-datetimepicker.js' //import

    export default {
        props: ['retainage_value','projectid'],

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
                period_to: null
            };
        },

        methods: {         
           async  saveCloseProject() {

              let _vm = this;


               if(!this.application_date || !this.period_to ){
                  this.error = true
                  this.errorMsg = 'Application date or Period to is missing!'

                   setTimeout(()=>{
                       this.clearMsg()
                    },2000);

                  return;
               }

              await axios.post('/projects/'+this.projectid+'/close-project/',{
                    application_date: this.application_date,
                    retainage_value: this.retainage_value,
                    period_to: this.period_to
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
                      
                      //  setTimeout(()=>{
                      //    _vm.cancel();
                      // },2000);

                      
                })
                .catch(function (error) {
                    console.log(error);
                });

                // _vm.projectLines = false;
            },
            clearMsg(){
                this.error = this.success = false
                this.errorMsg = this.successMsg = null
            },
            cancel(){
               window.location.href =  '/projects/'+this.projectid+'/aia-pay-app';
            }
        }

    }
</script>

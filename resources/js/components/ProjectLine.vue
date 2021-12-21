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
                <th>#</th>
                <th>Description</th>
                <th>Scheduled Value</th>
                <th>Retainage %</th>
                <th>Delete </th>
            </tr>
            </thead>
            <tbody>

              <tr v-for="(project_line, index) in project_lines" :key='project_line.id' >
                <td>#</td>
                <td>
                <input class="form-control" type="text"  v-model="project_lines[index].description"  /></td>
                <td><input class="form-control" type="number" v-model:form.value[index]="project_lines[index].value" /></td>
                <td><input class="form-control" type="number" max="100" v-model:form.retainage[index]="project_lines[index].retainage" /></td>
                <td><i @click="deleteLine(project_line.id)" style="cursor: pointer;" class="fa fa-trash"></i></td>
              </tr>

              <tr v-for="(addLineItem, index) in addLineItemHTML">
                <td>#</td>
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

            </div>

        </div>
        <div v-else>
               <div>
                   
                   Project Summary

               <button type="button" class="btn btn-danger mt-0" @click="editLineItem" >Edit Line Items
                </button>

               </div>
               
        </div> 
    </div>
</template>

<script>
    export default {
        props: ['retainage','projectid','baseurl'],

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
                lines: [],
                addLineItemHTML: [],
                project_lines: [],
                project_line : {},
                form :{
                        description: [],
                        value: [],
                        retainage: []
                },
                itemNumber: 1,
            };
        },

        methods: {
            async loadLines(){
            
              let _vm = this;

              await axios.get(this.baseurl+'/projects/'+this.projectid+'/get-project-lines/')
                .then(function (response) {
                       let res = response.data
                       _vm.project_lines = res.data
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
    
                let _vm = this;

                axios.delete(this.baseurl+'/projects/'+$id+'/project-lines/') 
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

            deleteHTMLLine(index){
              this.addLineItemHTML.splice(index, 1);
            },          
           async  saveLineItem() {

              let _vm = this;

              var lines = [];

              this.project_lines.map(function(value, key) {
                  lines.push({
                        description: value.description, 
                        retainage: value.retainage, 
                        value: value.value, 
                        id:  value.id
                    });
               }); 
                 

               if((!lines) && (!this.form.description[0] || !this.form.value[0] || !this.form.retainage[0])){
                  this.error = true
                  this.errorMsg = 'Enter lines to save data!'

                   setTimeout(()=>{
                       this.clearMsg()
                    },2000);

                  return;
               }  
               
              await axios.post(this.baseurl+'/projects/'+this.projectid+'/add-project-lines/',{
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
            }
        }

    }
</script>

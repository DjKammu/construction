<template>
    <div id="proposals-list">
      <div v-if="success" class="alert alert-success alert-dismissible fade show">
        <strong>Success!</strong> {{ successMsg }}
      </div>

      <div  v-else-if="error" class="alert alert-warning alert-dismissible fade show">
      <strong>Error!</strong> {{ errorMsg }}
      </div>

      <input type="file" ref="fileupload" v:model="file" name="attachment" @change="onUpload">

       <span v-if="ifFile">
         <img style="width:32px;cursor: pointer;" @click="openAttachment(URL)" 
        :src="'/img/' + extension + '.png' " />
        </span>
        
    </div>
</template>

<script>
    export default {
        props: ['projectid'],

        mounted() {
           this.getAttachment();
        },
        data() {
            return {
                ifFile : false,
                error : false,
                success : false,
                errorMsg : null,
                successMsg : null,
                attachment : [],
                extension : null,
                URL  : null
            };
        },

        methods: {         
           async  uploadAttachment() {

              let _vm = this;

              var form = new FormData();
              form.append("attachment", this.attachment);

              var config = {
                  header: { "Contect-type": "multipart/form-data" },
              };

              await axios.post('/projects/'+this.projectid+'/attachment/',form)
                .then(function (response) {

                       let res = response.data
                      
                      console.log(res);

                      if(res.error){
                            _vm.error = true
                            _vm.errorMsg = res.message
                       }else{
                          _vm.success = true
                          _vm.successMsg = res.message
                       }
                      
                       setTimeout(()=>{
                         _vm.clearMsg();
                      },2000);
                })
                .catch(function (error) {
                    console.log(error);
                });
            },

            async  getAttachment() {

              let _vm = this;

              await axios.get('/projects/'+_vm.projectid+'/attachment/')
                .then(function (response) {

                       let res = response.data
                       _vm.extension = res.extension
                       _vm.URL = res.URL
                       _vm.ifFile = true
                      
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            openAttachment($url){
                 let a= document.createElement('a');
                 a.target= '_blank';
                 a.href= $url;
                 a.click();
            },

            onUpload(event) {
              this.attachment  = event.target.files[0];
              this.uploadAttachment();

            },
            clearMsg(){
                this.error = this.success = false
                this.errorMsg = this.successMsg = null
                this.file = null
                this.$refs.fileupload.value = null;
                this.getAttachment()

            }
        }

    }
</script>

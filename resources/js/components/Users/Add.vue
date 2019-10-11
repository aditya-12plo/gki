<template>
    <div>


<div class="breadcrumb"> 
  <div class="panel">
    <h1 class="animated fadeInLeft" align="center">Add New User</h1>                
  </div>
</div>

<form @submit.prevent="submitData" method="POST">   
<div class="card mb-5">
    <div class="card-body">
        <div class="d-flex flex-column">
            <div class="form-group">
                <label>Name</label>
                <input type="text" class="form-control" v-model="forms.name" required="" aria-required="true">
            </div> 
            <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" v-model="forms.email" required="" aria-required="true">
            </div> 
            <div class="form-group">
                <label>Password</label>
                <input type="password" class="form-control" v-model="forms.password" required="" aria-required="true">
            </div> 
            <div class="form-group">
                <label>Status</label>
                    <select class="form-control" v-model="forms.status" required="" aria-required="true">
                        <option v-for="status in statusDropdown" :value="status">{{ status }}</option>
                    </select> 
            </div> 
            <div class="form-group">
                <label>Role</label>
                    <select class="form-control" v-model="forms.role_id" required="" aria-required="true">
                        <option v-for="(role, index) in roles" :value="role.id">{{ role.role_name }}</option>
                    </select> 
            </div> 
            <button class="btn btn-info pd-x-20" type="submit">Submit</button>
            <br>
            <button class="btn btn-warning pd-x-20" type="button" @click="backLink()">Back</button>
            </div>
    </div>
</div>
</form>
 

    </div>
</template>

<script>
    
import Vue from 'vue'
import VueSweetalert2 from 'vue-sweetalert2'
import Loading from 'vue-loading-overlay'
import 'vue-loading-overlay/dist/vue-loading.css'


Vue.use(VueSweetalert2)
Vue.use(Loading);


export default {
    props: {
      typenya: {
        type: String,
        required: true
      }
    },
  components: { 

  },
  data () {
    return {
        maxToasts: 100,
        position: 'up right',
        closeBtn: true,  
        errors: [],
        roles:[],
        statusDropdown: ['active','inactive'],
        forms: {name:'', email:'', role_id:'', status:'',role:[], password : ''},
        token: localStorage.getItem('token'), 

    }
  },
        watch: { 

        },
        methods: { 
            backLink() {
			   this.$router.go(-1);
            } ,
            dataAction () {
                if(this.typenya === "add-user")
                {
                    return true;
                }else{
                this.$router.push('/page-not-found');
                } 
            },
            dataRole () {
                this.loading();
                axios.get('/get-role').then((response) => {
                    if(!response.data){ 
                        window.location.href = window.webURL; 
                    }else{ 
                        if(response.data.status == 200){ 
                            this.roles = response.data.data;
                        }else{
                            this.resultError(error.response.data.errors) 
                        }
                    }
                }).catch(error => {
                    if (! _.isEmpty(error.response)) {
                        if (error.response.status = 422) {
                            this.resultError(error.response.data.errors)
                        }else if (error.response.status = 500) {
                            this.$router.push('/server-error');
                        }else{
                            this.$router.push('/page-not-found');
                        }
                    }
                });
            },
            submitData() {
                this.$swal({
                title: 'Are you sure ?',
                text: 'submit data',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes!'
                }).then((result) => {
                    if (result.value) {
                        axios.post('/users', this.forms).then(response => {
                            if(!response.data){ 
                                window.location.href = window.webURL; 
                            }else{ 
                                if(response.data.status == 200){ 
                                    this.errors = '';
                                    this.forms.name = '';
                                    this.forms.password = '';
                                    this.forms.email = ''; 
                                    this.success(response.data.message);
                                }else{
                                    this.resultError(response.data.message)
                                }
                            }
                        }).catch(error => {
                            if (! _.isEmpty(error.response)) {
                                if (error.response.status = 422) {
                                   this.resultError(error.response.data.errors)
                                }else if (error.response.status = 500) {
                                    this.$router.push('/server-error');
                                }else{
                                    this.$router.push('/page-not-found');
                                }
                            }
                                        
                        })
                    }
                })
                    
            },

            resultError(data) {  
                 var count = Object.keys(data).length;
                for(var x=0; x < count;x++){ 
                    var nameOb      = Object.keys(data)[x];
                    var objectData  = data[nameOb];
                    for(var y=0; y < objectData.length;y++){ 
                        this.error(objectData[y]);
                       // console.log(objectData[y]);
                    }
                }
            },

            success(kata) {
                this.$swal({
                    position: 'top-end',
                    type: 'success',
                    title: kata,
                    showConfirmButton: false,
                    timer: 1500
                })
            },
            error(kata) {
                this.$swal({
                    position: 'top-end',
                    type: 'error',
                    title: kata,
                    showConfirmButton: false,
                    timer: 1500
                })
            },
            loading(){ 
                let loader = this.$loading.show({
                  // Optional parameters
                  container: this.fullPage ? null : this.$refs.formContainer,
                  loader:'dots',
                  color:'#2196F3',
                  canCancel: true, 
                  onCancel: this.onCancelLoading,
                }); 
                setTimeout(() => {
                  loader.hide()
                },1000);
            },
            onCancelLoading() {
               console.log('User cancelled the loader.'); 
            }  	

        },
        events: { 

        },
        created: function() { 

        },
		mounted() { 
          this.dataAction();
          this.dataRole();
        }

}
</script>
<style> 
</style>
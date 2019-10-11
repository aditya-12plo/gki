<template>
    <div>   


<div class="breadcrumb"> 
  <div class="panel">
    <h1 class="animated fadeInLeft" align="center">Profile</h1>                
  </div>
</div>

<div class="card mb-5">
    <div class="card-body">
        <div class="d-flex flex-column">

            <div class="form-group">
                <label>Name</label>
                <h5 class="media-heading"><b>{{this.data.name}}</b></h5> 
            </div> 

            <div class="form-group">
                <label>Email</label>
                <h5 class="media-heading"><b>{{this.data.email}}</b></h5>
            </div> 

            <div class="form-group">
                <label>Status</label>
                <h5 class="media-heading"><b>{{this.data.status}}</b></h5>
            </div> 

            <div class="form-group">
                <label>Role</label>
                <h5 class="media-heading"><b>{{this.data.role.role_name}}</b></h5>
            </div>


        </div>
    </div>
</div>
 

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
  components: { 

  },
  data () {
    return {  
        errors: [],
        data: {name : '', email:'', status:''},
        token: localStorage.getItem('token'), 

    }
  },
        watch: { 

        },
        methods: {
            fetchIt(){
                this.loading();
                axios.get('/user/get-profile').then((response) => {
                    if(!response.data){ 
                        window.location.href = window.webURL; 
                    }else{ 
                        if(response.data.status == 200){ 
                            this.data = response.data.data;
                        }else{
                            window.location.href = window.webURL; 
                        }
                    }
                }).catch(error => {
                    if (! _.isEmpty(error.response)) {
                        if (error.response.status = 422) {
                            this.$router.push('/server-error');
                        }else if (error.response.status = 500) {
                            this.$router.push('/server-error');
                        }else{
                            this.$router.push('/page-not-found');
                        }
                    }
                });
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
            this.fetchIt();
        }

}
</script>
<style> 
</style>
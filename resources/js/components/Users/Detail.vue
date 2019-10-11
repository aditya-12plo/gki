<template>
    <div>



<div class="breadcrumb"> 
  <div class="panel">
    <h1 class="animated fadeInLeft" align="center">Detail User</h1>                
  </div>
</div>

<div class="card mb-5">
    <div class="card-body">
        <div class="d-flex flex-column">

            <div class="form-group">
                <label>Name</label>
                <h5 class="media-heading"><b>{{this.forms.name}}</b></h5> 
            </div>  
            <div class="form-group">
                <label>Email</label>
                <h5 class="media-heading"><b>{{this.forms.email}}</b></h5> 
            </div> 
            <div class="form-group">
                <label>Status</label>
                <h5 class="media-heading"><b>{{this.forms.status}}</b></h5> 
            </div> 
            <div class="form-group">
                <label>Role</label>
                <h5 class="media-heading"><b>{{this.forms.role.role_name}}</b></h5> 
            </div> 
            <div class="form-group">
                <label>Created At</label>
                <h5 class="media-heading"><b>{{this.formatDate(this.forms.created_at)}}</b></h5> 
            </div> 
            <br>
            <button class="btn btn-warning pd-x-20" type="button" @click="backLink()">Back</button>
            
        </div>
    </div>
</div>

       

    </div>
</template>

<script>
    class Errors{
            constructor(){
                this.errors = {};
            }
            get(field){
                if(this.errors[field]){
                    return  this.errors[field][0];
                }
            }
            record(errors){
                this.errors = errors.response.data;
            }
            any(){
                return Object.keys(this.errors).length > 0;
            }
            has(field){
                return this.errors.hasOwnProperty(field);
            }
            clear(field){
                if(field) delete this.errors[field];
                this.errors = {};
            }
            clearAll(){
                this.errors = "";
            }
        }

        class CrudForm {
            constructor(data) {
                this.originalData = data;
                for(let field in data){
                    this[field] = data[field];
                }
            }
            reset(){
                for(let field in this.originalData){
                    this[field] = '';
                }
            }
            /*  Set a value to the temp , verify if has this item and update  */
            setFillItem(item , index){
                for(let field in this.originalData){
                    if(field in item){
                        this[field] = item[field];
                    }else{
                        // if is index
                        if(field == 'index'){ this[field] = index; }
                    }
                }
            }
            data(){
                let data = Object.assign({} , this);
                delete data.originalData;
                delete data.errors;
                return data;
            }
        }


import Vue from 'vue'
import accounting from 'accounting'
import moment from 'moment'
import VueSweetalert2 from 'vue-sweetalert2'
import Loading from 'vue-loading-overlay'
import 'vue-loading-overlay/dist/vue-loading.css'


Vue.use(VueSweetalert2)
Vue.use(Loading);


export default {
props: {
    rowDatanya: {
        type: Object,
        required: true
    },
    typenya: {
        type: String,
        required: true
    }
},
  components: { 

  },
  data () {
    return {  
        errors: [], 
        token: localStorage.getItem('token'), 
        forms: new CrudForm({id:'' , name:'' , email:'', status:'' , role:'',  created_at:''}), 
    }
  },
        watch: { 

        },
        methods: { 
            backLink() {
			   this.$router.go(-1);
            } ,
            dataAction () {
                if(this.typenya === "detail-user"){
                    this.forms.setFillItem(this.rowDatanya);
                }else{
                    this.$router.push('/page-not-found');
                }
                
            },
             

            formatDate (value, fmt = 'DD-MM-YYYY HH:mm:ss') {
                return (value == null)
                    ? ''
                    : moment(value, 'YYYY-MM-DD HH:mm:ss').format(fmt)
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
        }

}
</script>
<style> 
</style>
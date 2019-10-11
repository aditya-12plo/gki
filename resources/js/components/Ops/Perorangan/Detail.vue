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
                <label>Nama</label>
                <h5 class="media-heading"><b>{{this.forms.nama}}</b></h5>  <img :src="this.url+'/account-image/'+forms.image_akun" class="imgnya img-responsive">
            </div>  
            <div class="form-group">
                <label>Email</label>
                <h5 class="media-heading"><b>{{this.forms.email}}</b></h5> 
            </div> 

            <div class="form-group">
                <label>Nomor Akun</label>
                <h5 class="media-heading"><b>{{this.forms.nomor_akun}}</b></h5> 
            </div> 
            <div class="form-group">
                <label>Tanggal Registrasi</label>
                <h5 class="media-heading"><b>{{formatDateLocal(this.forms.tanggal_registrasi)}}</b></h5> 
            </div> 
            <div class="form-group">
                <label>Jenis Kartu Identitas</label>
                <h5 class="media-heading"><b>{{this.forms.jenis_identitas}}</b></h5> 
            </div> 
            <div class="form-group">
                <label>Nomor Identitas</label>
                <h5 class="media-heading"><b>{{this.forms.nomor_identitas}}</b></h5> 
            </div> 
            <div class="form-group">
                <label>Masa Berlaku</label>
                <h5 class="media-heading"><b>{{formatDateLocal(this.forms.masa_berlaku)}}</b></h5> 
            </div>
            <div class="form-group">
                <label>NPWP</label>
                <h5 class="media-heading"><b>{{this.forms.npwp}}</b></h5> 
            </div>
            <div class="form-group">
                <label>Tempat / Tanggal Lahir</label>
                <h5 class="media-heading"><b>{{this.forms.tempat_lahir}} / {{formatDateLocal(this.forms.tanggal_lahir)}}</b></h5> 
            </div>
            <div class="form-group">
                <label>Jenis Kelamin</label>
                <h5 class="media-heading"><b>{{this.forms.jenis_kelamin}}</b></h5> 
            </div>
            <div class="form-group">
                <label>Profil(Profesi)</label>
                <h5 class="media-heading"><b>{{this.forms.profesi}}</b></h5> 
            </div>
            <div class="form-group">
                <label>Nomor Rekening Bank</label>
                <h5 class="media-heading"><b>{{this.forms.nomor_rekening_bank}}</b></h5> 
            </div>
            <div class="form-group">
                <label>Nomor Telepon</label>
                <h5 class="media-heading"><b>{{this.forms.nomor_telepon}}</b></h5> 
            </div>
            <div class="form-group">
                <label>Alamat Email</label>
                <h5 class="media-heading"><b>{{this.forms.email}}</b></h5> 
            </div>
            <div class="form-group">
                <label>Wilayah Domisili</label>
                <h5 class="media-heading"><b>{{this.forms.wilayah_domisili}}</b></h5> 
            </div>
            <div class="form-group">
                <label>Alamat</label>
                <h5 class="media-heading"><b>{{this.forms.alamat}}</b></h5> 
            </div>
            <br>
            <input class="submit btn btn-primary" @click="backLink()" type="button" value="Back">
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
        url : window.webURL,
        errors: [], 
        token: localStorage.getItem('token'), 
        forms: new CrudForm({id:'' , nama:'',nomor_akun:'',tanggal_registrasi:'',tanggal_nasabah:'',jenis_identitas:'',nomor_identitas:'',masa_berlaku:'',npwp:'',tempat_lahir:'',tanggal_lahir:'',jenis_kelamin:'',profesi:'',nomor_rekening_bank:'',nomor_telepon:'',email:'',wilayah_domisili:'',alamat:'',image_akun:'',  created_at:''}), 
    }
  },
        watch: { 

        },
        methods: { 
            backLink() {
			   this.$router.go(-1);
            } ,
            dataAction () {
                if(this.typenya === "detail-perorangan"){
                    this.forms.setFillItem(this.rowDatanya);
                }else{
                    this.$router.push('/page-not-found');
                }
                
            },
             

            formatDateLocal (value, fmt = 'DD-MM-YYYY') {
                return (value == null)
                    ? ''
                    : moment(value, 'YYYY-MM-DD').format(fmt)
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
.imgnya{
        max-height: 150px;
        max-width: 150px;
    }
</style>
<template>
    <div>  

<div class="breadcrumb"> 
  <div class="panel">
    <h1 class="animated fadeInLeft" align="center">Detail Non Perorangan</h1>                
  </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="row">

            <div class="col-lg-6 mb-3">
                <h3 class="card-title"><b>Informasi Profil Badan Usaha / Yayasan / Badan Usaha Lainnya</b></h3>
                <div class="form-group">
                <label>Nama Perusahaan</label>
                <h5 class="media-heading"><b>{{this.forms.nama_perusahaan}}</b></h5> 
                </div> 
                <div class="form-group">
                <label>Nomor Akun</label>
                <h5 class="media-heading"><b>{{this.forms.nomor_akun}}</b></h5> 
                </div>
                <div class="form-group">
                <label>Nomor Ijin Usaha</label>
                <h5 class="media-heading"><b>{{this.forms.nomor_ijin_usaha}}</b></h5> 
                </div>
                <div class="form-group">
                <label>Bidang Usaha</label>
                <h5 class="media-heading"><b>{{this.forms.bidang_usaha}}</b></h5> 
                </div>
                <div class="form-group">
                <label>Tempat / Tanggal Pendirian</label>
                <h5 class="media-heading"><b>{{this.forms.tempat_pendirian}} / {{formatDateLocal(this.forms.tanggal_pendirian)}}</b></h5> 
                </div>
                <div class="form-group">
                <label>Bentuk Hukum</label>
                <h5 class="media-heading"><b>{{this.forms.bentuk_hukum}}</b></h5> 
                </div>
                <div class="form-group">
                <label>NPWP</label>
                <h5 class="media-heading"><b>{{this.forms.npwp}}</b></h5> 
                </div>
                <div class="form-group">
                <label>Profil(Profesi)</label>
                <h5 class="media-heading"><b>{{this.forms.profil}}</b></h5> 
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

            </div>
            
            <div class="col-lg-6 mb-3">
                <h3 class="card-title"><b>Penerima Kuasa</b></h3>
                <div class="form-group">
                <label>Nama </label>
                <h5 class="media-heading"><b>{{this.forms.kuasa.nama}}</b></h5> 
                </div>
                <div class="form-group">
                <label>Nama Alias</label>
                <h5 class="media-heading"><b>{{this.forms.kuasa.nama_alias}}</b></h5> 
                </div>
                <div class="form-group">
                <label>Jenis Kartu Identitas</label>
                <h5 class="media-heading"><b>{{this.forms.kuasa.jenis_kartu_identitas}}</b></h5> 
                </div>
                <div class="form-group">
                <label>Nomor Identitas</label>
                <h5 class="media-heading"><b>{{this.forms.kuasa.nomor_identitas}}</b></h5> 
                </div>
                <div class="form-group">
                <label>Masa Berlaku</label>
                <h5 class="media-heading"><b>{{this.forms.kuasa.masa_berlaku}}</b></h5> 
                </div>
                <div class="form-group">
                <label>NPWP</label>
                <h5 class="media-heading"><b>{{this.forms.kuasa.npwp}}</b></h5> 
                </div>
                <div class="form-group">
                <label>Tempat / Tanggal Lahir</label>
                <h5 class="media-heading"><b>{{this.forms.kuasa.tempat_lahir}} /  {{formatDateLocal(this.forms.kuasa.tanggal_lahir)}}</b></h5> 
                </div>
                <div class="form-group">
                <label>Status Perkawinan</label>
                <h5 class="media-heading"><b>{{this.forms.kuasa.status_perkawinan}}</b></h5> 
                </div>
                <div class="form-group">
                <label>Jenis Kelamin</label>
                <h5 class="media-heading"><b>{{this.forms.kuasa.jenis_kelamin}}</b></h5> 
                </div>
                <div class="form-group">
                <label>Nomor telepon</label>
                <h5 class="media-heading"><b>{{this.forms.kuasa.nomor_telepon}}</b></h5> 
                </div>
                <div class="form-group">
                <label>Alamat Tinggal Sesuai Identitas</label>
                <h5 class="media-heading"><b>{{this.forms.kuasa.alamat}}</b></h5> 
                </div>
                <div class="form-group">
                <label>Alamat Tinggal Sekarang</label>
                <h5 class="media-heading"><b>{{this.forms.kuasa.alamat_sekarang}}</b></h5> 
                </div> 

            </div>

            <div class="col-lg-6 mb-3">
                <h3 class="card-title"><b>Benefecial Owner</b></h3>
                <div class="form-group">
                <label>Nama </label>
                <h5 class="media-heading"><b>{{this.forms.owner.nama}}</b></h5> 
                </div>
                <div class="form-group">
                <label>Nama Alias</label>
                <h5 class="media-heading"><b>{{this.forms.owner.nama_alias}}</b></h5> 
                </div>
                <div class="form-group">
                <label>Jenis Kartu Identitas </label>
                <h5 class="media-heading"><b>{{this.forms.owner.jenis_kartu_identitas}}</b></h5> 
                </div>
                <div class="form-group">
                <label>Nomor Identitas </label>
                <h5 class="media-heading"><b>{{this.forms.owner.nomor_identitas}}</b></h5> 
                </div>
                <div class="form-group">
                <label>Masa Berlaku </label>
                <h5 class="media-heading"><b>{{this.forms.owner.masa_berlaku}}</b></h5> 
                </div>
                <div class="form-group">
                <label>NPWP </label>
                <h5 class="media-heading"><b>{{this.forms.owner.npwp}}</b></h5> 
                </div>
                <div class="form-group">
                <label>Tempat / Tanggal Lahir</label>
                <h5 class="media-heading"><b>{{this.forms.owner.tempat_lahir}} /  {{formatDateLocal(this.forms.owner.tanggal_lahir)}}</b></h5> 
                </div> 
                <div class="form-group">
                <label>Status Perkawinan </label>
                <h5 class="media-heading"><b>{{this.forms.owner.status_perkawinan}}</b></h5> 
                </div>
                <div class="form-group">
                <label>Jenis Kelamin </label>
                <h5 class="media-heading"><b>{{this.forms.owner.jenis_kelamin}}</b></h5> 
                </div>
                <div class="form-group">
                <label>Nomor telepon </label>
                <h5 class="media-heading"><b>{{this.forms.owner.nomor_telepon}}</b></h5> 
                </div>
                <div class="form-group">
                <label>Alamat Tinggal Sesuai Identitas </label>
                <h5 class="media-heading"><b>{{this.forms.owner.alamat}}</b></h5> 
                </div>
                <div class="form-group">
                <label>Alamat Tinggal Sekarang </label>
                <h5 class="media-heading"><b>{{this.forms.owner.alamat_sekarang}}</b></h5> 
                </div>  
            </div>

        </div> 
            <input class="submit btn btn-primary" @click="backLink()" type="button" value="Back">
            <br><br>
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
        forms: new CrudForm({id:'' , nama_perusahaan:'',nomor_akun:'',nomor_ijin_usaha:'',bidang_usaha:'',
        tempat_pendirian:'',tanggal_pendirian:'',bentuk_hukum:'',npwp:'',profil:'',nomor_rekening_bank:'',nomor_telepon:'',email:'',
        wilayah_domisili:'',alamat:'', kuasa:'', owner:'' ,  created_at:''}), 
    }
  },
        watch: { 

        },
        methods: { 
            backLink() {
			   this.$router.go(-1);
            } ,
            dataAction () {
                if(this.typenya === "detail-non-perorangan"){
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
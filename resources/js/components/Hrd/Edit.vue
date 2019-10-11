<template>
    <div>   

<div class="breadcrumb"> 
  <div class="panel">
    <h1 class="animated fadeInLeft" align="center">Form Perubahan Karyawan</h1>                
  </div>
</div>
<form @submit.prevent="submitData" method="POST">  
<div class="card mb-5">
    <div class="card-body">
        <div class="d-flex flex-column">
            <div class="form-group">
                <label>Name</label>
                <input type="text" class="form-control" v-model="forms.nama" required="" aria-required="true">
                <div class="invalid-tooltip" v-for="error of errors['nama']">
                    {{ error }}
                </div> 
            </div>     
            <div class="form-group">
                <label>Nomor Aplikasi</label>
                <input type="text" class="form-control" v-model="forms.nomor_aplikasi" required="" aria-required="true" @keypress="isNumber($event)">
                <div class="invalid-tooltip" v-for="error of errors['nomor_aplikasi']">
                    {{ error }}
                </div> 
            </div>  
            <div class="form-group">
                <label>Jabatan</label>
                <select class="form-control" v-model="forms.jabatan" required="" aria-required="true">
                    <option v-for="status in jabatanDropdown" :value="status">{{ status }}</option>
                </select> 
                <div class="invalid-tooltip" v-for="error of errors['status']">
                    {{ error }}
                </div> 
            </div>  
            <div class="form-group">
                <label>Awal Masuk</label>
                <datepicker v-model="forms.awal_masuk" class="form-text"  :typeable="true" :format="customFormatter" required="" aria-required="true"></datepicker> 
                <div class="invalid-tooltip" v-for="error of errors['awal_masuk']">
                    {{ error }}
                </div> 
            </div>  
            <div class="form-group">
                <label>Cuti</label>
                 <input type="text" class="form-control" :maxlength="3" v-model="forms.cuti" required="" aria-required="true" @keypress="isNumber($event)">
                <div class="invalid-tooltip" v-for="error of errors['cuti']">
                    {{ error }}
                </div> 
            </div> 
            <div class="form-group">
                <label>Dokumen</label>
                <input type="file" name="file" id="file" v-on:change="uploadFile" class="form-text">
                <div class="invalid-tooltip" v-for="error of errors['file_name']">
                    {{ error }}
                </div> 
                <p class="center-block">* Type dokumen .pdf And Max 10 MB</p> 
                <br>
                <a v-bind:href="url+'/dokumen-karyawan/'+forms.dokumen" target="_blank"> <input class="submit btn btn-primary" type="button" value="Download"> </a>

            </div>     
            <br>
            <input class="submit btn btn-info" type="submit" value="Submit">
            <br>
            <button class="btn btn-warning pd-x-20" type="button" @click="backLink()">Back</button>
            
        </div>
    </div>
</div>
</form>
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
import moment from 'moment'
import Datepicker from 'vuejs-datepicker' 
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
    Datepicker,  

  },
  data () {
    return {
        url : window.webURL,
        file: '', 
        errors: [],
        option: {
            type: 'day',
            week: ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'],
            month: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            format: 'YYYY-MM-DD',
            placeholder: 'YYYY-MM-DD',
            inputStyle: {
            'display': 'inline-block',
            'padding': '6px',
            'line-height': '22px',
            'font-size': '16px',
            'border': '2px solid #fff',
            'box-shadow': '0 1px 3px 0 rgba(0, 0, 0, 0.2)',
            'border-radius': '2px',
            'color': '#5F5F5F'
            },
            color: {
            header: '#B799DA',
            headerText: '#f00'
            },
            buttons: {
            ok: 'Ok',
            cancel: 'Cancel'
            },
            overlayOpacity: 0.5, // 0.5 as default 
            dismissible: true // as true as default 
        },
        position: 'up right',
        closeBtn: true,
        maxToasts: 100,
        position: 'up right',
        closeBtn: true,  
        errors: [], 
        jabatanDropdown: ['Direktur','Wakil Direktur','Manager','Staff'],
        forms: new CrudForm({id:'', nama:'', nomor_aplikasi:'', jabatan:'', awal_masuk:'', cuti:'', dokumen:''}), 
        token: localStorage.getItem('token'), 

    }
  },
        watch: { 

        },
        methods: { 
            uploadFile(event) {
               let files = event.target.files || e.dataTransfer.files;
               if (files.length) this.forms.dokumen = files[0]; 
           },
            isNumber: function(evt) {
                evt = (evt) ? evt : window.event;
                var charCode = (evt.which) ? evt.which : evt.keyCode;
                if ((charCode > 31 && (charCode < 48 || charCode > 57)) && charCode !== 46) {
                    evt.preventDefault();;
                } else {
                    return true;
                }
            },
            backLink() {
			   this.$router.go(-1);
            } ,
            dataAction () {
                if(this.typenya === "edit-karyawan")
                {
                    this.forms.setFillItem(this.rowDatanya);
                }else{
                this.$router.push('/page-not-found');
                } 
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
                        let masuk = new FormData();
                        if(this.forms.dokumen == ''){
                            masuk.set('nama', this.forms.nama) 
                            masuk.set('nomor_aplikasi', this.forms.nomor_aplikasi) 
                            masuk.set('jabatan', this.forms.jabatan) 
                            masuk.set('awal_masuk', this.customFormatter(this.forms.awal_masuk))  
                            masuk.set('cuti', this.forms.cuti)    
                        }else{ 
                            masuk.set('nama', this.forms.nama) 
                            masuk.set('nomor_aplikasi', this.forms.nomor_aplikasi) 
                            masuk.set('jabatan', this.forms.jabatan) 
                            masuk.set('awal_masuk', this.customFormatter(this.forms.awal_masuk))  
                            masuk.set('cuti', this.forms.cuti)  
                            masuk.set('dokumen', this.forms.dokumen)  
                        }

                        axios.post('/hrd-karyawan-update/'+this.forms.id, masuk).then(response => {
                            if(!response.data){ 
                                window.location.href = window.webURL; 
                            }else{ 
                                if(response.data.status = 200){  
                                    this.errors = ''
                                    this.success(response.data.message);
                                }else{
                                    this.errors = response.data.message; 
                                }
                            }
                        }).catch(error => {
                            if (! _.isEmpty(error.response)) {
                                if (error.response.status = 422) {
                                    this.errors = error.response.data.errors;  
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
            
            customFormatter(date) {
                return moment(date).format('YYYY-MM-DD');
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
            this.errors=[]
          this.dataAction(); 
        }

}
</script>
<style> 
</style>
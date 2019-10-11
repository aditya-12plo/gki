<template>
    <div> 

<div class="breadcrumb"> 
  <div class="panel">
    <h1 class="animated fadeInLeft" align="center">KARYAWAN</h1>                
  </div>
</div>

<div class="row mb-4">
    <div class="col-md-12 mb-4">
        <div class="card text-left">
            <div class="card-body">
                <div class="table-responsive">
                    <div id="zero_configuration_table_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4">
                        
                     
                    <div class="row"> 
                        <div class="col-sm-12 col-md-6">
                            <div class="dataTables_length" id="zero_configuration_table_length">
                                <div class="row row-xs">
                                    <div class="col-md-6">
                                        <label>Date From :</label>
                                        <datepicker v-model="startTime.time"  :typeable="true" :format="customFormatter" placeholder="YYYY-MM-DD" @keyup.enter="doFilter"></datepicker>
                                    </div>
                                    <div class="col-md-6 mt-3 mt-md-0">
                                        <label>Date To : </label>
                                        <datepicker v-model="endtime.time"  :typeable="true" :format="customFormatter" placeholder="YYYY-MM-DD" @keyup.enter="doFilter"></datepicker>    
                                    </div> 
                                    <div class="col-md-12">
                                        <br>
                                        <label>Search for : </label>
                                        <input type="text" v-model="filterText" class="form-control form-control-sm" @keyup.enter="doFilter" placeholder="Nama / Nomor Aplikasi / Jabatan"> 
                                    </div>
                                    <div class="col-md-12">
                                        <br>
                                        <button type="button" class="btn btn-raised btn-raised-secondary m-1"  @click="importItem()"><i class="fa fa-upload"></i> Impor Data Karyawan</button>
                                        <button type="button" class="btn btn-raised btn-raised-info m-1"  @click="createItem()"><i class="fa fa-plus"></i> User</button>
                                        <button class="btn btn-raised btn-raised-success m-1" @click.prevent="doFilter">Search <i class="fa fa-thumbs-o-up position-right"></i></button>
                                        <button class="btn btn-raised btn-raised-warning m-1" @click.prevent="resetFilter">Reset Form <i class="fa fa-refresh position-right"></i></button>
                                        <button class="btn btn-raised btn-raised-danger m-1" @click.prevent="sumSelectedItems()">Delete <i class="fa fa-trash position-right"></i></button>
                                    </div>
                                </div> 
                            </div>
                        </div> 
                        <div class="col-sm-12 col-md-6">
                            <div id="zero_configuration_table_filter" class="dataTables_filter">
                                <label>Per Page: 
                                <select v-model="perPage" aria-controls="zero_configuration_table" class="form-control form-control-sm">
                                    <option :value=10>10</option>
                                    <option :value=25>25</option>
                                    <option :value=50>50</option>
                                    <option :value=75>75</option>
                                    <option :value=100>100</option>
                                </select>
                                </label>
                            </div>
                        </div>
                    </div>

                    <br>
                    <vuetable ref="vuetable"
                        api-url="/hrd-karyawan"
                        :fields="fields"
                        pagination-path=""
                        :per-page="perPage"
                        :css="css.table"
                        :append-params="moreParams" 
                        @vuetable:pagination-data="onPaginationData"
                        @vuetable:loading="onLoading"        
                        @vuetable:load-error="onLoadingError"        
                        @vuetable:load-success="onLoaded"
                    ></vuetable> 
                    <div class="vuetable-pagination">
                    <vuetable-pagination-info ref="paginationInfo"
                        info-class="pagination-info"
                    ></vuetable-pagination-info>
                    <vuetable-pagination ref="pagination"
                        :css="css.pagination"
                        @vuetable-pagination:change-page="onChangePage"
                    ></vuetable-pagination>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- MODAL -->
<div class="container-fluid display-page" id="display-post-category" >

<!-- @import --->
        <modal  v-if="modal.get('import')" @close="modal.set('import', false)"  >
        <template slot="header" ><h4>Import Data Karyawan</h4></template>
        <template slot="body" >

            <form method="POST" action="" @submit.prevent="importData()">
                <div class="modal-body">


 
<div class="form-group form-animate-text" style="margin-top:40px !important;">
    <input type="file" name="file" id="file" v-on:change="uploadFile" class="form-control" required="" aria-required="true"> 
    <p class="center-block">* Type dokumen .xlsx And Max 10 MB</p>
</div>   
  

<div class="form-group form-animate-text" style="margin-top:40px !important;">
 <button type="button" class="btn btn-primary" @click.prevent="downloadFile" > <i class="fa fa-download"></i> Download Themplate</button>
</div>  			 

<div class="form-group form-animate-text" style="margin-top:40px !important;">
    <div class="invalid-tooltip" v-for="error of errors['fileUpload']">
        {{ error }}
    </div>  
</div>  	

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" @click="modal.set('import', false)" >Close</button>
                    <button type="submit" class="btn btn-success">Impor</button>
                </div>
            </form>
        </template>
        </modal>
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

  /* CRUD MODAL */
        class CrudModal{
            constructor(data){
                this.modal = data;
            }
            get(value){
                if(this.modal[value]){
                    return this.modal[value];
                }
            }
            set(data , value){
                this.modal[data] = value;
            }
        }
        //  COMPONENT MODAL
        const Modal = {
            template: `   <transition name="modal">
                                <div class="modal-mask">
                                  <div class="modal-wrapper">
                                    <div :class="modalStyle">
                                    <a class="close-modal" @click="$emit('close')" ></a>
                                      <div class="modal-header">
                                           <p class="modal-card-title"><slot name="header" class="modal-card-title "></slot></p>
                                      </div>
                                        <slot name="body">
                                          default body
                                        </slot>
                                    </div>
                                  </div>
                                </div>
                              </transition>` ,
            props: {
                modalsize: {type: String},
            } ,
            computed: {
                modalStyle() {
                    return this.modalsize == null ? 'modal-container' : this.modalsize + ' modal-container';
                },
                created(){

                }
            }
        };
        
        
import Vue from 'vue'
import accounting from 'accounting'
import moment from 'moment'
import Datepicker from 'vuejs-datepicker' 
import VueEvents from 'vue-events'
import Hashids from 'hashids'
import {Vuetable, VuetablePagination, VuetablePaginationInfo} from 'vuetable-2'

import VueSweetalert2 from 'vue-sweetalert2'
import Loading from 'vue-loading-overlay'
import 'vue-loading-overlay/dist/vue-loading.css'

import AllActions from '../Buttons/AllActions.vue' 
Vue.component('all-actions', AllActions)
 

Vue.use(VueSweetalert2)
Vue.use(Loading)
Vue.use(VueEvents)


export default {
  components: {   
    Vuetable, 
    VuetablePagination,
    VuetablePaginationInfo,
    Datepicker,  
    'modal': Modal,
  },
  data () {
    return {  
        errors: new Errors() ,
        token: localStorage.getItem('token'), 
        modal:new CrudModal({import:false}),
        fileUpload:'',
	    isLoading: false,
        perPage: 10,
        startTime: {
            time: ''
        },
        endtime: {
            time: ''
        },
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
        submitSelectedItems:[],
        fields: [ 
            {
            name: '__sequence',
            title: 'No',
            titleClass: 'text-center',
            dataClass: 'text-center'
            }, 
            {
                name: 'nama',
                title: 'Name',
                titleClass: 'text-center',
                dataClass: 'text-center'
            },
            {
                name: 'nomor_aplikasi',
                title: 'Nomor Aplikasi',
                titleClass: 'text-center',
                dataClass: 'text-center'
            }, 
            {
                name: 'jabatan',
                title: 'Jabatan',
                titleClass: 'text-center',
                dataClass: 'text-center'
            }, 
            {
                name: 'awal_masuk',
                title: 'Tanggal Masuk',
                titleClass: 'text-center',
                dataClass: 'text-center',
                callback: 'formatDate|DD-MM-YYYY'
            }, 
            {
                name: '__component:all-actions',
                title: 'Actions',
                titleClass: 'text-center',
                dataClass: 'text-center'
            }
        ],
        filterText: '',
        css: {
            table: {
                tableClass: 'table table-bordered table-striped table-hover',
                ascendingIcon: 'glyphicon glyphicon-chevron-up',
                descendingIcon: 'glyphicon glyphicon-chevron-down'
            },
            pagination: {
                wrapperClass: 'pagination',
                activeClass: 'active',
                disabledClass: 'disabled',
                pageClass: 'page',
                linkClass: 'link',
                icons: {
                    first: '',
                    prev: '',
                    next: '',
                    last: '',
                },
            },
            icons: {
                first: 'glyphicon glyphicon-step-backward',
                prev: 'glyphicon glyphicon-chevron-left',
                next: 'glyphicon glyphicon-chevron-right',
                last: 'glyphicon glyphicon-step-forward',
            },
        },
        moreParams: {}
    }
  },
        watch: { 
            'perPage'(newValue, oldValue) {
               this.$events.fire('filter-set', this.filterText)
            }, 
        },
        methods: {

            importData() { 
                        let masuk = new FormData(); 
                        masuk.set('fileUpload', this.fileUpload)  
                        axios.post('/hrd-import-data-karyawan', masuk).then(response => {
                            if(!response.data){ 
                                window.location.href = window.webURL; 
                            }else{ 
                                if(response.data.status == 200){ 
                                    this.errors = '';
                                    this.fileUpload = '';  
                                    this.resetFilter(); 
                                    this.modal.set('import', false);
                                    this.success(response.data.message);
                                }else{
                                    this.errors = response.data.message 
                                }
                            }
                        }).catch(error => {
                            if (! _.isEmpty(error.response)) {
                                if (error.response.status = 422) {
                                   this.errors = error.response.data.errors
                                }else if (error.response.status = 500) {
                                    this.error(error.response.data.message); 
                                }else{
                                    this.$router.push('/page-not-found');
                                }
                            }
                                        
                        }) 
                    
            },

            uploadFile(event) {
               let files = event.target.files || e.dataTransfer.files;
               if (files.length) this.fileUpload = files[0]; 
           },

            downloadFile(){  
                axios({
                url: '/hrd-download-import-themplate',
                method: 'POST', 
                responseType: 'blob', // important
                }).then((response) => {
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'impor-data-karyawan.xlsx');
                document.body.appendChild(link);
                link.click();
                }); 
            },

            doFilter () {
        	    if(!this.startTime.time && !this.endtime.time){
                    this.$events.fire('filter-set', this.filterText, this.startTime.time, this.endtime.time )
                }else if(this.startTime.time && !this.endtime.time){
                    var startTime = this.customFormatter(this.startTime.time)
                    this.$events.fire('filter-set', this.filterText, startTime, this.endtime.time )
                }else if(!this.startTime.time && this.endtime.time){
                    var endtime = this.customFormatter(this.endtime.time)
                    this.$events.fire('filter-set', this.filterText, this.startTime.time, endtime)
                }else if(this.startTime.time && this.endtime.time){ 
                    if(this.endtime.time < this.startTime.time)
                        {
                            alert('Input Date Wrong');
                        }else{
                            var startTime = this.customFormatter(this.startTime.time)
                            var endtime = this.customFormatter(this.endtime.time)
                            this.$events.fire('filter-set', this.filterText, startTime, endtime )
                        }
                }else{
                    this.$events.fire('filter-set', this.filterText, this.startTime.time, this.endtime.time )
                }
            },
            diacak(id){
                var hashids = new Hashids('',1000,'abcdefghijklmnopqrstuvwxyz0987654321ABCDEFGHIJKLMNOPQRSTUVWXYZ'); // no padding
                return hashids.encode(id); 
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
             
           deleteItem(item ,index = this.indexOf(item)){
                this.$swal({
                    title: 'Are you sure ?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes!'
                }).then((result) => {
                    if (result.value) {
                        axios.delete('/hrd-karyawan/'+ item.id)
                        .then(response => {
                            if(!response.data){ 
                                window.location.href = window.webURL; 
                            }else{
                                if(response.data.status == 200){ 
                                            this.errorNya = '';
                                            this.resetFilter();
                                            this.success(response.data.message);
                                }else{
                                            this.resultError(response.data.message)
                                } 
                            }
                        }).catch(error => {
                            if (! _.isEmpty(error.response)) {
                                if (error.response.status = 422) {
                                   this.resultError(error.response.message)
                                }else if (error.response.status = 500) {
                                    this.$router.push('/server-error');
                                }else{
                                    this.$router.push('/page-not-found');
                                }
                            }
                        });
                    }
                }) 
            }  ,
            editItem(item ,index = this.indexOf(item)){
                this.$router.push({name:'HrdKaryawanEdit', params: {id: this.diacak(item.id),typenya:'edit-karyawan',rowDatanya:item }});
            },
            importItem() {
                this.file= '';
                this.modal.set('import', true);
            } ,
            createItem() {
                this.$router.push({name:'HrdKaryawanAdd', params: {typenya:'add-karyawan'}});
            } ,
            viewItem(item ,index = this.indexOf(item)){
                this.$router.push({name:'HrdKaryawanDetail', params: {id: this.diacak(item.id),typenya:'detail-karyawan',rowDatanya:item }});
            } ,
            formatDate (value, fmt = 'DD-MM-YYYY HH:mm:ss') {
                return (value == null)
                    ? ''
                    : moment(value, 'YYYY-MM-DD HH:mm:ss').format(fmt)
            },
            onChangePage (page) {
                 this.$refs.vuetable.changePage(page)
            },
            onPaginationData (paginationData) {
                this.$refs.pagination.setPaginationData(paginationData)
                this.$refs.paginationInfo.setPaginationData(paginationData)
            },
            onLoading() {
                this.loading();
                this.isLoading = true;
            },
            onLoaded() {
            this.isLoading = false;
            },
            onLoadingError() {
               this.isLoading = true;
               axios.get('hrd-karyawan').then((response) => {
                   if(!response.data){ 
                        window.location.href = window.webURL; 
                    }else{ 
                        this.isLoading = false;
                    }
                }).catch(error => {
                    if (! _.isEmpty(error.response)) {
                        if (error.response.status = 500) {
                            this.$router.push('/server-error');
                        }else{
                            this.isLoading = false;
                        }
                    }
                });
            },	

            customFormatter(date) {
                return moment(date).format('YYYY-MM-DD');
            },

            resetFilter () {
                this.filterText = ''
                this.startTime.time = ''
                this.endtime.time = ''
                this.$events.fire('filter-reset')
            },

            fetchIt(){
                this.loading();
                axios.get('/user/get-hrd').then((response) => {
                    if(!response.data){ 
                        window.location.href = window.webURL; 
                    }else{ 
                        if(response.data.status != 200){ 
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

            success(kata) {
                this.$swal({
                    position: 'top-end',
                    type: 'success',
                    title: kata,
                    showConfirmButton: false,
                    timer: 1500
                })
            },
            question(kata) {
                this.$swal('Ooppss?',kata,'question');
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
            'filter-set' (filterText,startTime,endtime) {
                this.moreParams = {
                filter: filterText,min: startTime, max: endtime
                }
                Vue.nextTick(() => this.$refs.vuetable.refresh() )
            },
            'filter-reset' () {
                this.moreParams = {}
                Vue.nextTick(() => this.$refs.vuetable.refresh() )
            } 
        },
        created: function() { 
            let self = this;
            this.$root.$on('viewitem', function(data,index){
                //console.log(data);
               self.viewItem(data,index);
            });
            this.$root.$on('edititem', function(data,index){
               self.editItem(data,index);
            });
            this.$root.$on('deleteitem', function(data,index){
               self.deleteItem(data,index);
            });
        },
		mounted() { 
            this.fetchIt(); 
        }

}
</script>
<style>
.pagination {
  margin: 0;
  float: right;
}
.pagination a.page {
  border: 1px solid lightgray;
  border-radius: 3px;
  padding: 5px 10px;
  margin-right: 2px;
}
.pagination a.page.active {
  color: white;
  background-color: #337ab7;
  border: 1px solid lightgray;
  border-radius: 3px;
  padding: 5px 10px;
  margin-right: 2px;
}
.pagination a.btn-nav {
  border: 1px solid lightgray;
  border-radius: 3px;
  padding: 5px 7px;
  margin-right: 2px;
}
.pagination a.btn-nav.disabled {
  color: lightgray;
  border: 1px solid lightgray;
  border-radius: 3px;
  padding: 5px 7px;
  margin-right: 2px;
  cursor: not-allowed;
}
.pagination-info {
  float: left;
} 
.modal-mask {
  position: fixed;
  z-index: 9998;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, .5);
  display: table;
  transition: opacity .3s ease;
}
.modal-wrapper {
  display: table-cell;
  vertical-align: middle;
}
.modal-container {
  width: 80%;
  margin: 0px auto;
  padding: 20px 30px;
  background-color: #fff;
  border-radius: 2px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
  transition: all .3s ease;
  font-family: Helvetica, Arial, sans-serif;
}
.modal-header h3 {
  margin-top: 0;
  color: #42b983;
}
.modal-body {
  margin: 20px 0;
     max-height: calc(100vh - 210px);
    overflow-y: auto;
}
.modal-default-button {
  float: right;
}
/*
 * The following styles are auto-applied to elements with
 * transition="modal" when their visibility is toggled
 * by Vue.js.
 *
 * You can easily play with the modal transition by editing
 * these styles.
 */
.modal-enter {
  opacity: 0;
}
.modal-leave-active {
  opacity: 0;
}
.modal-enter .modal-container,
.modal-leave-active .modal-container {
  -webkit-transform: scale(1.1);
  transform: scale(1.1);
}
</style>
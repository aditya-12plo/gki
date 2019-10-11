<template>
  <div> 

<div class="breadcrumb"> 
  <div class="panel">
    <h1 class="animated fadeInLeft" align="center">Dokumen DTTOT</h1>                
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
                                        <input type="text" v-model="filterText" class="form-control form-control-sm" @keyup.enter="doFilter" placeholder="Nama Dokumen"> 
                                    </div>
                                    <div class="col-md-12">
                                        <br> 
                                        <button type="button" class="btn btn-raised btn-raised-info m-1"  @click="createItem()"><i class="fa fa-plus"></i> Dokumen</button>
                                        <button class="btn btn-raised btn-raised-success m-1" @click.prevent="doFilter">Search <i class="fa fa-thumbs-o-up position-right"></i></button>
                                        <button class="btn btn-raised btn-raised-warning m-1" @click.prevent="resetFilter">Reset Form <i class="fa fa-refresh position-right"></i></button> 
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
                        api-url="/ukk-dttot"
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
 <!-- @create Modal--->
        <modal  v-if="modal.get('create')" @close="modal.set('create', false)" >
        <template slot="header" ><h4 align="center">Form Penambahan Dokumen DTTOT</h4></template>
        <template slot="body" >
            <form method="POST" action="" @submit.prevent="submitData()">
                <div class="modal-body">

                  <!-- form Group -->
<div class="form-element" style="padding-bottom:50px;">
    <div class="col-md-12 padding-0">


    
<div class="form-group form-animate-text" style="margin-top:40px !important;">
    <label>Tanggal Update</label>
    <datepicker v-model="forms.tanggal_update" class="form-control"  :typeable="true" :format="customFormatter" required="" aria-required="true"></datepicker>
    <div class="invalid-tooltip" v-for="error of errors['tanggal_update']">
        {{ error }}
    </div> 
</div>    
 
    
<div class="form-group form-animate-text" style="margin-top:40px !important;">
    <label>Nama Dokumen</label> 
    <input type="text" class="form-control" :maxlength="255" v-model="forms.dokumen_name" required="" aria-required="true">
    <div class="invalid-tooltip" v-for="error of errors['dokumen_name']">
        {{ error }}
    </div> 
</div>  

<div class="form-group" style="margin-top:40px !important;">
    <label>Nama Dokumen</label>
    <input type="file" name="file" id="file" v-on:change="uploadFile" class="form-text" required="" aria-required="true"> 
    <div class="invalid-tooltip" v-for="error of errors['file_name']">
        {{ error }}
    </div> 
    <p class="center-block">* Type dokumen .pdf And Max 10 MB</p>
</div>    


    </div>
</div>   

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" @click="modal.set('create', false)" >Close</button>
                    <button type="submit" class="btn btn-success">Create</button>
                </div>

            </form>
        </template>
        </modal>

 
        
<!-- @update --->
        <modal  v-if="modal.get('edit')" @close="modal.set('edit', false)"  >
        <template slot="header" ><h4>Edit Role</h4></template>
        <template slot="body" >

            <form method="POST" action="" @submit.prevent="updateData()">
                <div class="modal-body">


                   <!-- form Group -->
<div class="form-element" style="padding-bottom:50px;">
    <div class="col-md-12 padding-0">



    
<div class="form-group form-animate-text" style="margin-top:40px !important;">
    <datepicker v-model="forms.tanggal_update" class="form-text"  :typeable="true" :format="customFormatter" required="" aria-required="true"></datepicker>
    <span class="bar"></span>
    <label>Tanggal Update</label>
    <span class="label label-danger" v-for="error of errors['tanggal_update']">
        {{ error }}
    </span>
</div>    
 
    
<div class="form-group form-animate-text" style="margin-top:40px !important;">
    <input type="text" class="form-text" :maxlength="255" v-model="forms.dokumen_name" required="" aria-required="true">
    <span class="bar"></span>
    <label>Nama Dokumen</label>
    <span class="label label-danger" v-for="error of errors['dokumen_name']">
        {{ error }}
    </span>
</div>  

<div class="form-group" style="margin-top:40px !important;">
    <label>Nama Dokumen</label> 
    <input type="file" name="file" id="file" v-on:change="uploadFile" class="form-text"> 
    <br>
    <a v-bind:href="url+forms.dokumen" target="_blank"> <input class="submit btn btn-primary" type="button" value="Download"> </a>
    <p class="center-block">* Type dokumen .pdf And Max 10 MB</p>    
    <span class="label label-danger" v-for="error of errors['file_name']">
        {{ error }}
    </span>
</div>    


    </div>
</div>  				 


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" @click="modal.set('edit', false)" >Close</button>
                    <button type="submit" class="btn btn-success">Edit</button>
                </div>
            </form>
        </template>
        </modal>


</div>
<!-- MODAL -->

</div>
</template>

<script>

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

import dttotActions from '../Buttons/dttotActions.vue'  
 

Vue.component('dttot-actions', dttotActions)

Vue.use(VueSweetalert2)
Vue.use(Loading)
Vue.use(VueEvents)
 
        /* CRUD FORM */
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
        };
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
        };
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
        url:'/public/dokumen-dttot/',
        file:'',
        errors: {} ,
        token: localStorage.getItem('token'), 
        forms:new CrudForm({index:'',  id:'' , tanggal_update:'', dokumen_name:'', dokumen:'', created_at:''}),
        modal:new CrudModal({create:false , edit:false}),
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
                name: 'dokumen_name',
                title: 'Nama Dokumen',
                titleClass: 'text-center',
                dataClass: 'text-center'
            }, 
            {
                name: 'tanggal_update',
                title: 'Tanggal Update',
                titleClass: 'text-center',
                dataClass: 'text-center',
                callback: 'formatDate|DD-MM-YYYY'
            }, 
            {
                name: 'status',
                title: 'Status',
                titleClass: 'text-center',
                dataClass: 'text-center'
            }, 
            {
                name: 'remarks',
                title: 'Remarks',
                titleClass: 'text-center',
                dataClass: 'text-center'
            }, 
            {
                name: '__component:dttot-actions',
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
            uploadFile(event) {
               let files = event.target.files || e.dataTransfer.files;
               if (files.length) this.forms.dokumen = files[0]; 
           },

            updateData() { 
                let masuk = new FormData();
                    if(this.forms.dokumen == ''){
                        masuk.set('tanggal_update', this.customFormatter(this.forms.tanggal_update)) 
                        masuk.set('dokumen_name', this.forms.dokumen_name) 
                    }else{ 
                        masuk.set('tanggal_update', this.customFormatter(this.forms.tanggal_update)) 
                        masuk.set('dokumen_name', this.forms.dokumen_name)
                        masuk.set('dokumen', this.forms.dokumen)  
                    }
                axios.post('/ukk-dttot-update/'+this.forms.id, masuk).then(response => {
                    if(!response.data){ 
                        window.location.href = window.webURL; 
                    }else{ 
                         if(response.data.status == 200){ 
                            this.resetForms(); 
                            this.modal.set('edit', false);
                            this.resetFilter(); 
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
            },

            submitData() { 
                let masuk = new FormData();
                        masuk.set('tanggal_update', this.customFormatter(this.forms.tanggal_update)) 
                        masuk.set('dokumen_name', this.forms.dokumen_name)
                        masuk.set('dokumen', this.forms.dokumen)  

                axios.post('/ukk-dttot', masuk).then(response => {
                    if(!response.data){ 
                        window.location.href = window.webURL; 
                    }else{ 
                         if(response.data.status == 200){ 
                            this.resetForms(); 
                            this.modal.set('create', false);
                            this.resetFilter(); 
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
                        axios.delete('/ukk-dttot/'+ item.id)
                        .then(response => {
                            if(!response.data){ 
                                window.location.href = window.webURL; 
                            }else{
                                if(response.data.status == 200){ 
                                            this.resetForms(); 
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

            downloadPdfItem(item ,index = this.indexOf(item)){
                var masuk = 
                {
                    'id' : item.id, 
                    'filename' : item.dokumen, 
                }
                axios({
                url: '/ukk-dttot-download-file-pdf',
                method: 'POST',
                data: masuk,
                responseType: 'blob', // important
                }).then((response) => {
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', item.dokumen);
                document.body.appendChild(link);
                link.click();
                }); 
            },

            downloadItem(item ,index = this.indexOf(item)){
                if(item.status == 'complete'){ 
                    var masuk = 
                    {
                        'id' : item.id, 
                        'filename' : item.dokumen_name, 
                    }
                    axios({
                    url: '/ukk-dttot-download-excel',
                    method: 'POST',
                    data: masuk,
                    responseType: 'blob', // important
                    }).then((response) => {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', item.dokumen_name+'.xls');
                    document.body.appendChild(link);
                    link.click();
                    }); 
                }else{
                    this.error('document status must be complete');
                }
            },

            
            downloadNonPeroranganItem(item ,index = this.indexOf(item)){
                if(item.status == 'complete'){ 
                    var masuk = 
                    {
                        'id' : item.id, 
                        'filename' : item.dokumen_name, 
                    }
                    axios({
                    url: '/ukk-dttot-download-excel-non-perorangan',
                    method: 'POST',
                    data: masuk,
                    responseType: 'blob', // important
                    }).then((response) => {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', item.dokumen_name+' Non Perorangan.xls');
                    document.body.appendChild(link);
                    link.click();
                    });
                }else{
                    this.error('document status must be complete');
                } 
            },

            downloadPeroranganItem(item ,index = this.indexOf(item)){
                if(item.status == 'complete'){ 
                    var masuk = 
                    {
                        'id' : item.id, 
                        'filename' : item.dokumen_name, 
                    }
                    axios({
                    url: '/ukk-dttot-download-excel-perorangan',
                    method: 'POST',
                    data: masuk,
                    responseType: 'blob', // important
                    }).then((response) => {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', item.dokumen_name+' Perorangan.xls');
                    document.body.appendChild(link);
                    link.click();
                    });
                }else{
                    this.error('document status must be complete');
                } 
            },

            downloadKaryawanItem(item ,index = this.indexOf(item)){
                if(item.status == 'complete'){
                    var masuk = 
                    {
                        'id' : item.id, 
                        'filename' : item.dokumen_name, 
                    }
                    axios({
                    url: '/ukk-dttot-download-excel-karyawan',
                    method: 'POST',
                    data: masuk,
                    responseType: 'blob', // important
                    }).then((response) => {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', item.dokumen_name+'.xls');
                    document.body.appendChild(link);
                    link.click();
                    });
                }else{
                    this.error('document status must be complete');
                } 
            },

            processItem(item ,index = this.indexOf(item)){
                 this.$swal({
                    title: 'Are you sure ?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes!'
                }).then((result) => {
                    if (result.value) {
                        axios.get('/ukk-dttot-extract-file/'+ item.id+'/'+item.dokumen)
                        .then(response => {
                            if(!response.data){ 
                                window.location.href = window.webURL; 
                            }else{
                                if(response.data.status == 200){ 
                                            this.resetForms(); 
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
             },
            editItem(item ,index = this.indexOf(item)){
                this.forms.setFillItem(item , index );
                this.modal.set('edit', true);
             },
            createItem() {
                this.resetForms();
                this.modal.set('create', true);
            } ,  
            formatDate (value, fmt = 'DD-MM-YYYY') {
                return (value == null)
                    ? ''
                    : moment(value, 'YYYY-MM-DD').format(fmt)
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
               axios.get('/ukk-dttot').then((response) => {
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

            resetForms () { 
         		this.forms.reset();
                window.errors.clearAll();
            },

            resetFilter () {
                this.filterText = ''
                this.startTime.time = ''
                this.endtime.time = ''
                this.$events.fire('filter-reset')
            },

            fetchIt(){
                this.loading();
                axios.get('/user/get-ukk').then((response) => {
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
            this.$root.$on('downloadnonperoranganitem', function(data,index){
               self.downloadNonPeroranganItem(data,index);
            });
            this.$root.$on('downloadperoranganitem', function(data,index){
               self.downloadPeroranganItem(data,index);
            });
            this.$root.$on('downloadkaryawanitem', function(data,index){
               self.downloadKaryawanItem(data,index);
            });
            this.$root.$on('downloaditem', function(data,index){
               self.downloadItem(data,index);
            });
            this.$root.$on('downloadpdfitem', function(data,index){
               self.downloadPdfItem(data,index);
            });
            this.$root.$on('processitem', function(data,index){
               self.processItem(data,index);
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
.modal-backdrop {
z-index: -1;
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
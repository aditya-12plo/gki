<template>
  <div> 

<div class="breadcrumb"> 
  <div class="panel">
    <h1 class="animated fadeInLeft" align="center">LIST DIVISI KARYAWAN</h1>                
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
                                            <input type="text" v-model="filterText" class="form-control form-control-sm" @keyup.enter="doFilter" placeholder="Nama Divisi"> 
                                        </div>
                                        <div class="col-md-12">
                                            <br>
                                            <button type="button" class="btn btn-raised btn-raised-info m-1"  @click="createItem()"><i class="fa fa-plus"></i> Divisi</button>
                                            <button type="button" class="btn btn-raised btn-raised-primary m-1"  @click="printItem()"><i class="fa fa-print"></i> Print</button>
                                            <button type="button" class="btn btn-raised btn-raised-secondary m-1"  @click="downloadItem()"><i class="fa fa-file-excel-o"></i> Download</button>
                                            <button class="btn btn-raised btn-raised-success m-1" @click.prevent="doFilter"><i class="fa fa-thumbs-o-up position-right"></i> Search </button>
                                            <button class="btn btn-raised btn-raised-warning m-1" @click.prevent="resetFilter"><i class="fa fa-refresh position-right"></i> Reset Form </button>
                                            <button class="btn btn-raised btn-raised-danger m-1" @click.prevent="sumSelectedItems()"><i class="fa fa-trash position-right"></i> Delete </button>
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
                        api-url="/divisi"
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
        <template slot="header" ><h4 align="center">Penambahan Divisi</h4></template>
        <template slot="body" >
            <form method="POST" action="" @submit.prevent="submitData()">
                <div class="modal-body">
 

<div class="form-group form-animate-text" style="margin-top:40px !important;">
    <label>Nama Divisi</label>
    <input type="text" class="form-control" v-model="forms.divisi" required="" aria-required="true">
    <div class="invalid-tooltip" v-for="error of errors['divisi']">
        {{ error }}
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


<!-- @view --->
        <modal  v-if="modal.get('view')" @close="modal.set('view', false)"  >
        <template slot="header" ><h4 align="center">Detail Divisi</h4></template>
        <template slot="body" >
                <div class="modal-body">
 

<div class="form-group form-animate-text" style="margin-top:40px !important;">
    <label>Nama Divisi</label>
    <h5 class="media-heading"><b>{{forms.divisi}}</b></h5>  
</div>    

<div class="form-group form-animate-text" style="margin-top:40px !important;">
    <label>Created At</label>
    <h5 class="media-heading"><b>{{this.formatDate(forms.created_at)}}</b></h5> 
</div>    

    

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" @click="modal.set('view', false)" >Close</button>
                </div>
        </template>
        </modal>

        
<!-- @update --->
        <modal  v-if="modal.get('edit')" @close="modal.set('edit', false)"  >
        <template slot="header" ><h4>Edit Divisi</h4></template>
        <template slot="body" >

            <form method="POST" action="" @submit.prevent="updateData()">
                <div class="modal-body">
 

<div class="form-group form-animate-text" style="margin-top:40px !important;">
    <label>Role Name</label>
    <input type="text" class="form-control" v-model="forms.divisi" required="" aria-required="true">
    <div class="invalid-tooltip" v-for="error of errors['divisi']">
        {{ error }}
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
    'all-actions': AllActions,
  },
  data () {
    return { 
        errors: [],
        token: localStorage.getItem('token'), 
        forms:new CrudForm({index:'',  id:'' , divisi:'', created_at:''}),
        modal:new CrudModal({create:false , view:false  , edit:false}),
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
            name: '__checkbox:id',
            titleClass: 'text-center',
            dataClass: 'text-center',
            },
            {
                name: 'divisi',
                title: 'Nama Divisi',
                titleClass: 'text-center',
                dataClass: 'text-center'
            }, 
            {
                name: 'created_at',
                title: 'Created At',
                titleClass: 'text-center',
                dataClass: 'text-center',
                callback: 'formatDate|DD-MM-YYYY HH:mm:ss'
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

            updateData() { 
                axios.put('/divisi/'+this.forms.id, this.forms).then(response => {
                    if(!response.data){ 
                        window.location.href = window.webURL; 
                    }else{ 
                         if(response.data.status == 200){  
                            this.resetForms();
                            this.modal.set('edit', false);
                            this.resetFilter(); 
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
            },

            submitData() { 
                axios.post('/divisi', this.forms).then(response => {
                    if(!response.data){ 
                        window.location.href = window.webURL; 
                    }else{ 
                         if(response.data.status == 200){   
                            this.resetForms();
                            this.modal.set('create', false);
                            this.resetFilter(); 
                            this.success(response.data.message);
                        }else{
                            this.errors = response.data.message;
                            // this.resultError(response.data.message)
                        }
                    }
                }).catch(error => {
                    if (! _.isEmpty(error.response)) {
                        if (error.response.status = 422) {
                            this.errors = error.response.data.errors;
                            // this.resultError(error.response.data.errors)
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
            sumSelectedItems() {
                var ttl = this.$refs.vuetable.selectedTo;
                if(ttl.length <= 0)
                {
                    this.question('Choose data for delete');
                }else{
                    this.$swal({
                        title: 'Are you sure ?',
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes!'
                    }).then((result) => {
                        if (result.value) {
                            var join_selected_values = ttl.join(","); 
                                axios.delete('/divisi/delete-all/'+ join_selected_values)
                                .then(response => {
                                    if(!response.data){ 
                                        window.location.href = window.webURL; 
                                    }else{
                                        if(response.data.status == 200){  
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
                                });
                        }
                    })
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
                        axios.delete('/divisi/'+ item.id)
                        .then(response => {
                            if(!response.data){ 
                                window.location.href = window.webURL; 
                            }else{
                                if(response.data.status == 200){  
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
                        });
                    }
                }) 
            }  ,
            editItem(item ,index = this.indexOf(item)){
                this.errors=[]
                this.forms.setFillItem(item , index );
                this.modal.set('edit', true);
             },
            printItem(){
               this.isLoading = true;
               axios.get('/print-divisi?filter='+this.filterText+'&min='+this.startTime.time+'&max='+this.endtime.time).then((response) => {
                   if(!response.data){ 
                        window.location.href = window.webURL; 
                    }else{ 
                        this.isLoading = false;
                        this.$router.push({name:'Print', params: {datas:response.data }});
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
            } ,
            downloadItem() {
               this.isLoading = true;
                var masuk = {
                        'filter' : this.filterText, 
                        'min' : this.startTime.time, 
                        'max' : this.endtime.time,
                        'filename':'download-divisi.xls'
                    }
                    axios({
                    url: '/download-excel-divisi',
                    method: 'POST',
                    data: masuk,
                    responseType: 'blob', // important
                    }).then((response) => {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'download-divisi.xls');
                    document.body.appendChild(link);
                    link.click();
                    }); 
            } ,
            createItem() {
                this.errors=[]
                this.resetForms();
                this.modal.set('create', true);
            } ,
            viewItem(item ,index = this.indexOf(item)){
                this.errors=[]
                this.forms.setFillItem(item , index );
                this.modal.set('view', true);
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
               axios.get('/divisi').then((response) => {
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
                axios.get('/user/get-profile').then((response) => {
                    if(!response.data){ 
                        window.location.href = window.webURL; 
                    }else{
                        if(response.data.status == 200){
                            if(response.data.data.role.role_code == 'hrd' || response.data.data.role.role_code == 'dirut' || response.data.data.role.role_code == 'root'){
                                console.log(true);
                            }else{
                                window.location.href = window.webURL; 
                            } 
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
<template>
  <div> 


<div class="breadcrumb"> 
  <div class="panel">
    <h1 class="animated fadeInLeft" align="center">USERS</h1>                
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
                                        <input type="text" v-model="filterText" class="form-control form-control-sm" @keyup.enter="doFilter" placeholder="Email / Name"> 
                                    </div>
                                    <div class="col-md-12">
                                        <br>
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
                        api-url="/users"
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
  },
  data () {
    return {  
        errors: [], 
        token: localStorage.getItem('token'), 

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
                name: 'name',
                title: 'Name',
                titleClass: 'text-center',
                dataClass: 'text-center'
            },
            {
                name: 'email',
                title: 'Email',
                titleClass: 'text-center',
                dataClass: 'text-center'
            }, 
            {
                name: 'role.role_name',
                title: 'Role',
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
                                axios.delete('/user/delete-all/'+ join_selected_values)
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
                        axios.delete('/users/'+ item.id)
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
                this.$router.push({name:'useredit', params: {id: this.diacak(item.id),typenya:'edit-user',rowDatanya:item }});
            },
            createItem() {
                this.$router.push({name:'useradd', params: {typenya:'add-user'}});
            } ,
            viewItem(item ,index = this.indexOf(item)){
                this.$router.push({name:'userdetail', params: {id: this.diacak(item.id),typenya:'detail-user',rowDatanya:item }});
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
               axios.get('users').then((response) => {
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
                axios.get('/user/get-super-admin').then((response) => {
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
</style>
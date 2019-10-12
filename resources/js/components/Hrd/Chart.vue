<template>
    <div>

<div class="breadcrumb"> 
  <div class="panel">
    <h1 class="animated fadeInLeft" align="center">CHART DATA KARYAWAN</h1>                
  </div>
</div>

<div class="row">

    <div class="col-lg-8 col-md-12">
        <div class="card mb-4">
            <div class="card-body">
                <div class="card-title">By Divisi</div>
                
                <KaryawanByDivisi width="1000" height="300"
                    type="bar"
                    id="chart1"
                    title="# Divisi"
                    :border-color="'rgba(0, 0, 0, 0.1)'"
                ></KaryawanByDivisi>

            </div>
        </div>
    </div>

    <div class="col-lg-4 col-sm-12">
        <div class="card mb-4">
            <div class="card-body">
                <div class="card-title">by Jabatan</div>
 
                <KaryawanByJabatan width="1000" height="300"
                    type="pie"
                    id="chart2"
                    title="# Jabatan"
                    :border-color="'rgba(153, 102, 255, 1)'"
                ></KaryawanByJabatan>


            </div>
        </div>
    </div>

</div>


    </div>
</template>

<script>
   
import Vue from 'vue' 
import VueSweetalert2 from 'vue-sweetalert2'
import moment from 'moment'
import VueEvents from 'vue-events'
import Loading from 'vue-loading-overlay'
import 'vue-loading-overlay/dist/vue-loading.css'

import KaryawanByJabatan from '../Charts/KaryawanByJabatan.vue'
import KaryawanByDivisi from '../Charts/KaryawanByDivisi.vue'


Vue.use(VueSweetalert2)
Vue.use(Loading)
Vue.use(VueEvents)
Vue.component('KaryawanByJabatan', KaryawanByJabatan)
Vue.component('KaryawanByDivisi', KaryawanByDivisi)
 

export default {
  components: { 
    
  },
  data () {
    return { 
      isLoading: false,
      dataChart:'',
      chartByJabatanColumns:[],
      chartByJabatanData:[],
      user:{}
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

    resultError(data) {  
      var count = Object.keys(data).length;
      for(var x=0; x < count;x++){ 
        var nameOb      = Object.keys(data)[x];
        var objectData  = data[nameOb];
        for(var y=0; y < objectData.length;y++){ 
          this.error(objectData[y]);
        }
      }
    },
  },
  events: { 

  },
  created: function() { 
    let self = this;
  },
	mounted(){ 
    this.fetchIt(); 
  }

}
</script>
<style>
  .small {
    max-width: 100%;
    margin:  5% auto;
  }
</style>
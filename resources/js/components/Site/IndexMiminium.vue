<template>
  <div> 

    <div class="panel" v-if="this.user.role.role_code == 'dirut'">
      <div class="panel-body">
        <div class="col-md-12 col-sm-12">
          <h1 class="animated fadeInLeft" align="center">MENU DIREKTUR UTAMA</h1>  
        </div>
      </div>                    
    </div> 

    <div class="panel" v-if="this.user.role.role_code == 'dirkep'">
      <div class="panel-body">
        <div class="col-md-12 col-sm-12">
          <h1 class="animated fadeInLeft" align="center">MENU DIREKTUR KEPATUHAN</h1>  
        </div>
      </div>                    
    </div> 

    <div class="panel" v-if="this.user.role.role_code == 'operasional'">
      <div class="panel-body">
        <div class="col-md-12 col-sm-12">
          <h1 class="animated fadeInLeft" align="center">MENU OPERASIONAL</h1>  
        </div>
      </div>                    
    </div> 

    <div class="panel" v-if="this.user.role.role_code == 'root'">
      <div class="panel-body">
        <div class="col-md-12 col-sm-12">
          <h1 class="animated fadeInLeft" align="center">MENU ADMINSTRATOR</h1>  
        </div>
      </div>                    
    </div> 


    <div class="panel" v-if="this.user.role.role_code == 'hrd'">
      <div class="panel-body">
        <div class="col-md-12 col-sm-12">
          <h1 class="animated fadeInLeft" align="center">MENU HRD</h1>  
        </div>
      </div>                    
    </div> 


    <div class="panel" v-if="this.user.role.role_code == 'ukk'">
      <div class="panel-body">
        <div class="col-md-12 col-sm-12">
          <h1 class="animated fadeInLeft" align="center">MENU UNIT KERJA KHUSUS</h1>  
        </div>
      </div>                    
    </div> 

    <div class="col-md-12" style="padding:20px;">
      <div class="col-md-12 padding-0">
        <div class="panel box-v3"> 


<!-- dirkep menu -->
<div class="panel-body" v-if="this.user.role.role_code == 'dirkep'">
                 
<div class="col-md-4" style="background-color: #918C8C !important;">
<a href="/ukk-view#/ukk-peps">
  <div class="panel box-v1" style="background-color: #27C24C !important;">
        <div class="panel-body text-center">
           <h1 style="color: #FFFFFF !important;">UKK</h1>
          <hr>
        </div>
  </div>
</a>
</div>


</div>


<!-- dirut menu -->
<div class="panel-body" v-if="this.user.role.role_code == 'dirut'">

   <div class="col-md-4" style="background-color: #FFFFFF !important;">
    <a href="/operasional-view#/ops-perorangan">
      <div class="panel box-v1" style="background-color: #2196F3 !important;">
        <div class="panel-body text-center">
           <h1 style="color: #FFFFFF !important;">Profil Orang Perorangan</h1>
          <hr>
        </div>
      </div>
    </a>
  </div>

  <div class="col-md-4" style="background-color: #FFFFFF !important;">
    <a href="/operasional-view#/ops-non-perorangan">
      <div class="panel box-v1" style="background-color: #2196F3 !important;">
        <div class="panel-body text-center">
           <h1 style="color: #FFFFFF !important;">Profil Non Perorangan</h1>
          <hr>
        </div>
      </div>
    </a>
  </div>

  <div class="col-md-4" style="background-color: #FFFFFF !important;">
    <a href="/hrd-view#/hrd-list-karyawan">
      <div class="panel box-v1" style="background-color: #2196F3 !important;">
        <div class="panel-body text-center">
           <h1 style="color: #FFFFFF !important;">Rekap Data Karyawan</h1>
          <hr>
        </div>
      </div>
    </a>
  </div> 

</div>


<!-- ukk menu -->
<div class="panel-body" v-if="this.user.role.role_code == 'ukk'">

 
  <div class="col-md-4" style="background-color: #FFFFFF !important;">
    <a href="/ukk-view#/ukk-peps">
      <div class="panel box-v1" style="background-color: #2196F3 !important;">
        <div class="panel-body text-center">
            <h1 style="color: #FFFFFF !important;">Dokumen PEPS</h1>
            <hr>
          </div>
      </div>
    </a>
  </div>

  <div class="col-md-4" style="background-color: #FFFFFF !important;">
    <a href="/ukk-view#/ukk-dttot">
      <div class="panel box-v1" style="background-color: #2196F3 !important;">
        <div class="panel-body text-center">
           <h1 style="color: #FFFFFF !important;">Dokumen DTTOT</h1>
          <hr>
        </div>
      </div>
    </a>
  </div>

</div>


<!-- operasional menu -->
<div class="panel-body" v-if="this.user.role.role_code == 'operasional'">

  <div class="col-md-4" style="background-color: #FFFFFF !important;">
    <a href="/operasional-view#/ops-perorangan">
      <div class="panel box-v1" style="background-color: #2196F3 !important;">
        <div class="panel-body text-center">
           <h1 style="color: #FFFFFF !important;">Profil Orang Perorangan</h1>
          <hr>
        </div>
      </div>
    </a>
  </div>

  <div class="col-md-4" style="background-color: #FFFFFF !important;">
    <a href="/operasional-view#/ops-non-perorangan">
      <div class="panel box-v1" style="background-color: #2196F3 !important;">
        <div class="panel-body text-center">
           <h1 style="color: #FFFFFF !important;">Profil Non Perorangan</h1>
          <hr>
        </div>
      </div>
    </a>
  </div>

</div>


<!-- hrd menu -->
<div class="panel-body" v-if="this.user.role.role_code == 'hrd'">

  <div class="col-md-4" style="background-color: #FFFFFF !important;">
    <a href="/hrd-view#/hrd-list-karyawan">
      <div class="panel box-v1" style="background-color: #2196F3 !important;">
        <div class="panel-body text-center">
           <h1 style="color: #FFFFFF !important;">Rekap Data Karyawan</h1>
          <hr>
        </div>
      </div>
    </a>
  </div> 

</div>


<!-- administrator menu -->
<div class="panel-body" v-if="this.user.role.role_code == 'root'">
<div class="col-md-4" style="background-color: #FF6656 !important;">
<a href="/operasional-view#/ops-perorangan">
  <div class="panel box-v1" style="background-color: #27C24C !important;">
        <div class="panel-body text-center">
           <h1 style="color: #FFFFFF !important;">OPS</h1>
          <hr>
        </div>
  </div>
</a>
</div>

                         
<div class="col-md-4" style="background-color: #2196F3 !important;">
<a href="/hrd-view#/hrd-list-karyawan">
  <div class="panel box-v1" style="background-color: #f0ad4e !important;">
        <div class="panel-body text-center">
           <h1 style="color: #FFFFFF !important;">HRD</h1>
          <hr>
        </div>
  </div>
</a>
</div>
                    
<div class="col-md-4" style="background-color: #918C8C !important;">
<a href="/ukk-view#/ukk-peps">
  <div class="panel box-v1" style="background-color: #27C24C !important;">
        <div class="panel-body text-center">
           <h1 style="color: #FFFFFF !important;">UKK</h1>
          <hr>
        </div>
  </div>
</a>
</div>

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


Vue.use(VueSweetalert2)
Vue.use(Loading)
Vue.use(VueEvents)


export default {
  components: { 

  },
  data () {
    return { 
      isLoading: false,
      user:''
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
                  this.user = response.data.data; 
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

  },
	mounted(){ 
    this.fetchIt(); 
  }

}
</script>
<style> 
</style>
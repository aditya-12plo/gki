<template>
  <div> 


<div class="breadcrumb">
  <div class="panel" v-if="this.user.role.role_code == 'dirut'"> 
    <h1 class="animated fadeInLeft" align="center">DIREKTUR UTAMA</h1>                       
  </div>  
  <div class="panel" v-if="this.user.role.role_code == 'dirkep'">
    <h1 class="animated fadeInLeft" align="center">DIREKTUR KEPATUHAN</h1>                      
  </div>     
  <div class="panel" v-if="this.user.role.role_code == 'operasional'">
    <h1 class="animated fadeInLeft" align="center">OPERASIONAL</h1>              
  </div> 
  <div class="panel" v-if="this.user.role.role_code == 'root'">
    <h1 class="animated fadeInLeft" align="center">ADMINSTRATOR</h1>                 
  </div>
  <div class="panel" v-if="this.user.role.role_code == 'hrd'">
    <h1 class="animated fadeInLeft" align="center">HRD</h1>  
  </div>
  <div class="panel" v-if="this.user.role.role_code == 'ukk'">
    <h1 class="animated fadeInLeft" align="center">UNIT KERJA KHUSUS</h1>                
  </div>
</div> 



<div class="separator-breadcrumb border-top"></div>

<div class="row">

  <div class="col-md-3" v-if="this.user.role.role_code == 'root'">
    <div class="card mb-4">
      <div class="cardMenu">
        <h1 class="mb-3">Administrator</h1>
        <p class="text-20 text-success line-height-1 mb-3"> 
        <ul>
          <li><a href="/users-view#/list-users"><i class="nav-icon mr-2 fa fa-user"></i><span class="item-name">User  Akses</span></a></li>
          <li><a href="/roles-view#/list-roles"><i class="nav-icon mr-2 fa fa-gear"></i><span class="item-name">Role Akses</span></a></li>
        </ul>
        </p> 
      </div>
    </div>
  </div>

  <div class="col-md-3" v-if="this.user.role.role_code == 'root' || this.user.role.role_code == 'operasional' || this.user.role.role_code == 'dirut'">
    <div class="card mb-4">
      <div class="cardMenu">
        <h1 class="mb-3">Operasional</h1>
        <p class="text-20 text-success line-height-1 mb-3"> 
        <ul>
          <li><a href="/operasional-view#/ops-perorangan"><i class="nav-icon mr-2 i-Checked-User"></i><span class="item-name">Profil Perorangan</span></a></li>
          <li><a href="/operasional-view#/ops-non-perorangan"><i class="nav-icon mr-2 i-Business-ManWoman"></i><span class="item-name">Profil Non Perorangan</span></a></li>
        </ul>
        </p> 
      </div>
    </div>
  </div>

  <div class="col-md-3" v-if="this.user.role.role_code == 'root' || this.user.role.role_code == 'ukk' || this.user.role.role_code == 'dirkep'">
    <div class="card mb-4">
      <div class="cardMenu">
        <h1 class="mb-3">UKK</h1>
        <p class="text-20 text-success line-height-1 mb-3"> 
        <ul>
          <li><a href="/ukk-view#/ukk-peps"><i class="nav-icon mr-2 i-File-Horizontal-Text"></i><span class="item-name">Dokumen PEPS</span></a></li>
          <li><a href="/ukk-view#/ukk-dttot"><i class="nav-icon mr-2 i-File-Horizontal-Text"></i><span class="item-name">Dokumen DTTOT</span></a></li>
        </ul>
        </p> 
      </div>
    </div>
  </div>

 <div class="col-md-3" v-if="this.user.role.role_code == 'root' || this.user.role.role_code == 'hrd' || this.user.role.role_code == 'dirut'">
    <div class="card mb-4">
      <div class="cardMenu">
        <h1 class="mb-3">HRD</h1>
        <p class="text-20 text-success line-height-1 mb-3"> 
        <ul>
          <li><a href="/operasional-view#/hrd-list-karyawan"><i class="nav-icon mr-2 fa fa-users"></i><span class="item-name">Data Karyawan</span></a></li> 
        </ul>
        </p> 
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
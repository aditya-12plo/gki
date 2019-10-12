import VueRouter from 'vue-router';
import Home from './components/Site/Index.vue';
import ServerError from './components/Site/ServerError.vue';
import PageNotFound from './components/Site/PageNotFound.vue';
import Profile from './components/Users/Profile.vue';
import UserPassword from './components/Users/UserPassword.vue';
import Users from './components/Users/Index.vue';
import UserAdd from './components/Users/Add.vue';
import UserEdit from './components/Users/Edit.vue';
import UserDetail from './components/Users/Detail.vue';
import Roles from './components/Roles/Index.vue';  
import OpsPerorangan from './components/Ops/Perorangan/Index.vue';  
import OpsPeroranganEdit from './components/Ops/Perorangan/Edit.vue';  
import OpsPeroranganDetail from './components/Ops/Perorangan/Detail.vue';  
import OpsNonPerorangan from './components/Ops/NonPerorangan/Index.vue';  
import OpsNonPeroranganEdit from './components/Ops/NonPerorangan/Edit.vue';  
import OpsNonPeroranganDetail from './components/Ops/NonPerorangan/Detail.vue';  
import HrdKaryawan from './components/Hrd/Index.vue';
import HrdKaryawanAdd from './components/Hrd/Add.vue';
import HrdKaryawanDetail from './components/Hrd/Detail.vue';
import HrdKaryawanEdit from './components/Hrd/Edit.vue';
import HrdKaryawanJabatan from './components/Hrd/JabatanIndex.vue';
import HrdKaryawanDivisi from './components/Hrd/DivisiIndex.vue';
import HrdKaryawanChart from './components/Hrd/Chart.vue';
import UkkPeps from './components/Ukk/IndexPep.vue';
import UkkDttot from './components/Ukk/IndexDttot.vue';
import Print from './components/Prints/Index.vue';

let routes=[
{
	path:'/',
    name: 'home',
    component: Home
}, 

/** Print */
{
	path:'/print',
	name: 'Print',
	component:Print,
	 props: true
},

/** Users */
{
	path:'/profile',
    name: 'profile',
    component: Profile
}, 
{
	path:'/change-password',
    name: 'userpassword',
    component: UserPassword
}, 
{
	path:'/list-users',
    name: 'users',
    component: Users
}, 
{
	path:'/user-add',
	name: 'useradd',
	component:UserAdd,
	props: true
},
{
	path:'/user-edit/:id',
	name: 'useredit',
	component:UserEdit,
	 props: true
},
{
	path:'/user-detail/:id',
    name: 'userdetail',
    component: UserDetail,
	props: true
}, 

/** HRD */
{
	path:'/hrd-list-karyawan',
    name: 'HrdKaryawan',
    component: HrdKaryawan
},
{
	path:'/hrd-chart-karyawan',
    name: 'HrdKaryawanChart',
    component: HrdKaryawanChart
},
{
	path:'/hrd-list-jabatan-karyawan',
    name: 'HrdKaryawanJabatan',
    component: HrdKaryawanJabatan
},
{
	path:'/hrd-list-divisi-karyawan',
    name: 'HrdKaryawanDivisi',
    component: HrdKaryawanDivisi
},
{
	path:'/karyawan-add',
	name: 'HrdKaryawanAdd',
	component:HrdKaryawanAdd,
	props: true
},
{
	path:'/karyawan-detail/:id',
	name: 'HrdKaryawanDetail',
	component:HrdKaryawanDetail,
	props: true
},
{
	path:'/karyawan-edit/:id',
	name: 'HrdKaryawanEdit',
	component:HrdKaryawanEdit,
	 props: true
},

/** UKK */
{
	path:'/ukk-peps',
    name: 'UkkPeps',
    component: UkkPeps
},
{
	path:'/ukk-dttot',
    name: 'UkkDttot',
    component: UkkDttot
},


/** Perorangan */
{
	path:'/ops-perorangan',
    name: 'OpsPerorangan',
    component: OpsPerorangan
},
{
	path:'/ops-perorangan-detail/:id',
    name: 'OpsPeroranganDetail',
    component: OpsPeroranganDetail,
	props: true
}, 
{
	path:'/ops-perorangan-edit/:id',
	name: 'OpsPeroranganEdit',
	component:OpsPeroranganEdit,
	props: true
},

/** Non Perorangan */
{
	path:'/ops-non-perorangan',
    name: 'OpsNonPerorangan',
    component: OpsNonPerorangan
},
{
	path:'/ops-non-perorangan-detail/:id',
    name: 'OpsNonPeroranganDetail',
    component: OpsNonPeroranganDetail,
	props: true
}, 
{
	path:'/ops-non-perorangan-edit/:id',
	name: 'OpsNonPeroranganEdit',
	component:OpsNonPeroranganEdit,
	props: true
},
/** Roles */
{
	path:'/list-roles',
    name: 'roles',
    component: Roles
}, 
  

/**
 * Error
*/
{
	path:'/server-error',
    name: 'servererror',
    component: ServerError
}, 
{
	path:'*', 
    component: PageNotFound
}, 
{
	path:'/page-not-found',
    name: 'pagenotfound',
    component: PageNotFound
}, 


];

export default new VueRouter({
	routes,
	linkActiveClass: 'active'
});
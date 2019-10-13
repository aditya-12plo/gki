<template>
    <div>   

<div class="breadcrumb"> 
  <div class="panel">
    <h1 class="animated fadeInLeft" align="center">Pencarian Data DTTOT Dengan Data Nasabah Non Perorangan</h1>                
  </div>
</div>

<div class="row mb-4">
    <div class="col-md-12 mb-4">
        <div class="card text-left">
            <div class="card-body">
                <button class="btn btn-warning" type="button" @click.prevent="backLink()"><i class="fa fa-arrow-left"></i> Back</button>
                <button class="btn btn-success" type="button" @click.prevent="printItem()"><i class="fa fa-print"></i> Print</button>
                <button class="btn btn-primary" type="button" @click.prevent="downloadItem()"><i class="fa fa-file-excel-o"></i> Download</button>
                <div class="table-responsive">
                    <div id="zero_configuration_table_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4">
                        <div class="row">

    <vue-good-table
        id="dataDttotNonPerorangan"
        :columns="columns"
        :rows="rows"
      />

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
import moment from 'moment'
import Datepicker from 'vuejs-datepicker' 
import VueSweetalert2 from 'vue-sweetalert2'
import Loading from 'vue-loading-overlay'
import 'vue-loading-overlay/dist/vue-loading.css'
import 'vue-good-table/dist/vue-good-table.css'
import { VueGoodTable } from 'vue-good-table';
import VueHtmlToPaper from 'vue-html-to-paper';

const options = {
  name: '_blank',
  specs: [
    'fullscreen=yes',
    'titlebar=yes',
    'scrollbars=yes'
  ],
  styles: [
    'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css',
    'https://unpkg.com/kidlat-css/css/kidlat.css'
  ]
}

Vue.use(VueHtmlToPaper, options);
Vue.use(VueSweetalert2)
Vue.use(Loading);
export default {
    props: {
      rowDatanya: {
        type: Object,
        required: true
      }
    },
    components: { 
        Datepicker,
        VueGoodTable,
    },
    data () {
        return {
            url : window.webURL,
            errors: [],
            token: localStorage.getItem('token'),
            columns: [
            {
                label: 'NOMOR AKUN',
                field: 'nomor_akun',
                filterOptions: {
                    enabled: true, // enable filter for this column
                    placeholder: '', // placeholder for filter input
                    filterValue: '', // initial populated value for this filter
                    filterDropdownItems: [], // dropdown (with selected values) instead of text input
                    filterFn: this.columnFilterFn, //custom filter function that
                    trigger: 'enter', //only trigger on enter not on keyup 
                },
            },
            {
                label: 'NAMA PERUSAHAAN',
                field: 'nama_perusahaan',
                filterOptions: {
                    enabled: true, // enable filter for this column
                    placeholder: '', // placeholder for filter input
                    filterValue: '', // initial populated value for this filter
                    filterDropdownItems: [], // dropdown (with selected values) instead of text input
                    filterFn: this.columnFilterFn, //custom filter function that
                    trigger: 'enter', //only trigger on enter not on keyup 
                },
            },
            {
                label: 'NOMOR REKENING PERUSAHAAN',
                field: 'no_rekening_bank_perusahaan',
                filterOptions: {
                    enabled: true, // enable filter for this column
                    placeholder: '', // placeholder for filter input
                    filterValue: '', // initial populated value for this filter
                    filterDropdownItems: [], // dropdown (with selected values) instead of text input
                    filterFn: this.columnFilterFn, //custom filter function that
                    trigger: 'enter', //only trigger on enter not on keyup 
                },
            },
            {
                label: 'NOMOR REKENING BANK',
                field: 'nomor_rekening_bank',
                filterOptions: {
                    enabled: true, // enable filter for this column
                    placeholder: '', // placeholder for filter input
                    filterValue: '', // initial populated value for this filter
                    filterDropdownItems: [], // dropdown (with selected values) instead of text input
                    filterFn: this.columnFilterFn, //custom filter function that
                    trigger: 'enter', //only trigger on enter not on keyup 
                },
            },
            {
                label: 'NAMA OWNER',
                field: 'nama_bo',
                filterOptions: {
                    enabled: true, // enable filter for this column
                    placeholder: '', // placeholder for filter input
                    filterValue: '', // initial populated value for this filter
                    filterDropdownItems: [], // dropdown (with selected values) instead of text input
                    filterFn: this.columnFilterFn, //custom filter function that
                    trigger: 'enter', //only trigger on enter not on keyup 
                },
            },
            {
                label: 'NAMA ALIAS OWNER',
                field: 'nama_alias_bo',
                filterOptions: {
                    enabled: true, // enable filter for this column
                    placeholder: '', // placeholder for filter input
                    filterValue: '', // initial populated value for this filter
                    filterDropdownItems: [], // dropdown (with selected values) instead of text input
                    filterFn: this.columnFilterFn, //custom filter function that
                    trigger: 'enter', //only trigger on enter not on keyup 
                },
            },
            {
                label: 'NOMOR IDENTITAS OWNER',
                field: 'nomor_identitas_bo',
                filterOptions: {
                    enabled: true, // enable filter for this column
                    placeholder: '', // placeholder for filter input
                    filterValue: '', // initial populated value for this filter
                    filterDropdownItems: [], // dropdown (with selected values) instead of text input
                    filterFn: this.columnFilterFn, //custom filter function that
                    trigger: 'enter', //only trigger on enter not on keyup 
                },
            },
            {
                label: 'NAMA KUASA HUKUM',
                field: 'nama_knp',
                filterOptions: {
                    enabled: true, // enable filter for this column
                    placeholder: '', // placeholder for filter input
                    filterValue: '', // initial populated value for this filter
                    filterDropdownItems: [], // dropdown (with selected values) instead of text input
                    filterFn: this.columnFilterFn, //custom filter function that
                    trigger: 'enter', //only trigger on enter not on keyup 
                },
            },
            {
                label: 'NAMA ALIAS KUASA HUKUM',
                field: 'nama_alias_knp',
                filterOptions: {
                    enabled: true, // enable filter for this column
                    placeholder: '', // placeholder for filter input
                    filterValue: '', // initial populated value for this filter
                    filterDropdownItems: [], // dropdown (with selected values) instead of text input
                    filterFn: this.columnFilterFn, //custom filter function that
                    trigger: 'enter', //only trigger on enter not on keyup 
                },
            },
            {
                label: 'NOMOR IDENTITAS KUASA HUKUM',
                field: 'nomor_identitas_knp',
                filterOptions: {
                    enabled: true, // enable filter for this column
                    placeholder: '', // placeholder for filter input
                    filterValue: '', // initial populated value for this filter
                    filterDropdownItems: [], // dropdown (with selected values) instead of text input
                    filterFn: this.columnFilterFn, //custom filter function that
                    trigger: 'enter', //only trigger on enter not on keyup 
                },
            },
            {
                label: 'NAMA DTTOT',
                field: 'nama_dttot',
                filterOptions: {
                    enabled: true, // enable filter for this column
                    placeholder: '', // placeholder for filter input
                    filterValue: '', // initial populated value for this filter
                    filterDropdownItems: [], // dropdown (with selected values) instead of text input
                    filterFn: this.columnFilterFn, //custom filter function that
                    trigger: 'enter', //only trigger on enter not on keyup 
                },
            },
            {
                label: 'TEMPAT TANGGAL LAHIR DTTOT',
                field: 'lahir_dttot',
                filterOptions: {
                    enabled: true, // enable filter for this column
                    placeholder: '', // placeholder for filter input
                    filterValue: '', // initial populated value for this filter
                    filterDropdownItems: [], // dropdown (with selected values) instead of text input
                    filterFn: this.columnFilterFn, //custom filter function that
                    trigger: 'enter', //only trigger on enter not on keyup 
                },
            },
            {
                label: 'NEGARA DTTOT',
                field: 'negara_dttot',
                filterOptions: {
                    enabled: true, // enable filter for this column
                    placeholder: '', // placeholder for filter input
                    filterValue: '', // initial populated value for this filter
                    filterDropdownItems: [], // dropdown (with selected values) instead of text input
                    filterFn: this.columnFilterFn, //custom filter function that
                    trigger: 'enter', //only trigger on enter not on keyup 
                },
            },
            {
                label: 'ALAMAT DTTOT',
                field: 'alamat_dttot',
                filterOptions: {
                    enabled: true, // enable filter for this column
                    placeholder: '', // placeholder for filter input
                    filterValue: '', // initial populated value for this filter
                    filterDropdownItems: [], // dropdown (with selected values) instead of text input
                    filterFn: this.columnFilterFn, //custom filter function that
                    trigger: 'enter', //only trigger on enter not on keyup 
                },
            },
            {
                label: 'KETERANGAN DTTOT',
                field: 'keterangan_dttot',
                filterOptions: {
                    enabled: true, // enable filter for this column
                    placeholder: '', // placeholder for filter input
                    filterValue: '', // initial populated value for this filter
                    filterDropdownItems: [], // dropdown (with selected values) instead of text input
                    filterFn: this.columnFilterFn, //custom filter function that
                    trigger: 'enter', //only trigger on enter not on keyup 
                },
            },
            ],
            rows: [],
        }
    },
    watch: { 
        
    },
    methods: {
        backLink() {
            this.$router.go(-1);
        },
        fetchIt(){
            this.isLoading = true;
               axios.get('/ukk-dttot-with-non-perorangan?id='+this.rowDatanya.id+'&min='+this.rowDatanya.min+'&max='+this.rowDatanya.max+'&filename='+this.rowDatanya.filename).then((response) => {
                   if(!response.data){ 
                        window.location.href = window.webURL; 
                    }else{ 
                        this.rows = response.data;
                    }
                }).catch(error => {
                    if (! _.isEmpty(error.response)) {
                        if (error.response.status == 500) {
                            this.$router.push('/server-error');
                        }else{
                            this.isLoading = false;
                        }
                    }
                });
        },
        printItem() {
            this.$htmlToPaper('dataDttotNonPerorangan');
        } ,
        downloadItem() {
            var masuk = {
                'data' : this.rows, 
                'filename' : this.rowDatanya.filename, 
            }
            axios({
                url: '/ukk-dttot-excel-non-perorangan',
                method: 'POST',
                data: masuk,
                responseType: 'blob', // important
            }).then((response) => {
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download',this.rowDatanya.filename+'.xls');
                document.body.appendChild(link);
                link.click();
            });
        } ,
    },
    events: {

    },
    created: function() {

    },
	mounted() { 
        this.fetchIt(); 
    }

}

</script>
<style>

</style>
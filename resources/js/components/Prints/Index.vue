<template>
    <div>
<div class="row mb-4">
    <div class="col-md-12 mb-4">
        <div class="card text-left">
            <div class="card-body">
                <button class="btn btn-warning" type="button" @click.prevent="backLink()">Back</button>
                <button class="btn btn-success" type="button" @click.prevent="printItem()">Print</button>
                <br>
                <h4 class="card-title mb-3">Data Print</h4>
<div class="table-responsive">
<table border="1" ref="printTable" class="display table table-striped table-bordered dataTable" style="width:100%">
    <tbody>
    <thead>
        <tr>
            <th v-for="(value, key) in gridColumns">{{ value }}</th>
        </tr>
    </thead>
    <tbody> 
        <tr v-for="entry in gridData">
            <td v-for="(key,index) in gridColumns">{{entry[key]}}</td>
        </tr> 
    </tbody>
    </tbody>
</table>
</div>
            </div>
        </div>
    </div>
</div>

    </div>
</template>
<script>
export default {
    props: {
      datas: {
        type: Object,
        required: true
      }
    },
  components: { 

  },
  data () {
    return {  
        url : window.webURL,
        file: '', 
        errors: [],
        gridData:[],
        gridColumns:[],
    }
  },
  watch: { 

  },
  methods: { 
    backLink() {
		this.$router.go(-1);
    },
    dataAction () {
         this.gridColumns =  this.datas.gridColumns;
         this.gridData =  this.datas.gridData;
    },
    itemDelete(index) {
        this.gridData.splice(index, 1);
    },
    printItem() {
        var template = this.$refs.printTable;
        var newWin = window.open("");
        newWin.document.write(template.outerHTML);
        newWin.print();
        newWin.close();
    } ,
    
  },
  events: { 

  },
  created: function() { 

  },
	mounted(){ 
        this.dataAction();
  }

}
</script>
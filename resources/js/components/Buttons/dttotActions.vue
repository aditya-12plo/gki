 <template>
 <div>
    <div class="dttot-actions">
      <!-- <button class="btn btn-sm" @click="itemAction('process-item', rowData, rowIndex)"><i class="fa fa-refresh"> Proses</i></button> -->
      <button class="btn btn-sm" @click="itemAction('download-item', rowData, rowIndex)"><i class="fa fa-file-excel-o"> Download Excel</i></button>
      <button class="btn btn-sm" @click="itemAction('download-karyawan-item', rowData, rowIndex)"><i class="fa fa-users"> Cek Data Karyawan</i></button>
      <button class="btn btn-sm" @click="itemAction('download-perorangan-item', rowData, rowIndex)"><i class="fa fa-user"> Cek Data Perorangan</i></button>
      <button class="btn btn-sm" @click="itemAction('download-non-perorangan-item', rowData, rowIndex)"><i class="fa fa-user-times"> Cek Data Non Perorangan</i></button>
      <button class="btn btn-sm" @click="itemAction('download-pdf-item', rowData, rowIndex)"><i class="fa fa-file-pdf-o"> Download File</i></button>
      <button class="btn btn-sm" @click="itemAction('edit-item', rowData, rowIndex)"><i class="fa fa-edit"> Edit</i></button>
      <button class="btn btn-sm" @click="itemAction('delete-item', rowData, rowIndex)"><i class="fa fa-trash"> Delete</i></button>
    </div>
</div>
  </template>

  <script>

  export default {
    props: {
      rowData: {
        type: Object,
        required: true
      },
      rowIndex: {
        type: Number
      }
    },
    methods: {
      itemAction (action, data, index) {
      if(action === "process-item")
      {
               this.$root.$emit('processitem', data , index);
			   
      }
      else if(action === "edit-item")
      {
               this.$root.$emit('edititem', data , index);
      }
      else if(action === "download-item")
      {
               this.$root.$emit('downloaditem', data , index);
      }
      else if(action === "download-karyawan-item")
      {
               this.$root.$emit('downloadkaryawanitem', data , index);
      }
      else if(action === "download-non-perorangan-item")
      {
               this.$root.$emit('downloadnonperoranganitem', data , index);
      }
      else if(action === "download-perorangan-item")
      {
               this.$root.$emit('downloadperoranganitem', data , index);
      }
      else if(action === "download-pdf-item")
      {
               this.$root.$emit('downloadpdfitem', data , index);
      }
      else if(action === "delete-item")
      {
               this.$root.$emit('deleteitem', data , index);
      }
      else
      {
        alert('error');
      }
        
      }
      },
      
		mounted() { 
             
        } 
  }
  </script>

  <style>
    .dttot-actions button.ui.button {
      padding: 8px 8px;
    }
    .dttot-actions button.ui.button > i.icon {
      margin: auto !important;
    }
  </style>
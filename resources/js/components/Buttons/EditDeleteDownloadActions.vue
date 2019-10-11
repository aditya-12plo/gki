 <template>
 <div>
    <div class="edit-delete-download-actions">
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
      if(action === "edit-item")
      {
               this.$root.$emit('edititem', data , index);
      }
      else if(action === "delete-item")
      {
               this.$root.$emit('deleteitem', data , index);
      }
      else if(action === "download-pdf-item")
      {
               this.$root.$emit('downloadpdfitem', data , index);
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
    .edit-delete-download-actions button.ui.button {
      padding: 8px 8px;
    }
    .edit-delete-download-actions button.ui.button > i.icon {
      margin: auto !important;
    }
  </style>
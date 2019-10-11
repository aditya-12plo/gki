 <template>
 <div>
    <div class="view-actions">
      <button class="btn btn-sm" @click="itemAction('view-item', rowData, rowIndex)"><i class="fa fa-search-plus"> Detail</i></button>
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
      if(action === "view-item")
      {
               this.$root.$emit('viewitem', data , index);
			   
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
    .view-actions button.ui.button {
      padding: 8px 8px;
    }
    .view-actions button.ui.button > i.icon {
      margin: auto !important;
    }
  </style>
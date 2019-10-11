import Vue from 'vue'; 
import axios from 'axios'; 

export const userDetail = new Vue({ 
    data() { 
        return{
            user: this.fetchIt(), 
        }
    }, 
    methods: { 
        fetchIt() {  
            axios.get('/user/get-profile').then((response) => { 
                   return response.data.data.name;
            });
        }
    }
    
});
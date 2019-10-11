'use strict';
      /* ERRORS */
      class Errors{
        constructor(){
            this.errors = {};
        }
        static get(field){
            if(this.errors[field]){
                return  this.errors[field][0];
            }
        }
        static record(errors){
            this.errors = errors.response.data;
        }
        static any(){
            return Object.keys(this.errors).length > 0;
        }
        static has(field){
            return this.errors.hasOwnProperty(field);
        }
        static clear(field){
            if(field) delete this.errors[field];
            this.errors = {};
        }
        static clearAll(){
            this.errors = "";
        }
    };

    
module.exports = Errors; 
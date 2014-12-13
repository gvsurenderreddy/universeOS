//This file is published by transparency - everywhere with the best deeds.
//        Check transparency - everywhere.com for further information.
//        Licensed under the CC License, Version 4.0 (the "License");
//        you may not use this file except in compliance with the License.
//        You
//may obtain a copy of the License at
//        
//        https://creativecommons.org/licenses/by/4.0/legalcode
//        
//        Unless required by applicable law or agreed to in writing, software
//        distributed under the License is distributed on an "AS IS" BASIS,
//        WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
//        See the License for the specific language governing permissions and
//        limitations under the License.
//        @author nicZem for Tranpanrency - everywhere.com
//        


var api = new function(){
    this.basePath = 'http://localhost/universe';
    
    this.query = function(action, parameters, callback){
        console.log(parameters);
        var async;
        if(typeof callback !== 'undefined'){
            async = true;
        }else{
            async = false;
        };
        var result;
        $.ajax({
            type: 'POST',
            url: action,
            data: $.param(parameters),
            success:function(data){
                console.log(parameters);
                if(!async)
                    result = JSON.parse(data);
                else
                    result = callback(data);
            },
            async:async
        });
        return result;
    };
    
    
    
};
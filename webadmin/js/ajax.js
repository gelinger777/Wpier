function get_http(){
    var xmlhttp;
    /*@cc_on
    @if (@_jscript_version >= 5)
        try {
            xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
            try {
                xmlhttp = new 
                ActiveXObject("Microsoft.XMLHTTP");
            } catch (E) {
                xmlhttp = false;
            }
        }
    @else
        xmlhttp = false;
    @end @*/
    if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {
        try {
            xmlhttp = new XMLHttpRequest();
        } catch (e) {
            xmlhttp = false;
        }
    }
    return xmlhttp;
}

lookupworking=false;

function lookup(url,func,mod) {
      if(mod==null) mod=true;
      if(!this.http){
            this.http = get_http();
            lookupworking = false;
      } 
    
      if (!lookupworking && this.http) {
         var http = this.http;
         this.http.open("GET", url, mod);

         if(mod) {

           this.http.onreadystatechange = function() {
             if (http.readyState == 4) {
               lookupworking = false;
               if (func!='') eval(func+"(http.responseText)");
               return 0;
             }
           }
           lookupworking = true;
         }
         
         this.http.send(null);

         if  (!mod && this.http.status  ==  200) {  
           return this.http.responseText;
         }  
      }
      if(!this.http){
         alert('Ошибка при создании XMLHTTP объекта!')
      } 
} 
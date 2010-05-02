for(i=0;i<document.forms["MainEditForm"].elements.length;i++) {
	if(document.forms["MainEditForm"].elements[i].id!="closebutton") {
	  if(document.forms["MainEditForm"].elements[i].id=="savebutton") 
          document.forms["MainEditForm"].elements[i].removeNode(true);
	  else
	      document.forms["MainEditForm"].elements[i].disabled=true;
	}
}
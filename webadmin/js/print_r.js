/**************************************************
 * dom-drag.js
 * 09.25.2001
 * www.youngpup.net
 **************************************************
 * 10.28.2001 - fixed minor bug where events
 * sometimes fired off the handle, not the root.
 **************************************************/

var Drag = {

    obj : null,

    init : function(o, oRoot, minX, maxX, minY, maxY, bSwapHorzRef, bSwapVertRef, fXMapper, fYMapper)
    {
        o.onmousedown    = Drag.start;

        o.hmode            = bSwapHorzRef ? false : true ;
        o.vmode            = bSwapVertRef ? false : true ;

        o.root = oRoot && oRoot != null ? oRoot : o ;

        if (o.hmode  && isNaN(parseInt(o.root.style.left  ))) o.root.style.left   = "0px";
        if (o.vmode  && isNaN(parseInt(o.root.style.top   ))) o.root.style.top    = "0px";
        if (!o.hmode && isNaN(parseInt(o.root.style.right ))) o.root.style.right  = "0px";
        if (!o.vmode && isNaN(parseInt(o.root.style.bottom))) o.root.style.bottom = "0px";

        o.minX    = typeof minX != 'undefined' ? minX : null;
        o.minY    = typeof minY != 'undefined' ? minY : null;
        o.maxX    = typeof maxX != 'undefined' ? maxX : null;
        o.maxY    = typeof maxY != 'undefined' ? maxY : null;

        o.xMapper = fXMapper ? fXMapper : null;
        o.yMapper = fYMapper ? fYMapper : null;

        o.root.onDragStart    = new Function();
        o.root.onDragEnd    = new Function();
        o.root.onDrag        = new Function();
    },

    start : function(e)
    {
        var o = Drag.obj = this;
        e = Drag.fixE(e);
        if (o.root.style.top.indexOf('%') > -1)
        {
        	var y = parseInt(o.vmode ? o.root.offsetTop  : o.root.style.bottom);
        	var x = parseInt(o.hmode ? o.root.offsetLeft : o.root.style.right );
        }
        else
        {
        	var y = parseInt(o.vmode ? o.root.style.top  : o.root.style.bottom);
        	var x = parseInt(o.hmode ? o.root.style.left : o.root.style.right );
        }
        o.root.onDragStart(x, y);

        o.lastMouseX    = e.clientX;
        o.lastMouseY    = e.clientY;

        if (o.hmode) {
            if (o.minX != null)    o.minMouseX    = e.clientX - x + o.minX;
            if (o.maxX != null)    o.maxMouseX    = o.minMouseX + o.maxX - o.minX;
        } else {
            if (o.minX != null) o.maxMouseX = -o.minX + e.clientX + x;
            if (o.maxX != null) o.minMouseX = -o.maxX + e.clientX + x;
        }

        if (o.vmode) {
            if (o.minY != null)    o.minMouseY    = e.clientY - y + o.minY;
            if (o.maxY != null)    o.maxMouseY    = o.minMouseY + o.maxY - o.minY;
        } else {
            if (o.minY != null) o.maxMouseY = -o.minY + e.clientY + y;
            if (o.maxY != null) o.minMouseY = -o.maxY + e.clientY + y;
        }

        document.onmousemove    = Drag.drag;
        document.onmouseup        = Drag.end;

        return false;
    },

    drag : function(e)
    {
        e = Drag.fixE(e);
        var o = Drag.obj;

        var ey    = e.clientY;
        var ex    = e.clientX;
        var y = parseInt(o.vmode ? o.root.style.top : o.root.style.bottom);
        var x = parseInt(o.hmode ? o.root.style.left : o.root.style.right );
        var nx, ny;

        if (o.minX != null) ex = o.hmode ? Math.max(ex, o.minMouseX) : Math.min(ex, o.maxMouseX);
        if (o.maxX != null) ex = o.hmode ? Math.min(ex, o.maxMouseX) : Math.max(ex, o.minMouseX);
        if (o.minY != null) ey = o.vmode ? Math.max(ey, o.minMouseY) : Math.min(ey, o.maxMouseY);
        if (o.maxY != null) ey = o.vmode ? Math.min(ey, o.maxMouseY) : Math.max(ey, o.minMouseY);

        nx = x + ((ex - o.lastMouseX) * (o.hmode ? 1 : -1));
        ny = y + ((ey - o.lastMouseY) * (o.vmode ? 1 : -1));

        if (o.xMapper)        nx = o.xMapper(y)
        else if (o.yMapper)    ny = o.yMapper(x)

        Drag.obj.root.style[o.hmode ? "left" : "right"] = nx + "px";
        Drag.obj.root.style[o.vmode ? "top" : "bottom"] = ny + "px";
        Drag.obj.lastMouseX    = ex;
        Drag.obj.lastMouseY    = ey;

        Drag.obj.root.onDrag(nx, ny);
        return false;
    },

    end : function()
    {
        document.onmousemove = null;
        document.onmouseup   = null;
        Drag.obj.root.onDragEnd(    parseInt(Drag.obj.root.style[Drag.obj.hmode ? "left" : "right"]), 
                                    parseInt(Drag.obj.root.style[Drag.obj.vmode ? "top" : "bottom"]));
        Drag.obj = null;
    },

    fixE : function(e)
    {
        if (typeof e == 'undefined') e = window.event;
        if (typeof e.layerX == 'undefined') e.layerX = e.offsetX;
        if (typeof e.layerY == 'undefined') e.layerY = e.offsetY;
        return e;
    }
};

/*
end Dom Drag
*/
/*
	Main Object
*/
function print_rr (objRef, strObjRef) 
{
	// Objects Properties
	// Container for this instance of Print_R
	this.container = {};
	// Reference to the Title Bar
	this.titleBar = {};
	// Reference to the Body 
	this.body = {};
	// Reference to Header
	this.header = {};
	// Object Reference to Object we are viewing
	this.objRef = objRef;
	// String Representation of object we are viewing
	this.strObjRef = strObjRef;
	// Object Reference to refresh Button
	this.refreshBtn = {};
	// Object Reference to Close Button
	this.closeBtn = {};
	// Parsed parts of object we are viewing
	this.arrStrObjRef = [];
	// Simple name of Variable we are parsing
	this.objParseName = "";
	/*
		converts strObjRef to an Array of the parts of the object i,e ["window", "document", "childNodes", "[", "0", "]"]
	*/
	this.parseStrObjRef = function ()
	{
		// first split on the "." character	
		var temp = this.strObjRef;
		//alert ("'" + temp + "'");
		var temp2 = [];
		var x = 0;
		var buffer = ""; 	
		var inSingle = false;
		var inDouble = false;
		var inBracket = false;	
		//Now check for "[" and "]" chars to indicate arrays
		for (var elem =0;elem < temp.length;elem++)
		{
			// make sure we only look at the array elements not the clear() function
				switch (temp.charAt(elem))
				{					
					case "'":
						if (!inDouble)
						{	
							if (inSingle)
							{
								inSingle = false;
							}						
							else
							{
								inSingle = true;
							}
						}	
						buffer += temp.charAt(elem);
						break;
					case '"':
						if (!inSingle)
						{	
							if (inDouble)
							{
								inDouble = false;
							}						
							else
							{
								inDouble = true;
							}
						}
						buffer += temp.charAt(elem);
						break;
					case "[":
						if (!inSingle && !inDouble)
						{
							inBracket = true;
							if (buffer != "")
							{				
								temp2[temp2.length] = buffer;								
							}					
							buffer = new String();						
							temp2[temp2.length] = temp.charAt(elem);							
						}
						else
						{
							buffer += temp.charAt(elem);
						}
						break;
					case "]":
						if (!inSingle && !inDouble)
						{
							inBracket = false;
							if (buffer != "")
							{				
								temp2[temp2.length] = buffer;								
							}
							buffer = new String();
							temp2[temp2.length] = temp.charAt(elem);							
						}	
						else
						{
							buffer += temp.charAt(elem);
						}					
						break;
					case ".":
						if (!inSingle && !inDouble && !inBracket)
						{
							if (buffer != "")
							{
								temp2[temp2.length] = buffer;
								temp2[temp2.length] = temp.charAt(elem);
							}											
							buffer = "";	
						}
						else
						{
							buffer += temp.charAt(elem);
						}
						break;
					default: 						
						buffer += temp.charAt(elem);						
						break;
				}
				if (buffer != "]" && buffer != "" && buffer != null)
				{
					this.objParseName = buffer;
				}
								//will be overwritten unless it is the last entry 
				// which is the one we want							
			}			
			
		
		if (buffer != "")
		{
			temp2[temp2.length] = buffer;			
		}
		//alert (temp2);	
		// add the newly created array to our main object
		this.arrStrObjRef = temp2;	
		//alert (temp2);
	};
	// function to close the instance
	this.close = function ()
	{
		document.body.removeChild(this.container);
	}
	// this function sets up the container for everything else
	this.createContainers = function ()
	{
		//remove any existing Containers if for some reason it is called twice
		try {
			document.body.removeChild(this.container);
		} catch (e){}	
		this.container = document.createElement('div');
		this.applyStyles (styleObj.container, this.container);
		this.titleBar = document.createElement('div');
		this.applyStyles (styleObj.titleBar, this.titleBar);	
		this.container.appendChild(this.titleBar);
		this.header = document.createElement('div');
		this.container.appendChild(this.header);		
		this.body = document.createElement('div');		
		this.container.appendChild(this.body);
	};	
	// Function creates the title Bar
	this.populateTitleBar = function ()
	{			
		var table = document.createElement('table');		
		var tbody = table.appendChild(document.createElement('tbody'));
		var tr = tbody.appendChild(document.createElement('tr'));
		var td1 = tr.appendChild(document.createElement('td'));
		td1.appendChild(document.createTextNode(this.objParseName));
		var td2 = tr.appendChild(document.createElement('td'));
		this.refreshBtn = td2.appendChild(document.createElement('button'));	
		this.refreshBtn.appendChild(document.createTextNode('Refresh'));
		this.closeBtn = td2.appendChild(document.createElement('button'));	
		this.closeBtn.appendChild(document.createTextNode('close'));		
		this.applyStyles(styleObj.propTable, table);		
		this.applyStyles(styleObj.titleTd1, td1);
		this.applyStyles(styleObj.titleTd2, td2);
		this.titleBar.appendChild(table);
	};
	/*
	//function creates the browseable header
	this.populateHeader = function ()
	{
		//alert(this.arrStrObjRef);
		// the string we will change this.objStrRef when updating object
		var strRef = "";
		// iterate through each element of this.arrStrObjRef
		for (var x =0;x<this.arrStrObjRef.length;x++)
		{
			// if it is a "[" or a "]" char
			if ((this.arrStrObjRef[x].match(/\[/g)) || (this.arrStrObjRef[x].match(/\]/g))) 
			{
				// just output a single char "[" or "]"
				this.header.appendChild(document.createTextNode(this.arrStrObjRef[x]));
				// if it is a "[" make sure we add it to strRef
				if (this.arrStrObjRef[x] == "[")
				{
					strRef += "[";	
				}				
			}
			// it it is anything but a "[" or "]"
			else
			{
				// we want to make it browseable
				var a = document.createElement('a');
				a.href = "javascript:void(0);";
				// apply our styles
				this.applyStyles (styleObj.a , a);
				// add the name of the object we looking at
				a.appendChild(document.createTextNode(this.arrStrObjRef[x]));
				// if there is a previous item in the list is it a "]" char 
				if (this.arrStrObjRef[x-1] && this.arrStrObjRef[x-1] == "]" && x != (this.arrStrObjRef.length -1))
				{
					// if so we need to generate the "." char for output and strRef
					this.header.appendChild(document.createTextNode("."));
					strRef += ".";
				}	
				// now add the name to strRef we do it here so that the "." is in the right place
				strRef += this.arrStrObjRef[x];
				//if the next item in the array is a "]"
				if (this.arrStrObjRef[x+1] == "]")
				{
					// add it to strRef
					strRef += "]";	
				}	
				// now we have a working strRef set the onclick handler for the output
				a.onclick = clickMe(this, strRef);		
				// add the "a" element to the dom				
				this.header.appendChild(a);		
				// now we need to add the "." operator to the dom and strRef for the next pass		
				if (this.arrStrObjRef[x+1] && this.arrStrObjRef[x+1] != "]" && this.arrStrObjRef[x+1] != "[")
				{
					this.header.appendChild(document.createTextNode("."));
					strRef += ".";					
				}				
			}			
		}
		try 
		{
		// this makes sure we don't have trailing "."'s in our dom
		if (this.header.lastChild.value == ".")
		{			
			this.header.removeChild(this.header.lastChild);
		}
		} catch (e){}		
	};
	*/
	//function creates the browseable header
	this.populateHeader = function ()
	{
		//alert(this.arrStrObjRef);
		// the string we will change this.objStrRef when updating object
		var strRef = "";
		// iterate through each element of this.arrStrObjRef
		var inBracket = false;
		for (var x =0;x<this.arrStrObjRef.length;x++)
		{
			switch (this.arrStrObjRef[x])
			{				
				case "[":
				case "]":				
				case ".":
					if (this.arrStrObjRef[x] == "[")
					{
						inBracket = true;
					}	
					if (this.arrStrObjRef[x] == "]")
					{
						inBracket = false;
					}									
					this.header.appendChild(document.createTextNode(this.arrStrObjRef[x]));
					if (this.arrStrObjRef[x] != "]")
					{
						strRef += this.arrStrObjRef[x];
					}
					break;
				default:
					if (this.arrStrObjRef[x-1] == ']')
					{
						this.header.appendChild(document.createTextNode("."));
					strRef += ".";
					}
					var a = document.createElement('a');
					a.appendChild(document.createTextNode(this.arrStrObjRef[x]));					
					strRef += this.arrStrObjRef[x];					
					if (inBracket)
					{
						strRef += "]";
					}
					a.onclick = clickMe(this, strRef);
					a.href = "javascript:void(0);";
					this.header.appendChild(a);
			}
			/*		
			// if it is a "[" or a "]" char
			if ((this.arrStrObjRef[x].match(/\[/g))) 
			{
				// just output a single char "[" or "]"
				
				// if it is a "[" make sure we add it to strRef
				if (this.arrStrObjRef[x] == "[")
				{
					strRef += "[";	
				}				
			}
			// it it is anything but a "[" or "]"
			else
			{
				// we want to make it browseable
				
				
				// apply our styles
				this.applyStyles (styleObj.a , a);
				// add the name of the object we looking at
				
				// if there is a previous item in the list is it a "]" char 
				if (this.arrStrObjRef[x-1] && this.arrStrObjRef[x-1] == "]" && x != (this.arrStrObjRef.length -1))
				{
					// if so we need to generate the "." char for output and strRef
					//this.header.appendChild(document.createTextNode("."));
					//strRef += ".";
				}	
				// now add the name to strRef we do it here so that the "." is in the right place
				strRef += this.arrStrObjRef[x];
				//if the next item in the array is a "]"
				if (this.arrStrObjRef[x] == "]")
				{	
					if (this.arrStrObjRef[x+1] && this.arrStrObjRef[x+1] != "]" && this.arrStrObjRef[x+1] != "[")
					{
						this.header.appendChild(document.createTextNode("."));
						strRef += ".";					
					}
				}	
				
				if (this.arrStrObjRef[x] != "]")
				{
					// now we have a working strRef set the onclick handler for the output
					a.onclick = clickMe(this, strRef);
					a.href = "javascript:void(0);";
				}		
				// add the "a" element to the dom				
				this.header.appendChild(a);		
				// now we need to add the "." operator to the dom and strRef for the next pass		
				if (this.arrStrObjRef[x+1] && this.arrStrObjRef[x+1] != "]" && this.arrStrObjRef[x+1] != "[")
				{
					this.header.appendChild(document.createTextNode("."));
					strRef += ".";					
				}				
			}
			*/	
			//alert(strRef);
		}
		try 
		{
		// this makes sure we don't have trailing "."'s in our dom
		if (this.header.lastChild.value == ".")
		{			
			this.header.removeChild(this.header.lastChild);
		}
		} catch (e){}		
	};
	/*
		This is the main function to populate the dom
	*/
	this.populateBody = function ()
	{
		// we want our properties, events, functions alphabatized so we need to cache them so we can sort them
		var properties = [];
		var functions = [];
		var events = [];
		// start creating the output dom
		var table = document.createElement('table');
		var tbodyobj = document.createElement('tbody');
		var tr = tbodyobj.appendChild(document.createElement('tr'));
		var th1 = tr.appendChild(document.createElement('th'));
		this.applyStyles (styleObj.propTableTh, th1);
		th1.appendChild(document.createTextNode('This Object'));
		th1.colSpan = "3";
		//Even if the object has no props we want to make sure we tell them about the object they are looking at
		var tr = tbodyobj.appendChild(document.createElement('tr'));
		var th1 = tr.appendChild(document.createElement('td'));
		this.applyStyles (styleObj.propTableTd, th1);				
		th1.appendChild(document.createTextNode('Name'));

		var th1 = tr.appendChild(document.createElement('td'));
		this.applyStyles (styleObj.propTableTd, th1);				
		th1.appendChild(document.createTextNode(this.objParseName));
		
		th1.colSpan = "2";
		
		var tr = tbodyobj.appendChild(document.createElement('tr'));
		var th1 = tr.appendChild(document.createElement('td'));
		this.applyStyles (styleObj.propTableTd, th1);				
		th1.appendChild(document.createTextNode('toString'));
		
		var th1 = tr.appendChild(document.createElement('td'));
		this.applyStyles (styleObj.propTableTd, th1);				
		var pre = document.createElement('pre');
		// in Some browsers calling the "toString" method of built in objects causes an error so plan for it
		try 
		{
			pre.appendChild(document.createTextNode(getString(this.objRef)));
				
		}catch(e) {
			pre.appendChild(document.createTextNode("undefined"));
		}
		th1.appendChild(pre);
		this.applyStyles (styleObj.tostring, pre);
		th1.colSpan = "2";
		
		
		var tr = tbodyobj.appendChild(document.createElement('tr'));
		var th1 = tr.appendChild(document.createElement('td'));
		this.applyStyles (styleObj.propTableTd, th1);				
		th1.appendChild(document.createTextNode('Type'));
		
		var th1 = tr.appendChild(document.createElement('td'));
		this.applyStyles (styleObj.propTableTd, th1);
		var type= typeof(this.objRef);
		// Arrays report to be Objects when typeof is used
		// however if they are true arrays we attempt to compare their constructor to 
		// known arrays constructor
		if (properties.constructor == this.objRef.constructor)
		{
			type = "array";
		}			
		th1.appendChild(document.createTextNode(type));		
		th1.colSpan = "2";
		tbodyobj.appendChild(tr);
		table.appendChild(tbodyobj);	
		// start the dom ouptut for the props funcs and events		
		var tbodyprops = document.createElement('tbody');
		var tbodyfuncs = document.createElement('tbody'); 
		var tbodyevents = document.createElement('tbody');
		//this is main walking of object here
		// it is enclosed in a try because enumerating a built in object sometimes causes an Error
		var props = [];
		var x = 0;
		try{
		  for (prop in this.objRef)
		  {
			props[props.length] = prop;			
		  }
		} catch(e) {}
		
			// the boolean false is not the same as null so we need another known false to test against
			var boolFalse = false;
			// enumerate over every property of this.objRef
			for (prop in props)
			{
				//document.write(props[prop] +  "<br />");
				if (props[prop] != 'sun')
				{
				try 
				{
					// as long as it is a valid object and not null run the typeof method on it
					if (this.objRef[props[prop]] || this.objRef[props[prop]] == boolFalse)
					{				
						var type = typeof(this.objRef[props[prop]]);
					}
					// if it is null mark it with an * so we know it is not enumerable
					else
					{	
						type = "object*";
					}
				}
				catch (e) {
					type = "undefined";
				}
				
				// alright depending on the type of object add it to the appropriate array
				switch (type)
				{
					// event handlers are always prefaced with _e__ so lets look for it
					// btw event handlers are 
					case "function":
						if (prop.indexOf('_e__') > -1)
						{
							type = "event";
							events[events.length] = {name:props[prop], toString:getString(this.objRef[props[prop]]), type:type};
						}
						else
						{													
							functions[functions.length] = {name:props[prop], toString:getString(this.objRef[props[prop]]), type:type};
						}
						break;						
					case "boolean":
						properties[properties.length] = {name:props[prop], toString:getString(this.objRef[props[prop]]), type:type};
						break;
					case "string":				
						properties[properties.length] = {name:props[prop], toString:getString(this.objRef[props[prop]]) + unescape('%A0'), type:type};			
						break;
					case "number":
						properties[properties.length] = {name:props[prop], toString:getString(this.objRef[props[prop]]), type:type};
						break;	
					case "object":												
						if (properties.constructor == this.objRef[props[prop]].constructor)
						{
							type = "array";
						}						
						properties[properties.length] = {name:props[prop], toString:getString(this.objRef[props[prop]]), type:type};
						break;
					default:
						properties[properties.length] = {name:props[prop], toString:"null", type:type};
						break;
				}
				}
				
			}
		
		{
			//alert('An Error occured ' + e.message);		
		}
		//var testObj2 = new Object();
		//properties.push({name:"isPrototypeOf", toString:this.objRef["constructor"].isPrototypeOf(Array), type:typeof(this.objRef["constructor"])});		
		// Write table is a reusable function for writing the tables out
		if (typeof(this.objRef) != 'string')
		{
			writeTable(properties, tbodyprops, 'Properties', this);
			writeTable(functions, tbodyfuncs, 'Methods', this);
			writeTable(events, tbodyevents, 'Events', this);
		}
		// add the new Tbody elements to the table
		table.appendChild(tbodyprops);
		table.appendChild(tbodyfuncs);
		table.appendChild(tbodyevents);
		// apply appropriate styles
		this.applyStyles (styleObj.propTable, table);
		// Add to the body object
		this.body.appendChild(table);
		// Sometimes calling the toString of an object generates an error so we catch it here
		function getString(ref)
		{
			try
			{
				if (ref)
				{
					try 
					{	
						return ref;
					} catch (e) {
						return ref.toString();
					}
				}
				else {
					try 
					{
						return ref.toString();
					}
					catch (e) 
					{
						return "undefined";
					}
				}
				
			} catch (e)
			{
				// if we can not convert it to a string write "undefined" just like javascript
				return "undefined";
			}
		}
		// helper function to perform repeating tasks
		function writeTable(properties, tbodyprops, name, print_r)
		{
			// only output to the tbody if there are actually props to enumerate
			if (properties.length > 0)
			{
				// Build the Header Row
				var tr = tbodyprops.appendChild(document.createElement('tr'));
				var th1 = tr.appendChild(document.createElement('th'));
				print_r.applyStyles (styleObj.propTableTh, th1);
				th1.appendChild(document.createTextNode(name));
				th1.colSpan = "3";	
				// Build the Header
				var tr = tbodyprops.appendChild(document.createElement('tr'));
				var th1 = tr.appendChild(document.createElement('th'));
				print_r.applyStyles (styleObj.propTableTd, th1);				
				th1.appendChild(document.createTextNode('Name'));
				var th1 = tr.appendChild(document.createElement('th'));
				print_r.applyStyles (styleObj.propTableTd, th1);
				th1.appendChild(document.createTextNode('toString'));
				var th1 = tr.appendChild(document.createElement('th'));
				print_r.applyStyles (styleObj.propTableTd, th1);
				th1.appendChild(document.createTextNode('Type'));
				// before we do anything lets alphabatize the list
				properties.sort(arrSort);
				// now we iterate through the list
				for (prop in properties)
				{
					if (properties[prop] && properties[prop].name)
					{
					// create a row for the object
					var tr = document.createElement('tr');
					var tdname = document.createElement('td');
					// output the div that will be clickable for appropriate elements				
					var z = document.createElement('div');	
					var a = document.createElement('a');		
					a.appendChild(document.createTextNode(properties[prop].name));					
					var type = 	properties[prop].type;				
					// if the object is enumerable or null don't run the onclick function
					if (type  != "object*")
					{
						a.href = "javascript:void(0);";
						// if the property name is a number access it as an array otherwise access it as an object 
						//i.e. array obj[4] object obj.prop
						if (isNaN(parseInt(properties[prop].name.charAt(0))))
						{
							try
							{
								var test = eval(print_r.strObjRef + "." + properties[prop].name);
								a.onclick = clickMe(print_r, print_r.strObjRef + "." + properties[prop].name);
							} catch(e) 
							{
								try {									
									var test = eval(print_r.strObjRef + "[" + properties[prop].name + "]");									
									a.onclick = clickMe(print_r, print_r.strObjRef + "[" + properties[prop].name + "]");
								}
								catch (e) 
								{
									a.onclick = clickMe(print_r, print_r.strObjRef + "['" + properties[prop].name + "']");
								}
							}
							//a.onclick = clickMe(print_r, print_r.strObjRef + "." + properties[prop].name);
							/*
							if (properties[prop].name.match(/[^a-zA-Z_\-]/g))
							{
								a.onclick = clickMe(print_r, print_r.strObjRef + "['" + properties[prop].name + "']");
							}
							else
							{
								a.onclick = clickMe(print_r, print_r.strObjRef + "." + properties[prop].name);
							}	
							*/			
						}	
						else
						{
							try
							{
								var test = eval(print_r.strObjRef + "." + properties[prop].name);	
								a.onclick = clickMe(print_r, print_r.strObjRef + "[" + properties[prop].name + "]");	
							} catch(e) 
							{
								try {
									var test = eval(print_r.strObjRef + "[" + properties[prop].name + "]");									
									a.onclick = clickMe(print_r, print_r.strObjRef + "[" + properties[prop].name + "]");
								}
								catch (e) 
								{
									a.onclick = clickMe(print_r, print_r.strObjRef + "['" + properties[prop].name + "']");
								}
							}							
						}
					}
					z.appendChild(a);
					tdname.appendChild(z);
						
					// now add the toString representation of the code.
					// some browsers keep formatting so add it to a <pre> element					
					var tdstring = document.createElement('td');	
					var pre = document.createElement('pre');
					try 
					{
						pre.appendChild(document.createTextNode(properties[prop].toString));
					} catch (e) {
						pre.appendChild(document.createTextNode('undefined1'));						
					}
					tdstring.appendChild(pre);
					// finally add the type and apply appropriate styles
					var tdtype = document.createElement('td');				
					tdtype.appendChild(document.createTextNode(properties[prop].type));
					print_r.applyStyles (styleObj.propTableTd, tdname);
					print_r.applyStyles (styleObj.name, z);
					print_r.applyStyles (styleObj.propTableTd, tdstring);
					print_r.applyStyles (styleObj.propTableTd, tdtype);					
					print_r.applyStyles (styleObj.tostring, pre);					
					tr.appendChild(tdname);
					tr.appendChild(tdstring);
					tr.appendChild(tdtype);
					tbodyprops.appendChild(tr);	
										
					} 
				}
				
			}
		}
	};
	this.center = function ()
	{
		
	}
	// this function will be used to rebuild the information stored in the container
	this.refresh = function ()
	{
		this.container.removeChild(this.titleBar);
		this.container.removeChild(this.header);
		this.container.removeChild(this.body);
		this.titleBar = document.createElement('div');
		this.container.appendChild(this.titleBar);
		this.header = document.createElement('div');
		this.container.appendChild(this.header);		
		this.body = document.createElement('div');
		this.container.appendChild(this.body);
		this.parseStrObjRef();
		this.populateTitleBar();
		this.refreshBtn.onclick = refreshThis(this);
		this.closeBtn.onclick = closeThis(this);
		this.applyStyles(styleObj.titleBar, this.titleBar);
		this.populateHeader();
		this.populateBody();
		this.container.style.left = (this.container.offsetLeft + (parseInt(this.container.style.width)/2)) + "px";
		this.container.style.top = (this.container.offsetTop + (parseInt(this.container.style.height)/2)) + "px";
		if (!this.container.style.MarginLeft)
		{
			this.container.style.MarginTop = "";
			this.container.style.MarginLeft = "";
		}		
		
		// make it draggable
		Drag.init(this.titleBar, this.container);
		// internal closure for the refresh button
	function refreshThis(print_r)
	{
		return (function(){
			return print_r.refresh();
		});
	}
	// internal closure for the close button
	function closeThis(print_r)
	{
		return (function(){
			return print_r.close();				
		});
	}
	};
	// similar to above but initializes rather than refreshes
	this.render = function ()
	{
		// Add Container to Document.	
		document.body.appendChild(this.container);
		// populate the refresh  amd close buttons
		this.refreshBtn.onclick = refreshThis(this);
		this.closeBtn.onclick = closeThis(this);
		this.applyStyles(styleObj.titleBar, this.titleBar);
		// Ugly Fix to make DOM DRAG WORK right
		this.container.style.left = (this.container.offsetLeft + (parseInt(this.container.style.width)/2)) + "px";
		this.container.style.top = (this.container.offsetTop + (parseInt(this.container.style.height)/2)) + "px";
		if (!this.container.style.MarginLeft)
		{
			this.container.style.MarginTop = "";
			this.container.style.MarginLeft = "";
		}		
		// start dragging
		Drag.init(this.titleBar, this.container);
		// internal closure for the refresh button
	function refreshThis(print_r)
	{
		return (function(){
			return print_r.refresh();
		});
	}
	// internal closure for the close button
	function closeThis(print_r)
	{
		return (function(){
			return print_r.close();				
		});
	}
	};	
	/*
		Since I could not get a css file to work consistently accross browsers in the 
		Bookmarklet edition, I went with this approach so I would not have so many
		dom.style.***** = "50px"; lines. And also to make it easy to customize
	*/
	this.applyStyles = function (styleObject, domObject)
	{
		for (prop in styleObject)
		{
			try 
			{
				domObject.style[prop] = styleObject[prop];
			} catch (e)
			{
			}
		}
	};	
	// initialize the function
	// parse the string
	this.parseStrObjRef();
	// create the Containers
	this.createContainers();
	// create the title bar
	this.populateTitleBar();
	// write the header
	this.populateHeader();
	// and the meat and potatoes
	this.populateBody();
	// the render function adds the final touches and makes it visible
	this.render();
	
	// internal function for the clickable Elements
	function clickMe (print_r, str)
	{
		return (function(){
			print_r.objRef = eval(str);
			print_r.strObjRef = str;
			print_r.refresh();					
		});
	}
	// Function to help sort the arrays
	function arrSort(a,b)
	{
		var acomp = a['name'].toString(10);
		var bcomp = b['name'].toString(10);
		if (!isNaN(Number(acomp)))
		{
			acomp = Number(acomp);
		}
		if (!isNaN(Number(bcomp)))
		{
			bcomp = Number(bcomp);
		}
		if (acomp < bcomp)
		{
			return -1;
		}
		if (acomp > bcomp)
		{
			return 1;
		}
		return 0;
	}		
}
// Customizable style object to suplement css 
// Since I could not get it to consistently work
// accross all supported browsers
var styleObj = {
	container:
	{
		position:'absolute',
		left:'50%',
		top:'50%',
		width:'800px',
		height:'500px',
		marginTop:'-250px',
		marginLeft:'-400px',
		borderColor:'red',
		borderWidth:'1px',
		borderStyle:'solid',
		overflow:'scroll',
		backgroundColor:'white'
	},
	titleBar:
	{
		width:"100%",	
		backgroundColor:'blue',
		color:'white'
	},
	header:
	{
		width:"100%"	
	},
	body:
	{
		width:"100%",
		fontSize:'8pt'			
	},
	propTable:
	{
		width:"100%"		
	},
	propTableTd:
	{
		fontSize:'8pt',
		borderColor:'black',
		borderWidth:'1px',
		borderStyle:'solid',
		verticalAlign:'top'
	},
	propTableTh:
	{
		backgroundColor:'orange',
		color:"white",
		fontSize:'10pt',
		fontWeight:'bold',
		textAlign:"left",
		borderColor:'black',
		borderWidth:'1px',
		borderStyle:'solid'
	},
	titleTd1: 
	{
		color:"white"	
	},
	titleTd2: 
	{
		textAlign:"right"	
	},
	a:
	{
		color:'blue',		
		cursor:'pointer'		
	}, 
	tostring:
	{
		width:"500px",
		height:"3em",	
		overflow:'auto',
		verticalAlign:'top'				
	},
	name:
	{
		width:"200px",	
		height:"3em",		
		overflow:'auto'
	}
}
function print_r_initialize(ref)
{
	var what =  ref || prompt('Please type in an Object you would like to view. i.e. window, document', 'window');
	try 
	{
		if (eval(what))
		{			
			var t = new print_r(eval(what), what);
		}
		else
		{
			if (what)
			{
				alert (what + " does not seem to exist, please try again.");
				print_r_initialize();
			}
		}
	}
	catch (e) 
	{		
		alert (what + " a silly error occured. " + e);
	}
}


function print_r(o) {
   new print_rr(o,'print_r_object');
}
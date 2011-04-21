/********************************
* xPandMenu MULTI-LEVEL class
*********************************
* Javascript file
*********************************
* Patrick Brosset
* patrickbrosset@gmail.com
*********************************
* 02/2005
*********************************/


// Show / hide a sub-menu
function xMenuShowHide(obj)
{

	if(obj.style.display == 'none'){
		obj.style.display = 'block';
	}else{
		obj.style.display = 'none';
	}
	
}


// Toggle expanded / collapsed versions of items' images
function xSwapImg(imgDiv,srcImg,srcAltImg){

	if(imgDiv.src == srcImg){
		imgDiv.src = srcAltImg;
	}else{
		imgDiv.src = srcImg;
	}

}


// Restore the menu state when the page loads
function xRestoreState()
{
	//restore list state
	var name = "xMenuState";
	var start = document.cookie.indexOf(name+"=");
	if(start != -1)
	{
		var len = start+name.length+1;
		if ((!start) && (name != document.cookie.substring(0,name.length))) return null;
		if (start == -1) return null;
		var end = document.cookie.indexOf(";",len);
		if (end == -1) end = document.cookie.length;
		var value = unescape(document.cookie.substring(len,end));
		var values = value.split("|");
		for(i=0;i<values.length-1;i++)
		{
			var couple = values[i].split(":");
			document.getElementById(couple[0]).style.display = couple[1];
		}
	}
	//restore img state
	name = "xMenuStateImg";
	start = document.cookie.indexOf(name+"=");
	if(start != -1)
	{
		var len = start+name.length+1;
		if ((!start) && (name != document.cookie.substring(0,name.length))) return null;
		if (start == -1) return null;
		var end = document.cookie.indexOf(";",len);
		if (end == -1) end = document.cookie.length;
		var value = unescape(document.cookie.substring(len,end));
		var values = value.split("[]");
		for(i=0;i<values.length-1;i++)
		{
			var couple = values[i].split(">>");
			var imgs = couple[1].split(",");
			for(var il in imgs)
			{
				document.getElementById(imgs[il]).src = couple[0];
			}
		}
	}
}


// Save the menu state when the page unloads
function xSaveState()
{
	//Save list state
	var value = "";
	var myLists = document.getElementsByTagName("UL");
	for(i=0;i<myLists.length;i++)
	{
		if(myLists[i].className == "Xtree")	value += myLists[i].id + ":" + myLists[i].style.display + "|";
	}
	document.cookie = "xMenuState=" + escape(value) + ";";
	//save img state
	value = new Array();
	myLists = document.getElementsByTagName("IMG");
	for(i=0;i<myLists.length;i++)
	{
		if(myLists[i].id.substring(0,4) == "Ximg")
		{
			if(value[myLists[i].src]){value[myLists[i].src] += "," + myLists[i].id;}
			else{value[myLists[i].src] = myLists[i].id;}
		}
	}
	var str = "";
	for(var imgPath in value)
	{
		str += imgPath + ">>" + value[imgPath] + "[]";
	}
	var cook = str.substring(0,str.length-2);
	document.cookie = "xMenuStateImg=" + escape(cook) + ";";
}
/**
*
*  @author    Onasus <contact@onasus.com>
*  @copyright 2012-2015 Onasus www.onasus.com
*  @license   Refer to the terms of the license you bought at codecanyon.net
*/

function setGetParameter(paramName, paramValue)
{
    var url = window.location.href;
    if (url.indexOf(paramName + "=") >= 0)
    {
        var prefix = url.substring(0, url.indexOf(paramName));
        var suffix = url.substring(url.indexOf(paramName));
        suffix = suffix.substring(suffix.indexOf("=") + 1);
        suffix = (suffix.indexOf("&") >= 0) ? suffix.substring(suffix.indexOf("&")) : "";
        url = prefix + paramName + "=" + paramValue + suffix;
    }
    else
    {
    if (url.indexOf("?") < 0)
        url += "?" + paramName + "=" + paramValue;
    else
        url += "&" + paramName + "=" + paramValue;
    }
    if(window.location.href != url && window.location.href != url+'#')
		window.location.href = url;
}

function changeLanguage(obj) {
	// var src = obj.src;
	// document.getElementById('selectedLanguage').src = src;
	// var v = document.getElementById('languageID').value = obj.name;
	if(document.getElementById('languageID').value != obj.name)
		setGetParameter("seomngr_lang", obj.name);
}
function ShowQCMST() {
	if (document.getElementById('QCMSDesc').style.display == "none")
		document.getElementById('QCMSDesc').style.display = "block";
	else
		document.getElementById('QCMSDesc').style.display = "none";
}
function ShowQCategoryT() {

	if (document.getElementById('Qcategorytitle').style.display == "none")
		document.getElementById('Qcategorytitle').style.display = "block";
	else
		document.getElementById('Qcategorytitle').style.display = "none";

}
function ShowQIndexT() {
	if (document.getElementById('QIndexDesc').style.display == "none")
		document.getElementById('QIndexDesc').style.display = "block";
	else
		document.getElementById('QIndexDesc').style.display = "none";
}
function ShowQCategoryD() {
	if (document.getElementById('QcategoryDesc').style.display == "none") {
		document.getElementById('QcategoryDesc').style.display = "block";
	} else {
		document.getElementById('QcategoryDesc').style.display = "none";
	}

}
function ShowQManufactureT() {

	if (document.getElementById('QmanufactureDesc').style.display == "none") {
		document.getElementById('QmanufactureDesc').style.display = "block";
	} else {
		document.getElementById('QmanufactureDesc').style.display = "none";
	}

}
function ShowQProductT() {

	if (document.getElementById('QProductDesc').style.display == "none") {
		document.getElementById('QProductDesc').style.display = "block";
	} else {
		document.getElementById('QProductDesc').style.display = "none";
	}

}
function ShowQSearchT() {
	if (document.getElementById('QSearchDesc').style.display == "none") {
		document.getElementById('QSearchDesc').style.display = "block";
	} else {
		document.getElementById('QSearchDesc').style.display = "none";
	}
}
function Show(id){
	if (document.getElementById(id).style.display == "none") {
		document.getElementById(id).style.display = "block";		
	} else {
		document.getElementById(id).style.display = "none";
	}
}




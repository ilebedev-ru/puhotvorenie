var AZ = '  ¨                         ¸       ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏĞÑÒÓÔÕÖ×ØÙÚÛÜİŞßàáâãäåæçèéêëìíîïğñòóôõö÷øùúûüışÿ'
var b64s  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
var b64a  = b64s.split('');
var rusChars = new Array('À','Á','Â','Ã','Ä','Å','¨','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ğ','Ñ','Ò','Ó','Ô','Õ','Ö','×','Ø','Ù','Ú','Û','Ü','İ','Ş','ß','à','á','â','ã','ä','å','¸','æ','ç','è','é','ê','ë','ì','í','î','ï','ğ','ñ','ò','ó','ô','õ','ö','÷','ø','ù','ú','û','ü','ı','ş','\ÿ');
var transChars = new Array('A','B','V','G','D','E','YO','ZH','Z','I','J','K','L','M','N','O','P','R','S','T','U','F','H','C','CH','SH','SHCH','\`','Y','\'','E','YU','YA','a','b','v','g','d','e','yo','zh','z','i','j','k','l','m','n','o','p','r','s','t','u','f','h','c','ch','sh','shch','\`','y','\'','e','yu','ya');

function deBase64(str)
{
	while(str.substr(-1,1)=='=') str=str.substr(0,str.length-1);
	var b=str.split(''), i;
	var s=Array(), t;
	var lPos = b.length - b.length % 4;
	for(i=0;i<lPos;i+=4)
	{
		t=(b64s.indexOf(b[i])<<18)+(b64s.indexOf(b[i+1])<<12)+(b64s.indexOf(b[i+2])<<6)+b64s.indexOf(b[i+3]);
		s.push( ((t>>16)&0xff), ((t>>8)&0xff), (t&0xff) );
	}
	if( (b.length-lPos) == 2 ){ t=(b64s.indexOf(b[lPos])<<18)+(b64s.indexOf(b[lPos+1])<<12); s.push( ((t>>16)&0xff)); }
	if( (b.length-lPos) == 3 ){ t=(b64s.indexOf(b[lPos])<<18)+(b64s.indexOf(b[lPos+1])<<12)+(b64s.indexOf(b[lPos+2])<<6); s.push( ((t>>16)&0xff), ((t>>8)&0xff) ); }
	for( i=s.length-1; i>=0; i-- )
	{
		if( s[i]>=168 ) s[i]=AZ.charAt(s[i]-163)
		else s[i]=String.fromCharCode(s[i])
	}
	return s.join('');
}

function getVarValueFromURL(url, varName) {
	var query = url.substring(url.indexOf('?') + 1);
	var vars = query.split("&");
	for (var i=0;i<vars.length;i++) {
		var pair = vars[i].split("=");
		if (pair[0] == varName) {
			return pair[1];
		}
	}
	return null;
} 

function urlDecode(str)
{
	reg1=/(%[Dd][01]%[89ABab][0-9A-Fa-f])/g
	reg2=/(%[0-9A-Fa-f]{2})/g
	reg3=/(%[Dd][01]%[89ABab][0-9A-Fa-f]%[Dd][01]%[89ABab][0-9A-Fa-f]%[Dd][01]%[89ABab][0-9A-Fa-f])/g // 3 symbols utf8
	if(str.match(reg3)) return Url.decode(str);

	len=str.length;
	encoded="";
	for(i=0;i<len;i++)
	{
		if(str[i]=='%')
		{
			hex=str.substring(i+1,i+3);
			code=hexb.indexOf(hex[0])*16+hexb.indexOf(hex[1]);
			if(code>=128) code+=848;
			encoded+=String.fromCharCode(code);
			i=i+2;
		}
		else encoded+=str[i];
	}
	return encoded;
}

/**
*
*  URL encode / decode
*  http://www.webtoolkit.info/
*
**/

var Url = {

    // public method for url encoding
    encode : function (string) {
        return escape(this._utf8_encode(string));
    },

    // public method for url decoding
    decode : function (string) {
        return this._utf8_decode(unescape(string));
    },

    // private method for UTF-8 encoding
    _utf8_encode : function (string) {
        string = string.replace(/\r\n/g,"\n");
        var utftext = "";

        for (var n = 0; n < string.length; n++) {

            var c = string.charCodeAt(n);

            if (c < 128) {
                utftext += String.fromCharCode(c);
            }
            else if((c > 127) && (c < 2048)) {
                utftext += String.fromCharCode((c >> 6) | 192);
                utftext += String.fromCharCode((c & 63) | 128);
            }
            else {
                utftext += String.fromCharCode((c >> 12) | 224);
                utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                utftext += String.fromCharCode((c & 63) | 128);
            }

        }

        return utftext;
    },

    // private method for UTF-8 decoding
    _utf8_decode : function (utftext) {
        var string = "";
        var i = 0;
        var c = c1 = c2 = 0;

        while ( i < utftext.length ) {

            c = utftext.charCodeAt(i);

            if (c < 128) {
                string += String.fromCharCode(c);
                i++;
            }
            else if((c > 191) && (c < 224)) {
                c2 = utftext.charCodeAt(i+1);
                string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                i += 2;
            }
            else {
                c2 = utftext.charCodeAt(i+1);
                c3 = utftext.charCodeAt(i+2);
                string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                i += 3;
            }

        }

        return string;
    }

}

function translit(from){
  var to = new String();
  var len = from.length;
  var character, isRus;
  for(i=0; i < len; i++){
    character = from.charAt(i,1);
    isRus = false;
    for(j=0; j < rusChars.length; j++){
      if(character == rusChars[j]){
        isRus = true;
        break;
      }
    }
    to += (isRus) ? transChars[j] : character;
  }
  return to;
}

openstat_param=new Array();
openstat=getVarValueFromURL(document.location.href, '_openstat');
openstat_tag=openstat;
campaign=getVarValueFromURL(document.location.href, 'utm_campaign');
if(openstat&&!campaign) {
	var camp=camp||[];
	var ban=ban||[];
	url=location.href.replace("?_openstat="+openstat,'')
	url=location.href.replace("_openstat="+openstat,'')
	utm_medium='cpc';
	openstat=deBase64(openstat);
	openstat_param=openstat.split(';');
	if(openstat_param[0]=='direct.yandex.ru'&&openstat_param[3].indexOf('yandex.ru')>-1) openstat_param[3]=openstat_param[3]+":"+translit(Url.decode(getVarValueFromURL(document.referrer, 'text')));

	if(camp[openstat_param[1]]) openstat_param[1]=camp[openstat_param[1]];
	if(ban[openstat_param[2]]) openstat_param[2]=ban[openstat_param[2]];
	extra="utm_campaign="+openstat_param[1]+"&utm_medium="+utm_medium+"&utm_source="+openstat_param[0]+"&utm_content="+openstat_param[2]+"&utm_term="+openstat_param[3]+"&_openstat="+openstat_tag;
	if(url.indexOf('?')) location.href=url+"&"+extra;
	else location.href=url+"?"+extra;
}




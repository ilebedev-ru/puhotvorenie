
var block_id = '.banner-box';
var top_offset = 0;
var tid = null;
function setCookie(name, value, expires, path, domain, secure) {
  document.cookie = name + "=" + escape(value) +
  ((expires) ? "; expires=" + expires : "") +
  ((path) ? "; path=" + path : "") +
  ((domain) ? "; domain=" + domain : "") +
  ((secure) ? "; secure" : "");
}
function getCookie(name) {
  var cookie = " " + document.cookie;
  var search = " " + name + "=";
  var setStr = null;
  var offset = 0;
  var end = 0;
  if (cookie.length > 0) {
    offset = cookie.indexOf(search);
    if (offset != -1) {
      offset += search.length;
      end = cookie.indexOf(";", offset)
      if (end == -1) {
        end = cookie.length;
      }
      setStr = unescape(cookie.substring(offset, end));
    }
  }
  return(setStr);
}
function blockPop() {
  $(block_id).fadeOut("normal");
  $(window).unbind("scroll");
  setCookie('pop', '1');
}
document.onkeypress = func;
function func(event) {
  if (window.event) event = window.event;
  var key = event.keyCode ? event.keyCode : event.which ? event.which : null;
  if( (event.ctrlKey) && ( (key == 0xA) || (key == 0xD) ) ) {
    setCookie('pop', '');
    showPop();
  }
}
function showPop() {
  var pop = getCookie('pop');
  if (pop) return;
  setTimeout(function() {
    var win_scrl = $(document).scrollTop();
    var top = parseInt(top_offset + win_scrl);
    $(block_id).css('top', top + 'px').fadeIn("normal", function() {
      $(block_id + ' #mask').unbind("click").click(function() {
        blockPop();
      });
      $(window).scroll(function() {
        win_scrl = $(document).scrollTop();
        top = parseInt(top_offset + win_scrl);
        $(block_id).css('top', top + 'px');
      });
    });
  }, 15000);
}




$(document).ready(function() {
  showPop();
  var winH = $(window).height();
  var winW = $(window).width();
  $('.banner').css('top',  winH/2-$('.banner').height()/2);
  $('.banner').css('left', winW/2-$('.banner').width()/2);
  
  $("#coupon_form").live("submit", function() {
	var url = "/backcoupon.php";
	$.ajax({
		   type: "POST",
		   url: url,
		   data: $(this).serialize(),
		   success: function(data)
		   {
			   if(data == "1") $('#coupon_form').html('<p class="success">Купон отправлен на почту.</p>'); // show response from the php script.
			   else alert(data);
			   setCookie('pop', '1');
		   }
		 });

	return false; 
  });
   $(".new .sbm").live("click", function() {
	var url = "/backcoupon.php";
	$.ajax({
		   type: "POST",
		   url: url,
		   data: $("#coupon_form").serialize(),
		   success: function(data)
		   {
			   if(data == "2") $('#coupon_form').html('<p class="success">Купон активирован!</p>'); // show response from the php script.
			   else alert(data);
			   setCookie('pop', '1');
		   }
		 });

	return false; 
  });
});


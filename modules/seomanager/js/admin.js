/**
*
*  @author    Onasus <contact@onasus.com>
*  @copyright 20012-2015 Onasus www.onasus.com
*  @license   Refer to the terms of the license you bought at codecanyon.net
*/


$(function(){
	$("button.shortcodeadder").on("click",function(e){
		adderId = $(this).attr("id");
		selectorId = adderId.split('-')[0]+"-shortcodeselector-"+adderId.split('-')[2];
		metaInputId = adderId.split('-')[2]+adderId.split('-')[0]+"meta";
		metaInputVal = $("#"+metaInputId).val();
		metaAddedVal = $("#"+selectorId).val();
		$("#"+selectorId).val("");
		if(metaAddedVal != "" && metaInputVal.indexOf(metaAddedVal) === -1){
			if(metaInputVal == "")
				$("#"+metaInputId).val(metaAddedVal);
			else
				$("#"+metaInputId).val(metaInputVal+'-'+metaAddedVal);
		}
		updateShortcodeTags(metaInputId);
		// alert(metaInputId);
	})
	$("button.shortcodereset").on("click",function(e){
		resetId = $(this).attr("id");
		metaInputId = resetId.split('-')[2]+resetId.split('-')[0]+"meta";
		$("#"+metaInputId).val("");
		updateShortcodeTags(metaInputId);
	})
	
	removeThisShortcode = function(object){
		metaInputId = object.attr("data-metainput");
		metaRemoved = object.attr("data-removed");
		metaInputVal = $("#"+metaInputId).val();
		if(metaRemoved != "" && metaInputVal.indexOf(metaRemoved) !== -1){
			metaTab = metaInputVal.split('-');
			metaInputVal = "";
			$.each(metaTab,function(i,val){
				if(val != "" && val != "-" && val != metaRemoved){
					if(metaInputVal == "")
						metaInputVal = val;
					else
						metaInputVal = metaInputVal+'-'+val;
				}
			})
			$("#"+metaInputId).val(metaInputVal);
			$("select[data-metainput='"+metaInputId+"'] option[Value='" + metaRemoved + "']").removeAttr("disabled").css('color','#000');
			object.parent().parent().remove();
		}
		// updateShortcodeTags(metaInputId);
	};
	
	updateShortcodeTags = function(inputId){
		metaInputVal = $("#"+inputId).val();
		metaInputVal = metaInputVal.split('-');
		$("#"+inputId+"-tags").empty();
		$("select[data-metainput='"+inputId+"'] option").removeAttr("disabled").css('color','#000');
		for(i = 0; i < metaInputVal.length; i++){
			// console.log();
			metaInputVal[i] = metaInputVal[i].trim();
			$("select[data-metainput='"+inputId+"'] option[Value='" + metaInputVal[i] + "']").prop("disabled","disabled").css('color','#eee');
			if(metaInputVal[i] != ''){
				$("#"+inputId+"-tags").append('<span class="badge" style="margin:2px;">'+metaInputVal[i]+'<span class="btn" style="padding:0 0 0 5px;"><b data-metainput="'+inputId+'" data-removed="'+metaInputVal[i]+'" onclick="removeThisShortcode($(this));" style="font-family:arial;">x</b></span></span>');
			}
		}
	}
	
	updateShortcodeTags("categorytitlemeta");
	updateShortcodeTags("categorydescmeta");
	updateShortcodeTags("cmstitlemeta");
	updateShortcodeTags("cmsdescmeta");
	updateShortcodeTags("indextitlemeta");
	updateShortcodeTags("indexdescmeta");
	updateShortcodeTags("manufacturetitlemeta");
	updateShortcodeTags("manufacturedescmeta");
	updateShortcodeTags("producttitlemeta");
	updateShortcodeTags("productdescmeta");
	updateShortcodeTags("searchtitlemeta");
	updateShortcodeTags("searchdescmeta");
	updateShortcodeTags("supplytitlemeta");
	updateShortcodeTags("supplydescmeta");
	updateShortcodeTags("contactustitlemeta");
	updateShortcodeTags("contactusdescmeta");
})


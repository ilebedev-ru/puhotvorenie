
$(document).ready(function () {
	$('body').on('submit', '.seoform', function() {
		var $form = $(this);
		$.ajax({
			type: 'POST',
			url: baseDir + 'modules/pwseo/controllers/front/index.php',
			dataType: 'json',
			data: $form.serialize(),
			success: function(data) {
				if(data.success === true) {
					showMessage('Данные успешно сохранены', $form);
				}
				else {
					if(typeof(data.message) !== 'undefined') {
						showMessage(data.message, $form);
					}
					else {
						showMessage('Неизвестаня ошибка',$form);
					}
				}
			},
			error: function() {
				showMessage('Возникла ошибка - сервис недоступен', $form);
			}
		});

		return false;
	});
    $('#pwseo_meta_edit').fancybox();
    $('#pwseo_desc_edit').fancybox();
    $('#pwseo_editlink').click(function(){
        $('#pwseo_menu').toggle();
        return false;
    });
});

function showMessage(message, form) {
    var container = form.parent();
    container.find('.pwseo_success').remove();
    container.prepend('<div class="pwseo_success">' + message + '</div>');
}

$(function(){
   $('#pwseo_status_edit').click(function(e){
        e.preventDefault();
        pwseo.toogleStatus($(this), $(this).attr('data-seoredirect'));
   });
    $('.edit-description.button').click(function(e){
        e.preventDefault();
        $('.edit-description').fadeIn();
    });
    $('#pwseo_slide').click(function(){
        $('#pwseo').slideToggle();
    });

});

$(document).keyup(function(e)
{
	var key=[];
	key["c"]=67;
	key["b"]=66;
	key["v"]=86;
	key["f"]=70;
	key["r"]=82;
	key["space"]=32;
	key["enter"]=13;
    if (e.keyCode==key["c"] && (e.altKey))
    {
        console.log('pwseo open');
        $("#pwseo_slide").click();
        return false;
    }
    if (e.keyCode==key["b"] && (e.altKey))
    {
        $('#pwseo_desc_edit').click()
        return false;
    }
    if (e.keyCode==key["v"] && (e.altKey))
    {
        $('#pwseo_meta_edit').click()
        return false;
    }
});
var pwseo = {
    entity: 'product',
    id: '1',

    toogleStatus: function(elem, redirect){
        // console.log(this.entity);
        // console.log(this.id);
        // return;
        // $.ajax(baseDir + 'modules/pwseo/controllers/front/index.php', + '?action=toogleStatus&entity='+this.entity+'&id='+this.id).done(function() {
            // elem.toggleClass( "done" );
            // if (redirect !== 'undefined')
                // location.href = redirect;
        // });
        $form = $('.seoform');
        var id_item = $form.find('input[name=id_item]').val();
        var editor_name = $form.find('input[name=editor_name]').val();
        var id_entity = $form.find('input[name=id_entity]').val();
        if (!id_item) {
            var i = 0;
            $form.find('[name="entity[]"]').each(function(){
                if ($(this).val() == id_entity) {
                    $id_item = $form.find('[name="entityId[]"]')[i];
                }
                i++;
            });
        }
        $.ajax({
            type: 'POST',
            url: baseDir + 'modules/pwseo/controllers/front/index.php',
            dataType: 'json',
            data: "action=toogleStatus&id_entity=" + id_entity + "&editor_name=" + editor_name + "&id_item=" + id_item,
            success: function(data) {
                if(data.success === true) {
                    showMessage('Данные успешно сохранены');
                }
                else {
                    if(typeof(data.message) !== 'undefined') {
                        showMessage(data.message);
                    }
                    else {
                        showMessage('Неизвестаня ошибка');
                    }
                }
            },
            error: function() {
                showMessage('Возникла ошибка - сервис недоступен');
            }
        });

        return false;

    }

};



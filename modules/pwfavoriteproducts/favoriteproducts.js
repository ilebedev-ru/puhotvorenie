$(document).ready(function(){
    $('body')
    .on('click', '#pwfavoriteAdd', function(e){

        e.preventDefault();
        $.ajax({
			url: favorite_products_url_add + '&rand=' + new Date().getTime(),
			type: "POST",
			headers: { "cache-control": "no-cache" },
			data: {
				"id_product": favorite_products_id_product
			},
			success: function(result){
				if (result == '0')
				{
			    	$('#pwfavoriteAdd').prop('id', 'pwfavoriteRemove');

				}
		 	}
		});
		yaCounter22742683.reachGoal('addToWishlist');
		return true;
    })
    .on('click', '#pwfavoriteRemove', function(e){
        e.preventDefault();
        $.ajax({
			url: favorite_products_url_remove + '&rand=' + new Date().getTime(),
			type: "POST",
			headers: { "cache-control": "no-cache" },
			data: {
				"id_product": favorite_products_id_product
			},
			success: function(result){
				if (result == '0')
				{
			    	$('#pwfavoriteRemove').prop('id', 'pwfavoriteAdd');
				}
		 	}
		});
    });

	$('[rel^=ajax_id_favoriteproduct_]').click(function(e)
	{
		e.preventDefault();
		var idFavoriteProduct =  $(this).attr('rel').replace('ajax_id_favoriteproduct_', '');
		var i = $(this).find('i');
        var action = favorite_products_url_add;
        if(i.hasClass('fa-heart'))
            action = favorite_products_url_remove;

		$.ajax({
			url: action,
			type: "POST",
			data: {
				'id_product': idFavoriteProduct,
				'ajax': true
			},
			success: function(result)
			{
				if (result == '0')
				{
					i.toggleClass('fa-heart').toggleClass('fa-heart-o');
				}
 		 	}
		});
	});
});
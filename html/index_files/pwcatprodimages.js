$(document).ready(function(){
	// $()
	$('#center_column')
		.on('hover', '.product-container div.image', function(){
			$images = $(this).find('img.catprodimg');
			rand = Math.floor(Math.random()*10) % $images.length;
			if($images.length){
				$($images[rand]).addClass('active');
				// $($images[rand]).fadeTo(1, 1);
				$($images[rand]).css('opacity', 1);
			}
		}).on('mouseleave', '.product-container div.image', function(){
			// $(this).find('img.catprodimg.active').fadeTo(1, 0).removeClass('active');
			$(this).find('img.catprodimg.active').css('opacity', 0).removeClass('active');
		});
});
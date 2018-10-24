$(document).ready(function(){

	$(".header-change-city-link").on("click", function(e){

        e.preventDefault();

		$(".city_popup").show();

		$(".city_popup").removeClass("bounceOutUp");

		$(".city_popup").addClass("bounceInDown");

		$(".city_overlay").show();

	});

    $('.city_overlay').on('click', function(){

        $(".city_popup .close").trigger('click');

    });

	$(".city_popup .close").on("click", function(){

		$(".city_popup").removeClass("bounceInDown");

		$(".city_popup").addClass("bounceOutUp");

		setTimeout(function(){

			$(".city_overlay").hide();

		}, 600);

	});



	$(".more_cities span").on("click", function(){

		var self = $(this);

		if(self.hasClass("active")){

			$(".more_cities_box").hide();

			self.removeClass("active");

		}else{

			$(".more_cities_box").slideDown(function(){

				self.addClass("active");				

			});

		}

	});

	$(".delivery_condition .get-info").on("click", function(){

		var self = $(this);

		if(self.hasClass("active")){

			$(".header-change-city .pwpopup").slideUp();

			self.removeClass("active");

		}else{

			$(".header-change-city .pwpopup").slideDown(function(){

				self.addClass("active");

				var timer = null;

				var element = document.getElementById('pwpopup');

				element.onmouseout = function () {

					timer = setTimeout(function () {

						$('#pwpopup').hide();

						self.removeClass("active");

					}, 1200);

				}

				element.onmouseover = function () {

					clearTimeout(timer);

				}



			});



		}

	});

	

	//Changing city

	$(".pop_cities li span").on("click", function(){

		var self = $(this);

		$.ajax({

		  type: 'POST',

		  url: baseDir + 'modules/pwblockusercity/ajax.php',

		  data: 'method=changeCity&city=' + encodeURIComponent($(this).html()),

		  dataType: 'json',

		  success: function(data) {

		  	if(data.delivery){

				$("#input_city").val(self.html());

			  	$(".city_popup").removeClass("bounceInDown");

					$(".city_popup").addClass("bounceOutUp");

					setTimeout(function(){

						$(".city_overlay").hide();

					}, 600);

				$(".header-change-city-link").html(self.html());

				$('.condition-info').html(data.delivery);

		  	}  

		  }

		});

	});

	

	$("#submit_city").on("click", function(){

		if($("#input_city").val().length > 1){

			$("#input_city").removeClass("empty_input");

			var self = $("#input_city");

			$.ajax({

			  type: 'POST',

			  url: baseDir + 'modules/pwblockusercity/ajax.php',

			  data: 'method=changeCity&city=' + encodeURIComponent(self.val()),

			  dataType: 'json',

			  success: function(data) {

			  	if(data === "true"){

				  	$(".city_popup").removeClass("bounceInDown");

					$(".city_popup").addClass("bounceOutUp");

					setTimeout(function(){

						$(".city_overlay").hide();

					}, 600);

					$(".header-change-city-link").html(self.val());

			  	}  

			  }

			});

		}else{

			$("#input_city").addClass("empty_input");

		}

	});



    $("#input_city")

        .autocomplete(

            '/modules/pwblockusercity/ajax.php', {

                minChars: 3,

                width: 330,

                selectFirst: false,

                scroll: true,

                dataType: "json",

                formatItem: function(data, i, max, value, term) {

                    $(".ac_results").addClass("city_autocomplete");

                    return value;

                },

                parse: function(data) {

                    var mytab = new Array();

                    for (var i = 0; i < data.length; i++){

                        mytab[mytab.length] = { data: data[i], value: data[i]};

                    }

                    return mytab;

                }

            })

        .result(function(event, data, formatted) { 

                $('#input_city').val(data);

            });

});
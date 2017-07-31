(function($){
	var purchase_code = '';

	function getTitlePoints(title, titleClass, setTips = false){
		$.ajax({
			type: "POST",
			url: ritle.api_url,
			data: {
				title: title,
				purchaseCode: ritle.purchase_code
			},
			statusCode: {
			      200: function (response) {
			         alert('1');
			      },
			      201: function (response) {
			         alert('1');
			      },
			      400: function (response) {
			         $('#ritle-message').css('display', 'block').html(JSON.parse(response.responseText).message);
			      },
			      404: function (response) {
			         alert('1');
			      }
			}, success: function(response){
				var points = response.points;
				$('#'+titleClass+' .c100').removeClass().addClass('c100 p'+points);
				$('#'+titleClass+' .points').html(points);

				if(setTips){
					$('#'+titleClass+' .tips').html('');
					$.each(response.tips, function(index, el){
						$('#'+titleClass+' .tips').append('<div class="cornice"><p><span class="text-font">Suggerimento: </span>'+el+'</p></div>')
					});
				}
			}
		});
	}

	$(document).on('change keyup', '#title', function(e){
		getTitlePoints($(this).val(), 'original-title', true);
	});

	$(document).on('change keyup', '#subtitle', function(e){
		getTitlePoints($(this).val(), 'alternative-title', true);
	});

	$(document).ready(function(){
		getTitlePoints($('#title').val(), 'original-title', true);
	});

})(jQuery);
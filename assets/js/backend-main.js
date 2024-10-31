jQuery(document).ready(function($){

	if($("input").is("#woocommerce_send24_shipping_postdanmark_price") == true){
		valid_price('#woocommerce_send24_shipping_postdanmark_price');	
	}
	if($("input").is("#woocommerce_send24_shipping_gls_price") == true){
		valid_price('#woocommerce_send24_shipping_gls_price');
	}
	if($("input").is("#woocommerce_send24_shipping_ups_price") == true){
		valid_price('#woocommerce_send24_shipping_ups_price');
	}
	if($("input").is("#woocommerce_send24_shipping_dhl_price") == true){
		valid_price('#woocommerce_send24_shipping_dhl_price');
	}
	if($("input").is("#woocommerce_send24_shipping_tnt_price") == true){
		valid_price('#woocommerce_send24_shipping_tnt_price');
	}
	if($("input").is("#woocommerce_send24_shipping_bring_price") == true){
		valid_price('#woocommerce_send24_shipping_bring_price');
	}

	function valid_price(id){
		$(id)[0].onkeypress = function(e) {
		  e = e || event;
		  if (e.ctrlKey || e.altKey || e.metaKey) return;
		  var chr = getChar(e);
		  if (chr == null) return;
		  if (chr < '0' || chr > '9') {
		    return false;
		  }
		}
	}

	function getChar(event) {
      if (event.which == null) {
        if (event.keyCode < 32) return null;
        return String.fromCharCode(event.keyCode) 
      }

      if (event.which != 0 && event.charCode != 0) {
        if (event.which < 32) return null;
        return String.fromCharCode(event.which) 
      }

      return null; 
    }

	// Default check.
	var enable_postdanmark = $('#woocommerce_send24_shipping_enable_postdanmark').val();
	if(enable_postdanmark == 'no'){
		$('#woocommerce_send24_shipping_postdanmark_price').parent().parent().parent().hide();
	}else{
		$('#woocommerce_send24_shipping_postdanmark_price').parent().parent().parent().show();
	}

	var enable_gls = $('#woocommerce_send24_shipping_enable_gls').val();
	if(enable_gls == 'no'){
		$('#woocommerce_send24_shipping_gls_price').parent().parent().parent().hide();
	}else{
		$('#woocommerce_send24_shipping_gls_price').parent().parent().parent().show();
	}	

	var enable_ups = $('#woocommerce_send24_shipping_enable_ups').val();
	if(enable_ups == 'no'){
		$('#woocommerce_send24_shipping_ups_price').parent().parent().parent().hide();
	}else{
		$('#woocommerce_send24_shipping_ups_price').parent().parent().parent().show();
	}	

	var enable_dhl = $('#woocommerce_send24_shipping_enable_dhl').val();
	if(enable_dhl == 'no'){
		$('#woocommerce_send24_shipping_dhl_price').parent().parent().parent().hide();
	}else{
		$('#woocommerce_send24_shipping_dhl_price').parent().parent().parent().show();
	}	

	var enable_tnt = $('#woocommerce_send24_shipping_enable_tnt').val();
	if(enable_tnt == 'no'){
		$('#woocommerce_send24_shipping_tnt_price').parent().parent().parent().hide();
	}else{
		$('#woocommerce_send24_shipping_tnt_price').parent().parent().parent().show();
	}	

	var enable_bring = $('#woocommerce_send24_shipping_enable_bring').val();
	if(enable_bring == 'no'){
		$('#woocommerce_send24_shipping_bring_price').parent().parent().parent().hide();
	}else{
		$('#woocommerce_send24_shipping_bring_price').parent().parent().parent().show();
	}

	// Select show/hode price for distributors.
	$('#woocommerce_send24_shipping_enable_postdanmark').change(function(){
		if($('#woocommerce_send24_shipping_enable_postdanmark').val() == 'no'){
			$('#woocommerce_send24_shipping_postdanmark_price').parent().parent().parent().hide();
		}else{
			$('#woocommerce_send24_shipping_postdanmark_price').parent().parent().parent().show();
		}
	});

	$('#woocommerce_send24_shipping_enable_gls').change(function(){
		if($('#woocommerce_send24_shipping_enable_gls').val() == 'no'){
			$('#woocommerce_send24_shipping_gls_price').parent().parent().parent().hide();
		}else{
			$('#woocommerce_send24_shipping_gls_price').parent().parent().parent().show();
		}
	});

	$('#woocommerce_send24_shipping_enable_ups').change(function(){
		if($('#woocommerce_send24_shipping_enable_ups').val() == 'no'){
			$('#woocommerce_send24_shipping_ups_price').parent().parent().parent().hide();
		}else{
			$('#woocommerce_send24_shipping_ups_price').parent().parent().parent().show();
		}
	});	

	$('#woocommerce_send24_shipping_enable_dhl').change(function(){
		if($('#woocommerce_send24_shipping_enable_dhl').val() == 'no'){
			$('#woocommerce_send24_shipping_dhl_price').parent().parent().parent().hide();
		}else{
			$('#woocommerce_send24_shipping_dhl_price').parent().parent().parent().show();
		}
	});	

	$('#woocommerce_send24_shipping_enable_tnt').change(function(){
		if($('#woocommerce_send24_shipping_enable_tnt').val() == 'no'){
			$('#woocommerce_send24_shipping_tnt_price').parent().parent().parent().hide();
		}else{
			$('#woocommerce_send24_shipping_tnt_price').parent().parent().parent().show();
		}
	});	

	$('#woocommerce_send24_shipping_enable_bring').change(function(){
		if($('#woocommerce_send24_shipping_enable_bring').val() == 'no'){
			$('#woocommerce_send24_shipping_bring_price').parent().parent().parent().hide();
		}else{
			$('#woocommerce_send24_shipping_bring_price').parent().parent().parent().show();
		}
	});

	// Check postcode.
	$('#woocommerce_send24_shipping_zip').focusout(function(e) {

		e.preventDefault();
		var value = $(this).val();
		var data = {
			'action': 'check_postcode',
			'zip': value
		};

		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: data,
			success: function (response) {
				 if (response == 'true') {
					$('.p_send24_success').remove();
					$('.p_send24_error').remove();
					$('#woocommerce_send24_shipping_zip').after('<p class="p_send24_success">success</p>');
					$('#woocommerce_send24_shipping_c_key').after('<p class="p_send24_success">success</p>');
					$('#woocommerce_send24_shipping_c_secret').after('<p class="p_send24_success">success</p>');

					setTimeout(function(){
						$('.p_send24_success').remove();
					}, 5000)
				 }else{
				 	$('.p_send24_success').remove();
					$('.p_send24_error').remove();
				 	data = JSON.parse(response);
				 	if (data == "") {
				 		$('#woocommerce_send24_shipping_zip').after('<p class="p_send24_error">invalid</p>');
				 	}else{
				 		$('#woocommerce_send24_shipping_c_key').after('<p class="p_send24_error">invalid</p>');
						$('#woocommerce_send24_shipping_c_secret').after('<p class="p_send24_error">invalid</p>');
				 	}
				 }
			}
		})
	});

});
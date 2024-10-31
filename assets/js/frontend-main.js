jQuery(document).ready(function($){
		
	$('#shipping_postcode, #shipping_city, #shipping_address_1').focusout(function() {

		var country = $('#select2-chosen-2').text();
		var city = $('#shipping_city').val();
		var address = $('#shipping_address_1').val();
		var postcode = $('#shipping_postcode').val();

		var data = {
			'action': 'show_shops',
			'country': country,
			'city': city,
			'address': address,
			'postcode': postcode,
		};
		
		$.ajax({
			url: MyAjax.ajaxurl,
			type: 'POST',
			data: data,
			success: function (response) {
				$( 'body' ).trigger( 'update_checkout' );
			}
		})
	});

	// Save billing_email in session(for no login user).
	$('#billing_email').focusout(function() {
		var email = $(this).val();
		var data = {
			'action': 'save_billing_email',
			'billing_email': email
		};
		
		$.ajax({
			url: MyAjax.ajaxurl,
			type: 'POST',
			data: data,
			success: function (response) {
				$( 'body' ).trigger( 'update_checkout' );
			}
		})
	});

	// Click and init map.
	$('#send24_map').live('click' ,function(){
		var map_value = $('#send24_map').attr('rel');
		// var shops = $('#send24_map').attr('data-id-shops');
		 $('#send24-popup-map').append('<script> coordinates = '+map_value+';</script>');
		 initMap();
	});

	// Select shops.
	$('#send24_select_shops').live('change' ,function(){
		var id = $(this).val();
        document.cookie = 'selected_shop_id='+id;	
	});

	var map;
    function initMap() {
	    if (coordinates != "") {
		  var start_coordinates = Math.round(coordinates.length/2);
	      var marker = Array();
	      map = new google.maps.Map(document.getElementById('map'), {
	        center: {lat: Number(coordinates[start_coordinates].lat), lng: Number(coordinates[start_coordinates].lng)},
	          zoom: 12
	      });

	      var default_id_distance = 0;
	      for (var i = 0; i < coordinates.length; i++){                          
	        var infowindow = new google.maps.InfoWindow;
	        infowindow.setContent('<b>'+coordinates[i].title+'</b>');
	        marker[i] = new google.maps.Marker({
	            map: map, 
	            position: {lat: Number(coordinates[i].lat), lng: Number(coordinates[i].lng)},
	            id_marker: coordinates[i].id,
	            distance: coordinates[i].distance,
	        });

	        // Default value distance for first pickup.
	        if(default_shop_id == coordinates[i].id){
	          default_id_distance = coordinates[i].distance;
	        }
	        
	        // Marker click.
        	marker[i].addListener('click', function() {
	            var id = this.id_marker;
	            console.log(id);
	    		var distance = parseInt(this.distance*100)/100;
	   			var shop = shops_list[id];
				    var monday = '<tr><td>Monday</td><td>'+shop.opening_week.Monday.start+'-'+shop.opening_week.Monday.end+'</td></tr>';
	            var tuesday = '<tr><td>Tuesday</td><td>'+shop.opening_week.Tuesday.start+'-'+shop.opening_week.Tuesday.end+'</td></tr>';
	            var wednesday = '<tr><td>Wednesday</td><td>'+shop.opening_week.Wednesday.start+'-'+shop.opening_week.Wednesday.end+'</td></tr>';
	            var thursday = '<tr><td>Thursday</td><td>'+shop.opening_week.Thursday.start+'-'+shop.opening_week.Thursday.end+'</td></tr>';
	            var friday = '<tr><td>Friday</td><td>'+shop.opening_week.Friday.start+'-'+shop.opening_week.Friday.end+'</td></tr>';
	            if(shop.opening_week.Saturday != null){
	              var saturday = '<tr><td>Saturday</td><td>'+shop.opening_week.Saturday.start+'-'+shop.opening_week.Saturday.end+'</td></tr>';
	            }else{
	              var saturday = '<tr><td>Saturday</td><td>-</td></tr>';
	            }
	            if(shop.opening_week.Sunday != null){
	              var sunday = '<tr><td>Sunday</td><td>'+shop.opening_week.Sunday.start+'-'+shop.opening_week.Sunday.end+'</td></tr>';
	            }else{
	              var sunday = '<tr><td>Sunday</td><td>-</td></tr>';
	            }
	            var distance_result = '<p id="send24_distance">Distance: '+distance+' meter</p>';
	            jQuery('#send24_info_map').html('<h3 id="popup_h3">Addresse</h3><p>Shop: '+shop.shop_title+'</p>'+distance_result+'<p id="step_1info_map">'+shop.shop_location+'</p>');
	            jQuery('#step_1info_map').after('<h3 id="popup_h3">Abningstider</h3><table id="popup_table">'+monday+''+tuesday+''+wednesday+''+thursday+''+friday+''+saturday+''+sunday+'</table>');
	            var rating = shops_list[id];
	            if(rating.user_login != null){
	              if(rating.rating['0'] != 'none'){
	                    var avatar = rating.user_avatar;
	                    var rating_html = '<div id="rating_send24"><div id="rating_avatar_send24">'+avatar+'<br><div id="rating_name_user">'+rating.user_login+'</div></div><div id="rating_service_name">'+rating.category+'<br><span class="send24_rating_stars"><span class="stars-rating">';  
	                    // var rating_html = '';
	                    for (var i = 0; i < Math.ceil(rating.rating); i++) {
	                      rating_html += '<span class="dashicons dashicons-star-filled"></span>';
	                    };
	                    if(i <= 5){
	                      var c = 5-i;
	                      for (var i = 0; i < c; i++) {
	                        rating_html += '<span class="dashicons dashicons-star-empty"></span>';
	                      };
	                    }
	                    rating_html += '</span><span class="send24_rating_average">'+rating.rating+'</span></span></div></div>  ';
	                    jQuery('#step_1info_map').after(rating_html);
	                    jQuery('#step_1info_map').after('<h3 class="bestyrer_h3">Bestyrer</h3>');
	              }
	            }
	            jQuery('#send24_selected_shop').html('Selected shop: <b style="color: #4E8FFD;">'+shop.shop_title+'</b>');
	            jQuery('#shop_selected').html(shop.shop_title);
	            $('#send24_map').html('change shop');
				$('.send34_map_selected').html('<span style="font-size: 12px;">Selected: <b style="color: #4E8FFD;">'+shop.shop_title+'</b></span>');
	            document.cookie = 'selected_shop_id='+id;						
			});
			// Default shop.
			var shop = shops_list[default_shop_id];
	        var distance = parseInt(default_id_distance*100)/100;
	        var monday = '<tr><td>Monday</td><td>'+shop.opening_week.Monday.start+'-'+shop.opening_week.Monday.end+'</td></tr>';
	        var tuesday = '<tr><td>Tuesday</td><td>'+shop.opening_week.Tuesday.start+'-'+shop.opening_week.Tuesday.end+'</td></tr>';
	        var wednesday = '<tr><td>Wednesday</td><td>'+shop.opening_week.Wednesday.start+'-'+shop.opening_week.Wednesday.end+'</td></tr>';
	        var thursday = '<tr><td>Thursday</td><td>'+shop.opening_week.Thursday.start+'-'+shop.opening_week.Thursday.end+'</td></tr>';
	        var friday = '<tr><td>Friday</td><td>'+shop.opening_week.Friday.start+'-'+shop.opening_week.Friday.end+'</td></tr>';
	        if(shop.opening_week.Saturday != null){
	          var saturday = '<tr><td>Saturday</td><td>'+shop.opening_week.Saturday.start+'-'+shop.opening_week.Saturday.end+'</td></tr>';
	        }else{
	          var saturday = '<tr><td>Saturday</td><td>-</td></tr>';
	        }
	        if(shop.opening_week.Sunday != null){
	          var sunday = '<tr><td>Sunday</td><td>'+shop.opening_week.Sunday.start+'-'+shop.opening_week.Sunday.end+'</td></tr>';
	        }else{
	          var sunday = '<tr><td>Sunday</td><td>-</td></tr>';
	        }
	        var distance_result = '<p id="send24_distance">Distance: '+distance+' meter</p>';
	        jQuery('#send24_info_map').html('<h3 id="popup_h3">Addresse</h3><p>Shop: '+shop.shop_title+' </p>'+distance_result+'<p id="step_1info_map">'+shop.shop_location+'</p>');
	        jQuery('#step_1info_map').after('<h3 id="popup_h3">Abningstider</h3><table id="popup_table">'+monday+''+tuesday+''+wednesday+''+thursday+''+friday+''+saturday+''+sunday+'</table>');
	        if(shop.user_login != null){
	            if(shop.rating['0'] != 'none'){
	                  var avatar = shop.user_avatar;
	                  var rating_html = '<div id="rating_send24"><div id="rating_avatar_send24">'+avatar+'<br><div id="rating_name_user">'+shop.user_login+'</div></div><div id="rating_service_name">'+shop.category+'<br><span class="send24_rating_stars"><span class="stars-rating">';  
	                  // var rating_html = '';
	                  for (var i = 0; i < Math.ceil(shop.rating); i++) {
	                    rating_html += '<span class="dashicons-star-filled">☆</span>';
	                  };
	                  if(i <= 5){
	                    var c = 5-i;
	                    for (var i = 0; i < c; i++) {
	                      rating_html += '<span class="dashicons-star-empty">☆</span>';
	                    };
	                  }
	                  rating_html += '</span><span class="send24_rating_average">'+shop.rating+'</span></span></div></div>  ';
	                  jQuery('#step_1info_map').after(rating_html);
	                  jQuery('#step_1info_map').after('<h3 class="bestyrer_h3">Bestyrer</h3>');
	            }
	        }
	        jQuery('#send24_selected_shop').html('Selected shop: <b style="color: #4E8FFD;">'+shop.shop_title+'</b>');
	        jQuery('#shop_selected').html(shop.shop_title);
	        $('#send24_map').html('change shop');
			$('.send34_map_selected').html('<span style="font-size: 12px;">Selected: <b style="color: #4E8FFD;">'+shop.shop_title+'</b></span>');
	        document.cookie = 'selected_shop_id='+default_shop_id;	
		    };
		}
    }

});

<?php

class WC_Send24_Shipping_Method extends WC_Shipping_Method{

	public $price;
	public $price_express;
	public $check_postcode;
	public $default_postcode = 1560;
	public $status_danmark = false;
	public $send24_settings;
	public $auth;
	public $product_id_express = 7062;
	public $shipping_country_code;
	public $is_available_distributors = false;
	// public $product_id_denamrk = 6026;

  	public function __construct()
  	{
		$this->id = 'send24_shipping';
	  	$this->method_title = __( 'Send24', 'woocommerce' );

	  	// Load the settings.
	  	$this->send24_settings = get_option('send24_settings');
	  	$this->auth = base64_encode($this->send24_settings['c_key'].':'.$this->send24_settings['c_secret']);
	  	$this->init_form_fields();
	  	$this->init_settings();

	  	// Define user set variables
	  	$this->enabled	= $this->get_option( 'enabled' );
	  	$this->title = $this->get_option( 'title' );

  		// add_action('woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options'));
  		add_action('woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options'));
  	}

  	public function init_form_fields(){
  		// $this->send24_settings['enable_postdanmark'] = 'no';
 		// General settings.
  		$this->form_fields = array(
		  	'enabled' => array(
		      'title' 	=> __( 'Enable/Disable', 'woocommerce' ),
		      'type' 	=> 'checkbox',
		      'label' 	=> __( 'Send24 Shipping', 'woocommerce' ),
		      'default' => 'yes'
		    ),
		  	'title' => array(
		      'title' 		=> __( 'Method Title', 'woocommerce' ),
		      'type' 			=> 'text',
		      'description' 	=> __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
		      'default'		=> __( 'Fragt', 'woocommerce' ),
		    ),
		    'zip' => array(
		      'title' 		=> __( 'Postcode', 'woocommerce' ),
		      'type' 			=> 'text',
		      'default'		=> '999',
		    ),
		    'api'           => array(
				'title'       => __( 'API Settings:', 'woocommerce-shipping-usps' ),
				'type'        => 'title',
				'class' => 'title_send24_setting',
				'description' => sprintf( __( 'You can obtain send24 user ID by signing up on the %s website.', 'woocommerce-shipping-usps' ), '<a href="https://send24.com/apikey/" target="_blank">Send24.com</a>' )
		    ),
		    'c_key' => array(
		      'title' 		=> 'Send24 Consumer Key',
		      'type' 			=> 'text',
		    ),
		    'c_secret' => array(
		      'title' 		=> 'Send24 Consumer Secret',
		      'type' 			=> 'text',
		    ),
		    'enabled_track' => array(
		      'title' 	=> __( 'Track Notice:', 'woocommerce' ),
		      'type' 	=> 'checkbox',
		      'label' 	=> __( 'Enable track link in the order confirmation mail', 'woocommerce' ),
		      'default' => 'no'
		    ),
		    'product_title'         => array(
				'title'       => 'Product Settings:',
				'type'        => 'title',
				'class' => 'title_send24_setting',
		    ),
		    'enabled_danmark' => array(
		      'title' 	=> __( 'Send24 Shipping:', 'woocommerce' ),
		      'type' 	=> 'checkbox',
		      'label' 	=> __( 'Enable', 'woocommerce' ),
		      'default' => 'yes',
		      'description' => 'enable sending to countries that support in Send24.'
		    ),
		    // 'enabled_express' => array(
		    //   'title' 	=> __( 'Express:', 'woocommerce' ),
		    //   'type' 	=> 'checkbox',
		    //   'label' 	=> __( 'Express', 'woocommerce' ),
		    //   'default' => 'no',
		    // ),
		    'start_work_express' => array(
		      'title' 	=> __( 'Start time work Express:', 'woocommerce' ),
		      'type'        => 'select',
				'default'     => '08:00',
				'options'     => $this->get_array_time(),
		      'desc_tip'    => true,
			  'description' => 'Please choose start time work Express',
		    ),
		    'end_work_express' => array(
		      'title' 	=> __( 'End time work Express:', 'woocommerce' ),
		       'type'        => 'select',
				'default'     => '18:00',
				'options'     => $this->get_array_time(),
		      'desc_tip'    => true,
			  'description' => 'Please choose end time work Express',
		    ),

		    'distributors'           => array(
				'title'       => 'Other distributors:',
				'type'        => 'title',
				'class' => 'title_send24_setting',
				'description' => 'Distributors that you have chosen <a href="https://send24.com/developer/">here</a>.',
		    ),
		    'enable_postdanmark' => array(
		      'title' 		=> __( 'PostDanmark', 'woocommerce' ),
		      'class' => 'level_class',
		      'type'        => 'select',
				'default'     => 'no',
				'options'     => array(
					'yes'      => 'yes',
					'no'      => 'no'
					),
				'desc_tip'    => true,
				'description' => 'Please choose',
		    ),
		    'postdanmark_price' => array(
		      'title' 		=> 'PostDanmark price',
		      'type' 			=> 'text',
		      'placeholder'	=> '0',
		    ),
		    'enable_gls' => array(
		      'title' 		=> __( 'GLS', 'woocommerce' ),
		      'class' => 'level_class',
		      'type'        => 'select',
				'default'     => 'no',
				'options'     => array(
					'yes'      => 'yes',
					'no'      => 'no'
					),
				'desc_tip'    => true,
				'description' => 'Please choose',
		    ),
		    'gls_price' => array(
		      'title' 		=> 'GLS price',
		      'type' 			=> 'text',
		      'placeholder'	=> '0',
		    ),
		    'enable_ups' => array(
		      'title' 		=> __( 'UPS', 'woocommerce' ),
		      'class' => 'level_class',
		      'type'        => 'select',
				'default'     => 'no',
				'options'     => array(
					'yes'      => 'yes',
					'no'      => 'no'
					),
				'desc_tip'    => true,
				'description' => 'Please choose',
		    ),		
		    'ups_price' => array(
		      'title' 		=> 'UPS price',
		      'type' 			=> 'text',
		      'placeholder'	=> '0',
		    ),   
		    'enable_dhl' => array(
		      'title' 		=> __( 'DHL', 'woocommerce' ),
		      'class' => 'level_class',
		      'type'        => 'select',
				'default'     => 'no',
				'options'     => array(
					'yes'      => 'yes',
					'no'      => 'no'
					),
				'desc_tip'    => true,
				'description' => 'Please choose',
		    ),
			'dhl_price' => array(
		      'title' 		=> 'DHL price',
		      'type' 			=> 'text',
		      'placeholder'	=> '0',
		    ),   
		    'enable_tnt' => array(
		      'title' 		=> __( 'TNT', 'woocommerce' ),
		      'class' => 'level_class',
		      'type'        => 'select',
				'default'     => 'no',
				'options'     => array(
					'yes'      => 'yes',
					'no'      => 'no'
					),
				'desc_tip'    => true,
				'description' => 'Please choose',
		    ),	
			'tnt_price' => array(
		      'title' 		=> 'TNT price',
		      'type' 			=> 'text',
		      'placeholder'	=> '0',
		    ),   
		    'enable_bring' => array(
		      'title' 		=> __( 'Bring', 'woocommerce' ),
		      'class' => 'level_class',
		      'type'        => 'select',
				'default'     => 'no',
				'options'     => array(
					'yes'      => 'yes',
					'no'      => 'no'
					),
				'desc_tip'    => true,
				'description' => 'Please choose',
		    ),
		    'bring_price' => array(
		      'title' 		=> 'Bring price',
		      'type' 			=> 'text',
		      'placeholder'	=> '0',
		    ),   

		    'payment_and_product'           => array(
				'title'       => 'Payment Settings:',
				'type'        => 'title',
				'class' => 'title_send24_setting',
		    ),
		    'whopay' => array(
		      'title' 	=> __( 'Payment parcels', 'woocommerce' ),
		      'type'        => 'select',
				'default'     => 'user',
				'options'     => array(
				'shop'      => 'Shop',
				'user'         => 'User'),
				'desc_tip'    => true,
				'description' => 'Who will pay the shipping costs?',
		     ),
		    'enable_return'         => array(
				'title'       => 'Service:',
				'type'        => 'title',
				'class' => 'title_send24_setting',
		    ),
		    'smart_price' => array(
		      'title' 		=> __( 'Smart Price', 'woocommerce' ),
		      'class' => 'level_class',
		      'type'        => 'select',
				'default'     => 'no',
				'options'     => array(
					'yes'      => 'yes',
					'no'      => 'no'
					),
				'desc_tip'    => true,
				'description' => 'Please choose',
		    ),

		    'return_portal'         => array(
				'title'       => 'Return portal',
				'type'        => 'title',
				'description' => $this->return_service(),
		    ),
		    'enabled_mail_return' => array(
		      'title' 	=> __( 'Return Notice:', 'woocommerce' ),
		      'type' 	=> 'checkbox',
		      'label' 	=> __( 'Link to Return portal in confirmation mail', 'woocommerce' ),
		      'default' => 'no'
		    ),
		   'shop_settings'         => array(
				'title'       => 'Shop Settings:',
				'type'        => 'title',
				'class' => 'title_send24_setting',
		    ),
		    'show_shops' => array(
		      'title' 		=> __( 'Show shops as', 'woocommerce' ),
		      'class' => 'level_class',
		      'type'        => 'select',
				'default'     => 'select',
				'options'     => array(
					'select'      => 'select box',
					'map'      => 'map'
					),
				'desc_tip'    => true,
				'description' => 'Please choose',
		    ),
		);

  	}

  	public function get_array_time(){
  		for ($i=0; $i < 24; $i++) { 
			if($i < 10) { $i = '0'.$i; }
			if($i%2 != 0){
				$date["$i:00"] = "$i:00";
				$date["$i:30"] = "$i:30";
			}else{
				$date["$i:00"] = "$i:00";
				$date["$i:30"] = "$i:30";
			}
		}
		return $date;		
  	}

  	public function return_service(){
  		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://send24.com/wc-api/v3/get_user_id");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Content-Type: application/json",
			"Authorization: Basic " . $this->auth
			));
		$user_meta = json_decode(curl_exec($ch));
		if(!empty($user_meta->return_activate)){
			$response = $user_meta->return_webpage_link['0'];
		}
		curl_close($ch);
		// Link return service.
		if(!empty($response)){
			$response = '<a id="link_return" href="'.$response.'" target="_blank">'.$response.'</a>';
		}else{
			$response = '<a id="button_return" href="http://send24.com/retur-indstilling/" target="_blank">Apply</a>';
		}
		return $response;
  	}

  	// Check woo.
	public function admin_options(){
		// Distributor. 
      	$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://send24.com/wc-api/v3/get_user_id");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Content-Type: application/json",
			"Authorization: Basic " . $this->auth
			)
		);
		$user_meta = json_decode(curl_exec($ch));
		// print_r($user_meta);
		$distributor_active_PostDanmark = (!empty($user_meta->distributor_active_PostDanmark) ? $user_meta->distributor_active_PostDanmark[0] : '');
        $distributor_active_GLS = (!empty($user_meta->distributor_active_GLS) ? $user_meta->distributor_active_GLS[0] : '');
        $distributor_active_UPS = (!empty($user_meta->distributor_active_UPS) ? $user_meta->distributor_active_UPS[0] : '');
        $distributor_active_DHL = (!empty($user_meta->distributor_active_DHL) ? $user_meta->distributor_active_DHL[0] : '');
        $distributor_active_TNT = (!empty($user_meta->distributor_active_TNT) ? $user_meta->distributor_active_TNT[0] : '');
        $distributor_active_Bring = (!empty($user_meta->distributor_active_Bring) ? $user_meta->distributor_active_Bring[0] : '');
        if(!empty($distributor_active_PostDanmark)){
         	$this->settings['enable_postdanmark'] = 'yes';
        }else{
         	$this->settings['enable_postdanmark'] = 'no';
        }
        if(!empty($distributor_active_GLS)){
	     	$this->settings['enable_gls'] = 'yes';
        }else{
	     	$this->settings['enable_gls'] = 'no';
        }
        if(!empty($distributor_active_UPS)){
	     	$this->settings['enable_ups'] = 'yes';
        }else{
	     	$this->settings['enable_ups'] = 'no';
        }
        if(!empty($distributor_active_DHL)){
	     	$this->settings['enable_dhl'] = 'yes';
        }else{
	     	$this->settings['enable_dhl'] = 'no';
        }
        if(!empty($distributor_active_TNT)){
	     	$this->settings['enable_tnt'] = 'yes';
        }else{
	     	$this->settings['enable_tnt'] = 'no';
        }
        if(!empty($distributor_active_Bring)){
	     	$this->settings['enable_bring'] = 'yes';
        }else{
	     	$this->settings['enable_bring'] = 'no';
        }
		// Check curentcy.
		if ( get_woocommerce_currency() != "DKK" ) {
			echo '<div class="error">
				<p>' . sprintf( __( 'Send24 requires that the <a href="%s">currency</a> is set to Danish Krone (DKK).', 'woocommerce-shipping-usps' ), admin_url( 'admin.php?page=wc-settings&tab=general' ) ) . '</p>
			</div>';
		}

		// Save/Update key.
		if(empty($this->send24_settings)){
	  		add_option('send24_settings', $this->settings);
	  	}else{
	  		update_option('send24_settings', $this->settings);
	  	}
	  	// Check keys and zip user.
	  	if($_POST){
	  		$this->check_key_and_zip($_POST['woocommerce_send24_shipping_c_key'], $_POST['woocommerce_send24_shipping_c_secret'], $_POST['woocommerce_send24_shipping_zip']);
		}else{
			$this->check_key_and_zip($this->send24_settings['c_key'], $this->send24_settings['c_secret'], $this->send24_settings['zip']);
		}
		
		// Show settings
		parent::admin_options();

	}

	// Check keys user.
	public function check_key_and_zip($c_key, $c_secret, $postcode){
		$auth = base64_encode($c_key.':'.$c_secret);
		if(!empty($zip)){
			$postcode = $this->default_postcode;
		}
		// Check zip.
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://send24.com/wc-api/v3/get_service_area/".$postcode);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Content-Type: application/json",
			"Authorization: Basic ".$auth
			));
		$zip_area = curl_exec($ch);
		$zip = json_decode($zip_area, true);
		if(!empty($zip['errors'])){
			echo '<div class="error"><p>Invalid Key</p></div>';
		}else{
			if($zip_area != 'true'){
		 		echo '<div class="error"><p>Invalid ZIP</p></div>';
			}
		}
	}

	// Check on postcode.
	public function checkonzip($postcode){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://send24.com/wc-api/v3/get_service_area/".$postcode);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Content-Type: application/json",
			"Authorization: Basic ".$this->auth
			));
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}

	// Show/Hide shipping.
  	public function is_available($package){

	  	$is_available = "yes" === $this->enabled;

		global $wpdb;
		if ($is_available){
	        if(!empty($this->send24_settings)){ 
	        	$is_available = false;
				$res['auth'] = $this->auth;
				$this->check_postcode = $package['destination']['postcode'];
				// Check country.
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, "https://send24.com/wc-api/v3/get_countries");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($ch, CURLOPT_HEADER, FALSE);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					"Content-Type: application/json",
					"Authorization: Basic " . $res['auth']
					));
				$send24_countries = json_decode(curl_exec($ch));
				curl_close($ch);
				// Countries.
				// $select_country = WC()->countries->countries[$package['destination']['country']];
				$this->shipping_country_code = $package['destination']['country'];
				// $this->shipping_country_code = 'DK';
				$is_available = false;
				$weight = $this->get_package_weight($package);
		        if(!empty($send24_countries['0']) && $this->shipping_country_code == 'DK'){
		        	foreach ($send24_countries['0'] as $key => $value) {
		        		$value = (array)$value;
		        		if($value['code'] == $this->shipping_country_code){
		        			if($weight <= 5 && !empty($value['0_5_kg'])){
		        				$this->price = $value['0_5_kg'];
		        			}elseif($weight > 5 && $weight <= 10 && !empty($value['5_10_kg'])){
		        				$this->price = $value['5_10_kg'];
		        			}elseif($weight > 10 && $weight <= 15 && !empty($value['10_15_kg'])){
		        				$this->price = $value['10_15_kg'];
		        			}
		                    $is_available = true;
		                    $this->is_available_distributors = true;
		                    break;
		        		}
		       		}
		        }

		        if(empty($this->price)){
		        	 $is_available = false;
		        	 $this->is_available_distributors = false;
		        }
		        
		        if($this->shipping_country_code == 'DK'){
			        // Sameday.
			        $ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, "https://send24.com/wc-api/v3/get_products");
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
					curl_setopt($ch, CURLOPT_HEADER, FALSE);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array(
						"Content-Type: application/json",
						"Authorization: Basic " . $res['auth']
						));
					$send24_countries = json_decode(curl_exec($ch));
					curl_close($ch);
					$n = count($send24_countries);
					for ($i = 0; $i < $n; $i++)
					{
						if($send24_countries[$i]->product_id == $this->product_id_express){
							$this->price_express = $send24_countries[$i]->price;
							$is_available = true;
						}
					}	
				}
				
			}
		}

		return apply_filters('woocommerce_shipping_' . $this->id . '_is_available', $is_available, $package);
  	}

  	
	// Calculate the total package weight.
	function get_package_weight( $package = array() ){
		$total_weight = 0;
		// Add up weight of each product
		if ( sizeof( $package['contents'] ) > 0 ) {
			foreach ( $package['contents'] as $item_id => $values ) {
				if ( $values['data']->has_weight() ) {
					$products_weight = $values['data']->get_weight() * $values['quantity'];
					$total_weight = $total_weight + $products_weight;
				}
			}
		}
		return $total_weight;
	}


  	// Calculate.
  	public function calculate_shipping($package){
  		// $this->status_danmark = true;
		// Express.
		$coast_express = $this->price_express;
       	$active_smartprice = $this->send24_settings['smart_price'];

        if($this->send24_settings['enabled_danmark'] == 'yes'){
        	$this->status_danmark = true;
        }

       	// Destributions.
       	if($this->is_available_distributors == true){
			if($this->status_danmark == true && self::checkonzip($this->check_postcode) == 'true'){
				$price['denmark'] = $this->price;
			}
			$price['postdanmark'] = (!empty($this->send24_settings['postdanmark_price']) ? $this->send24_settings['postdanmark_price'] : '0');
			$price['gls'] = (!empty($this->send24_settings['gls_price']) ? $this->send24_settings['gls_price'] : '0');
			$price['ups'] = (!empty($this->send24_settings['ups_price']) ? $this->send24_settings['ups_price'] : '0');
	   		$price['dhl'] = (!empty($this->send24_settings['dhl_price']) ? $this->send24_settings['dhl_price'] : '0');
			$price['tnt'] = (!empty($this->send24_settings['tnt_price']) ? $this->send24_settings['tnt_price'] : '0');
			$price['bring'] = (!empty($this->send24_settings['bring_price']) ? $this->send24_settings['bring_price'] : '0');
	  		// Who pay: user or shop.
			if($this->send24_settings['whopay'] != 'user'){
				$this->price = 0;
				$coast_express = 0;
				if($active_smartprice != 'yes'){
					$price['denmark'] = $price['postdanmark'] = $price['gls'] = $price['ups'] = $price['dhl'] = $price['tnt'] = $price['bring'] = 0;
				}
			}

	       	if($active_smartprice == 'yes'){
				$array_price = array_diff($price, array(0));
				$key_show = array_keys($array_price, min($array_price));
				// Who pay: user or shop.
				if($this->send24_settings['whopay'] != 'user'){
					$price[$key_show[0]] = 0;
				}
				// die;
				switch ($key_show[0]) {
					case 'denmark': 
						if($this->status_danmark == true){
							$rate = array(
								'id'    => $this->id,
								'label' => $this->title,
								'cost'  => $this->price
							);

							$this->add_rate($rate);
						}
				       	break;
					case 'bring':
				       	if($this->send24_settings['enable_bring'] == 'yes'){
				       		$rate = array(
								'id'    => $this->id.'_bring',
								'label' => 'Bring',
								'cost'  => $price['bring']
							);
							$this->add_rate($rate);
						}
						break;
					case 'dhl':
				       if($this->send24_settings['enable_dhl'] == 'yes'){
							$rate = array(
								'id'    => $this->id.'_dhl',
								'label' => 'DHL',
								'cost'  => $price['dhl']
							);
							$this->add_rate($rate);	
						}
						break;				
					case 'gls':
						if($this->send24_settings['enable_gls'] == 'yes'){
							$rate = array(
								'id'    => $this->id.'_gls',
								'label' => 'GLS',
								'cost'  => $price['gls']
							);
							$this->add_rate($rate);	
						}
						break;				
					case 'postdanmark':
				      	if($this->send24_settings['enable_postdanmark'] == 'yes'){
							$rate = array(
								'id'    => $this->id.'_postdanmark',
								'label' => 'PostDanmark',
								'cost'  => $price['postdanmark']
							);
							$this->add_rate($rate);
						}
						break;				
					case 'tnt':
				       	if($this->send24_settings['enable_tnt'] == 'yes'){
							$rate = array(
								'id'    => $this->id.'_tnt',
								'label' => 'TNT',
								'cost'  => $price['tnt']
							);
							$this->add_rate($rate);
						}
						break;
					case 'ups':
				       	if($this->send24_settings['enable_ups'] == 'yes'){
							$rate = array(
								'id'    => $this->id.'_ups',
								'label' => 'UPS',
								'cost'  => $price['ups']
							);
							$this->add_rate($rate);		
						}
						break;				
				}
	       	}else{
		 		// Check Denmark.
				if($this->status_danmark == true && self::checkonzip($this->check_postcode) == 'true'){
					$rate = array(
						'id'    => $this->id,
						'label' => $this->title,
						'cost'  => $this->price
					);

					$this->add_rate($rate);
				}

				if($this->send24_settings['enable_postdanmark'] == 'yes'){
					$rate = array(
						'id'    => $this->id.'_postdanmark',
						'label' => 'PostDanmark',
						'cost'  => $price['postdanmark']
					);
					$this->add_rate($rate);
				}

				if($this->send24_settings['enable_bring'] == 'yes'){
		       		$rate = array(
						'id'    => $this->id.'_bring',
						'label' => 'Bring',
						'cost'  => $price['bring']
					);
					$this->add_rate($rate);
				}

				if($this->send24_settings['enable_dhl'] == 'yes'){
					$rate = array(
						'id'    => $this->id.'_dhl',
						'label' => 'DHL',
						'cost'  => $price['dhl']
					);
					$this->add_rate($rate);	
				}

				if($this->send24_settings['enable_gls'] == 'yes'){
					$rate = array(
						'id'    => $this->id.'_gls',
						'label' => 'GLS',
						'cost'  => $price['gls']
					);
					$this->add_rate($rate);	
				}

				if($this->send24_settings['enable_tnt'] == 'yes'){
					$rate = array(
						'id'    => $this->id.'_tnt',
						'label' => 'TNT',
						'cost'  => $price['tnt']
					);
					$this->add_rate($rate);
				}

				if($this->send24_settings['enable_ups'] == 'yes'){
					$rate = array(
						'id'    => $this->id.'_ups',
						'label' => 'UPS',
						'cost'  => $price['ups']
					);
					$this->add_rate($rate);		
				}
	       	}
	    }

  		// Check Express.
  		if(!empty($this->price_express) && self::checkonzip($this->check_postcode) == 'true'){											
	  		// If Express = enable show shipping.
 	  		//if($this->send24_settings['enabled_express'] == 'yes'){
 	  			// Check time work.
 	  			date_default_timezone_set('Europe/Copenhagen');
 	  			$today = strtotime(date("Y-m-d H:i"));
 	  			$start_time = strtotime(''.date("Y-m-d").' '.$this->send24_settings['start_work_express'].'');
 	  			$end_time = strtotime(''.date("Y-m-d").' '.$this->send24_settings['end_work_express'].'');
 	  			if($start_time < $today && $end_time > $today){
 	  				// Get user billing
	  				$ch = curl_init();
		            curl_setopt($ch, CURLOPT_URL, "https://send24.com/wc-api/v3/get_user_id");
		            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		            curl_setopt($ch, CURLOPT_HEADER, FALSE);
		            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		                "Content-Type: application/json",
		                "Authorization: Basic ".$this->auth
		                ));
		            $user_meta = json_decode(curl_exec($ch));
		            $billing_address_1 = $user_meta->billing_address_1['0'];
		            $billing_postcode = $user_meta->billing_postcode['0'];
		            $billing_city = $user_meta->billing_city['0'];
		            $billing_country = $user_meta->billing_country['0'];
		            if($billing_country == 'DK'){
		                $billing_country = 'Denmark';
		            }	
		            // Full address.
	  				$full_billing_address = "$billing_address_1, $billing_postcode $billing_city, $billing_country";
	  				// $full_billing_address = "Lermontova St, 26, Zaporizhzhia, Zaporiz'ka oblast, Ukraine";
	  					
	  				$data_customer = WC()->session->customer;
	  				if($data_customer['shipping_country'] == 'DK'){
		                $data_customer['shipping_country'] = 'Denmark';
		            }
		        	$full_shipping_address = ''.$data_customer['shipping_address_1'].', '.$data_customer['postcode'].' '.$data_customer['shipping_city'].', '.$data_customer['shipping_country'].'';
		            // $full_shipping_address = "Lermontova St, 26, Zaporizhzhia, Zaporiz'ka oblast, Ukraine";
		            // Get billing coordinates.
		            $billing_url = "http://maps.googleapis.com/maps/api/geocode/json?sensor=false&address=".urlencode($full_billing_address);
		            $billing_latlng = get_object_vars(json_decode(file_get_contents($billing_url)));
		            // Check billing address.
            		if(!empty($billing_latlng['results'])){
			            $billing_lat = $billing_latlng['results'][0]->geometry->location->lat;
			            $billing_lng = $billing_latlng['results'][0]->geometry->location->lng;

			            // Get shipping coordinates.
			            $shipping_url = "http://maps.googleapis.com/maps/api/geocode/json?sensor=false&address=".urlencode($full_shipping_address);
			            $shipping_latlng = get_object_vars(json_decode(file_get_contents($shipping_url)));

			            // Check shipping address.
                		if(!empty($shipping_latlng['results'])){
				            $shipping_lat = $shipping_latlng['results'][0]->geometry->location->lat;
				            $shipping_lng = $shipping_latlng['results'][0]->geometry->location->lng;
	 	  				    // get_is_driver_area_five_km
				            $ch = curl_init();
				            curl_setopt($ch, CURLOPT_URL, "https://send24.com/wc-api/v3/get_is_driver_area_five_km");
				            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
				            curl_setopt($ch, CURLOPT_HEADER, FALSE);
				            curl_setopt($ch, CURLOPT_POST, TRUE);
				            curl_setopt($ch, CURLOPT_POSTFIELDS, '
				                                            {
				                                                "billing_lat": "'.$billing_lat.'",
				                                                "billing_lng": "'.$billing_lng.'",
				                                                "shipping_lat": "'.$shipping_lat.'",
				                                                "shipping_lng": "'.$shipping_lng.'"
				                                            }
				                                            ');

				            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				                "Content-Type: application/json",
				                "Authorization: Basic ".$this->auth
				            ));

				            $response = curl_exec($ch);
				            $res = json_decode($response);

				            if(!empty($res)){
				            	 // Check start_time.
                            	if(!empty($res->start_time)){
                            		$picked_up_time = strtotime(''.date("Y-m-d").' '.$res->start_time.'');
                                    // Check time work from send24.com
                                    if($start_time < $picked_up_time && $end_time > $picked_up_time){
										$rate = array(
											'id'    => $this->id.'_express',
											'label' => 'Send24 Express(ETA: '.$res->end_time.')',
											'cost'  => $coast_express
										);
										$this->add_rate($rate);
									}
								}
							}
						}
					}
				}
	  		//}
		}
		
  	}
}

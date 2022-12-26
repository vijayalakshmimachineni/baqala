<?php

class ControllerApiRest extends Controller {

	private $error = array();

	public function login() {



		$this->load->language('api/customer');



		$json = array();



		if (!isset($this->session->data['api_id'])) {

			$json['error']['warning'] = $this->language->get('error_permission');

		} else {				

			if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

				$this->load->model('account/customer');

				$error = $this->loginValidate();

				if(!$error) {

					// Unset guest

					unset($this->session->data['guest']);



					// Default Shipping Address

					$this->load->model('account/address');



					if ($this->config->get('config_tax_customer') == 'payment') {

						$this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());

					}



					if ($this->config->get('config_tax_customer') == 'shipping') {

						$this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());

					}



					// Wishlist

					if (isset($this->session->data['wishlist']) && is_array($this->session->data['wishlist'])) {

						$this->load->model('account/wishlist');



						foreach ($this->session->data['wishlist'] as $key => $product_id) {

							$this->model_account_wishlist->addWishlist($product_id);



							unset($this->session->data['wishlist'][$key]);

						}

					}

					//if($customer_id) {

						$json['Status'] = 'Success';

						$json['Content'] = array("customer" => $this->session->data);

						$json['Mssg'] = $this->language->get('text_success');

					//}

				}else {

					$json['Status'] = 'Success';

					$json['Content'] = $error;

					$json['Mssg'] = 'Not registered';

				}

			}			

		}		

		$this->response->addHeader('Content-Type: application/json');

		$this->response->setOutput(json_encode($json));

	}

	protected function loginValidate() {

		// Check how many login attempts have been made.

		$login_info = $this->model_account_customer->getLoginAttempts($this->request->post['email']);



		if ($login_info && ($login_info['total'] >= $this->config->get('config_login_attempts')) && strtotime('-1 hour') < strtotime($login_info['date_modified'])) {

			$this->error['warning'] = $this->language->get('error_attempts');

		}



		// Check if customer has been approved.

		$customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);



		if ($customer_info && !$customer_info['status']) {

			$this->error['warning'] = $this->language->get('error_approved');

		}



		if (!$this->error) {

			if (!$this->customer->login($this->request->post['email'], $this->request->post['password'])) {

				$this->error['warning'] = $this->language->get('error_login');



				$this->model_account_customer->addLoginAttempt($this->request->post['email']);

			} else {

				$this->model_account_customer->deleteLoginAttempts($this->request->post['email']);

			}

		}



		return $this->error;

	}

	public function register() {

		$this->load->language('api/customer');



		// Delete past customer in case there is an error

		unset($this->session->data['customer']);



		$json = array();



		if (!isset($this->session->data['api_id'])) {

			$json['error']['warning'] = $this->language->get('error_permission');

		} else {		

			$this->load->model('account/customer');

			if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

				$error = $this->validate();

				if(!$error) {

				$customer_id = $this->model_account_customer->addCustomer($this->request->post);



				// Clear any previous login attempts for unregistered accounts.

				$this->model_account_customer->deleteLoginAttempts($this->request->post['email']);

				if($customer_id) {

				    $this->load->language('account/success');

					$json['Status'] = 'Success';

					$json['Content'] = array("customer_id" => $customer_id);

					$json['Mssg'] = $this->language->get('text_approval');

				}

				}else {

					$json['Status'] = 'Success';

					$json['Content'] = $error;

					$json['Mssg'] = 'Not registered';

				}

			}else {

				$json['error']['warning'] = $this->language->get('error_permission');

			}

		}

		

		$this->response->addHeader('Content-Type: application/json');

		$this->response->setOutput(json_encode($json));

	}

	private function validate() {

		if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {

			$this->error['firstname'] = $this->language->get('error_firstname');

		}



		if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {

			$this->error['lastname'] = $this->language->get('error_lastname');

		}



		if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {

			$this->error['email'] = $this->language->get('error_email');

		}



		if ($this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {

			$this->error['warning'] = $this->language->get('error_exists');

		}



		if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {

			$this->error['telephone'] = $this->language->get('error_telephone');

		}



		// Customer Group

		if (isset($this->request->post['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->post['customer_group_id'], $this->config->get('config_customer_group_display'))) {

			$customer_group_id = $this->request->post['customer_group_id'];

		} else {

			$customer_group_id = $this->config->get('config_customer_group_id');

		}



		// Custom field validation

		$this->load->model('account/custom_field');



		$custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);



		foreach ($custom_fields as $custom_field) {

			if ($custom_field['location'] == 'account') {

				if ($custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {

					$this->error['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);

				} elseif (($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !filter_var($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $custom_field['validation'])))) {

					$this->error['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);

				}

			}

		}



		if ((utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) < 4) || (utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) > 40)) {

			$this->error['password'] = $this->language->get('error_password');

		}



		if ($this->request->post['confirm'] != $this->request->post['password']) {

			$this->error['confirm'] = $this->language->get('error_confirm');

		}	

		

		return $this->error;

	}

	public function sendOtp() {

		$this->load->language('api/customer');

		$json = array();

		$otp = rand(10000, 99999);

		$message = "Your OTP for login to STAPLE STATION is ".$otp.". please do not share the OTP with anyone. Thank you.";

		$res = $this->SendSms($this->request->post['mobile'], $message);

	//	print_r($res);exit;

		if($res) {

			$json['Status'] = 'Success';

			$json['Content'] = array('mobile'=>$this->request->post['mobile'],

													'OTP'=>$otp);

			$json['Mssg'] = $this->language->get('text_success');

		}else {

			$json['Status'] = 'Fail';

			$json['Content'] = '';

			$json['Mssg'] = 'Not Sent';

		}

		$this->response->addHeader('Content-Type: application/json');

		$this->response->setOutput(json_encode($json));

	}

	private function SendSms($mobile, $message){

	    $apiKey = urlencode('eWwV71iHfL0-GKXnLzLFCaNX1kcGadHSaJcA0xVAY9');

		$sender = "STAPLS"; // This is who the message appears to be from.

		$numbers = $mobile; // A single number or a comma-seperated list of numbers

		$message = urlencode($message);

		$data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);

        $ch = curl_init('http://api.textlocal.in/send/?');

		curl_setopt($ch, CURLOPT_POST, true);

		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$result = curl_exec($ch); // This is the result from the API

		curl_close($ch);

		return $result;

	}

	public function forgot() {

		$this->load->language('account/forgotten');

		$json = array();

		$this->document->setTitle($this->language->get('heading_title'));



		$this->load->model('account/customer');



		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			$error = $this->forgotvalidate();

			if(!$error) {

				$this->model_account_customer->editCode($this->request->post['email'], token(40));

				$json['Status'] = 'Success';

				$json['Content'] = array();

				$json['Mssg'] = $this->language->get('text_success');

			}else {

				$json['Status'] = 'Success';

				$json['Content'] = $error;

				$json['Mssg'] = 'Not registered';

			}

		}

		$this->response->addHeader('Content-Type: application/json');

		$this->response->setOutput(json_encode($json));

	}

	protected function forgotvalidate() {

		if (!isset($this->request->post['email'])) {

			$this->error['warning'] = $this->language->get('error_email');

		} elseif (!$this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {

			$this->error['warning'] = $this->language->get('error_email');

		}

		

		// Check if customer has been approved.

		$customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);



		if ($customer_info && !$customer_info['status']) {

			$this->error['warning'] = $this->language->get('error_approved');

		}



		return $this->error;

	}

	public function changePassword() {

		$this->load->language('account/password');

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			$error = $this->passwordValidate();

			if(!$error) {

				$this->load->model('account/customer');

				$this->model_account_customer->editPassword($this->customer->getEmail(), $this->request->post['password']);

				$json['Status'] = 'Success';

				$json['Content'] = array();

				$json['Mssg'] = $this->language->get('text_success');

			}else {

				$json['Status'] = 'Success';

				$json['Content'] = $error;

				$json['Mssg'] = 'Not registered';

			}

		}

		$this->response->addHeader('Content-Type: application/json');

		$this->response->setOutput(json_encode($json));

	}

	protected function passwordValidate() {

		if ((utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) < 4) || (utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) > 40)) {

			$this->error['password'] = $this->language->get('error_password');

		}



		if ($this->request->post['confirm'] != $this->request->post['password']) {

			$this->error['confirm'] = $this->language->get('error_confirm');

		}



		return $this->error;

	}

	public function profile() {

		$this->load->language('account/edit');

		$this->load->model('account/customer');

		$json = array();



		if (isset($this->request->get['user_id'])) {

			$user_id = $this->request->get['user_id'];

		} else {

			$user_id = 0;

		}



		$customer_info = $this->model_account_customer->getCustomer($user_id);



		if ($customer_info) {

				$json['Status'] = 'Success';

				$json['Content'] = $customer_info;

				$json['Mssg'] = $this->language->get('text_success');

			} else {

				$json['Status'] = 'Success';

				$json['Content'] = array();

				$json['Mssg'] = $this->language->get('error_not_found');

			}

		$this->response->addHeader('Content-Type: application/json');

		$this->response->setOutput(json_encode($json));

	}

	public function updateProfile() {

		$json = array();

		$this->load->language('account/edit');

		$this->load->model('account/customer');

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			$error = $this->forgotvalidate();

			if(!$error) {

				$this->model_account_customer->editCustomer($this->request->post['user_id'], $this->request->post);

				$json['Status'] = 'Success';

				$json['Content'] = array();

				$json['Mssg'] = $this->language->get('text_success');

			}else {

				$json['Status'] = 'Success';

				$json['Content'] = $error;

				$json['Mssg'] = 'Not Updated';

			}

		}

		$this->response->addHeader('Content-Type: application/json');

		$this->response->setOutput(json_encode($json));

	}

	protected function profileValidate() {

		if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {

			$this->error['firstname'] = $this->language->get('error_firstname');

		}



		if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {

			$this->error['lastname'] = $this->language->get('error_lastname');

		}



		if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {

			$this->error['email'] = $this->language->get('error_email');

		}



		if (($this->customer->getEmail() != $this->request->post['email']) && $this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {

			$this->error['warning'] = $this->language->get('error_exists');

		}



		if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {

			$this->error['telephone'] = $this->language->get('error_telephone');

		}



		// Custom field validation

		$this->load->model('account/custom_field');



		$custom_fields = $this->model_account_custom_field->getCustomFields('account', $this->config->get('config_customer_group_id'));



		foreach ($custom_fields as $custom_field) {

			if ($custom_field['location'] == 'account') {

				if ($custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {

					$this->error['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);

				} elseif (($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !filter_var($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $custom_field['validation'])))) {

					$this->error['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);

				}

			}

		}



		return $this->error;

	}

	public function categories() {

		$this->load->language('product/category');

		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$data['categories'] = array();

		$category_id = 0;

		$results = $this->model_catalog_category->getCategories($category_id);

		if(!empty($results)) {

				$json['Status'] = 'Success';

				$json['Content'] = $results;

				$json['Mssg'] = $this->language->get('text_success');

		}else {

			$json['Status'] = 'Success';

			$json['Content'] = array();

			$json['Mssg'] = 'No categories';

		}

		$this->response->addHeader('Content-Type: application/json');

		$this->response->setOutput(json_encode($json));

	}

	public function categoryProducts() {

		$this->load->language('product/category');

		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		if (isset($this->request->post['category_id'])) {

			$category_id = $this->request->post['category_id'];

		}else {

			$category_id = 0;

		}

		$category_info = $this->model_catalog_category->getCategory($category_id);

		if ($category_info) {

			$data['products'] = array();



			$filter_data = array(

				'filter_category_id' => $category_id

			);



			$product_total = $this->model_catalog_product->getTotalProducts($filter_data);



			$results = $this->model_catalog_product->getProducts($filter_data);

			$wish = false;

			foreach ($results as $result) {

				if(isset($this->request->post['user_id'])) {

					$this->load->model('api/rest');

					$res = $this->model_api_rest->checkWishlist($this->request->post['user_id'], $result['product_id']);

					if($res > 0) {

						$wish = true;

					}else {

						$wish = false;

					}

				}

				if ($result['image']) {

					$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));

				} else {

					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));

				}



				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {

					$price1 = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);

				} else {

					$price1 = false;

				}



				if ((float)$result['special']) {

					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);

				} else {

					$special = false;

				}



				if ($this->config->get('config_tax')) {

					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);

				} else {

					$tax = false;

				}



				if ($this->config->get('config_review_status')) {

					$rating = (int)$result['rating'];

				} else {

					$rating = false;

				}



				/* inspire Images Start */



				$insp_data['insp_images'] = array();

				$insp_results = $this->model_catalog_product->getProductImages($result['product_id']);



				



				foreach ($insp_results as $insp_result) {

					$insp_data['insp_images'][] = array('popup' => $this->model_tool_image->resize($insp_result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height')));

				}



				if($result['special'] > 0 AND $result['special'] != NULL ){

				$tag_per = ($result['special']*100)/$result['price'];

				$tag_per = round($tag_per);

				if($tag_per == 0){

				$tag_per = 1;

				}else{

				$tag_per = 100-$tag_per;

				}

				$tag = $result['price'] - $result['special'];

				}else{

				$tag = 0;

				$tag_per = 0;

				}			

				$options = array();



				foreach ($this->model_catalog_product->getProductOptions($result['product_id']) as $option) {

					$product_option_value_data = array();



					foreach ($option['product_option_value'] as $option_value) {

						if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {

							if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {

								$price = $this->currency->format($this->tax->calculate($option_value['price'], $result['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']);

							} else {

								$price = false;

							}



							$product_option_value_data[] = array(

								'product_option_value_id' => $option_value['product_option_value_id'],

								'option_value_id'         => $option_value['option_value_id'],

								'name'                    => $option_value['name'],

								'image'                   => $this->model_tool_image->resize($option_value['image'], 50, 50),

								'price'                   => $price,

								'price_prefix'            => $option_value['price_prefix']

							);

						}

					}



					$options[] = array(

						'product_option_id'    => $option['product_option_id'],

						'product_option_value' => $product_option_value_data,

						'option_id'            => $option['option_id'],

						'name'                 => $option['name'],

						'type'                 => $option['type'],

						'value'                => $option['value'],

						'required'             => $option['required']

					);

				}

				/* End */



				$data['products'][] = array(

					'product_id'  => $result['product_id'],

					'thumb'       => $image,

					'tag'       => $tag,

					'tag_per' => $tag_per,

					'name'        => $result['name'],

					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',

					'price'       => $price1,

					'special'     => $special,
					'special_discount' => "(".round((($result['price']-$result['special'])/($result['price']))*100,2)."%)",

					'tax'         => $tax,

					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,

					'rating'      => $result['rating'],

					'wishlisted'	=> $wish,
					'stock'        => $result['quantity'],
					'stock_alert'  => $result['quantity'] <= 5 ? "Only ".$result['quantity']." Left" : NULL,
					

					 // Add images Data 

					'insp_images' => $insp_data['insp_images'],

					'options'			=> $options

				);

			}

		}

			

		if(!empty($data)) {

				$json['Status'] = 'Success';

				$json['Content'] = $data;

				$json['Mssg'] = 'category products';

		}else {

			$json['Status'] = 'Success';

			$json['Content'] = array();

			$json['Mssg'] = 'No Products';

		}

		$this->response->addHeader('Content-Type: application/json');

		$this->response->setOutput(json_encode($json));

	}

	public function product() {

		$this->load->language('product/product');

		if (isset($this->request->post['product_id'])) {

			$product_id = (int)$this->request->post['product_id'];

		} else {

			$product_id = 0;

		}



		$this->load->model('catalog/product');



		$product_info = $this->model_catalog_product->getProduct($product_id);



		if ($product_info) {

			



			$data['heading_title'] = $product_info['name'];



			$data['text_minimum'] = sprintf($this->language->get('text_minimum'), $product_info['minimum']);

			$data['text_login'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', true), $this->url->link('account/register', '', true));



			$this->load->model('catalog/review');



			$data['tab_review'] = sprintf($this->language->get('tab_review'), $product_info['reviews']);



			$data['product_id'] = (int)$product_id;

			$data['manufacturer'] = $product_info['manufacturer'];

			$data['manufacturers'] = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id']);

			$data['model'] = $product_info['model'];

			$data['reward'] = $product_info['reward'];

			$data['points'] = $product_info['points'];

			$data['description'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');
			if ($product_info['quantity'] <= 5) {
				$data['stock_alert'] = "Only ".$product_info['quantity']." Left";
			}
			else {
				$data['stock_alert'] = '';
 			}



			if ($product_info['quantity'] <= 0) {

				$data['stock'] = $product_info['stock_status'];

			} elseif ($this->config->get('config_stock_display')) {

				$data['stock'] = $product_info['quantity'];

			} else {

				$data['stock'] = $this->language->get('text_instock');

			}



			$this->load->model('tool/image');



			if ($product_info['image']) {

				$data['popup'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'));

			} else {

				$data['popup'] = '';

			}



			if ($product_info['image']) {

				$data['thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height'));

			} else {

				$data['thumb'] = '';

			}



			$data['images'] = array();



			$results = $this->model_catalog_product->getProductImages($product_id);



			foreach ($results as $result) {

				$data['images'][] = array(

					'popup' => $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height')),

					'thumb' => $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height'))

				);

			}



			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {

				$data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);

			} else {

				$data['price'] = false;

			}



			if ((float)$product_info['special']) {

				$data['special'] = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);

			} else {

				$data['special'] = false;

			}



			if ($this->config->get('config_tax')) {

				$data['tax'] = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);

			} else {

				$data['tax'] = false;

			}



			$discounts = $this->model_catalog_product->getProductDiscounts($product_id);



			$data['discounts'] = array();



			foreach ($discounts as $discount) {

				$data['discounts'][] = array(

					'quantity' => $discount['quantity'],

					'price'    => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency'])

				);

			}



			$data['options'] = array();



			foreach ($this->model_catalog_product->getProductOptions($product_id) as $option) {

				$product_option_value_data = array();



				foreach ($option['product_option_value'] as $option_value) {

					if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {

						if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {

							$price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']);

						} else {

							$price = false;

						}



						$product_option_value_data[] = array(

							'product_option_value_id' => $option_value['product_option_value_id'],

							'option_value_id'         => $option_value['option_value_id'],

							'name'                    => $option_value['name'],

							'image'                   => $this->model_tool_image->resize($option_value['image'], 50, 50),

							'price'                   => $price,

							'price_prefix'            => $option_value['price_prefix']

						);

					}

				}



				$data['options'][] = array(

					'product_option_id'    => $option['product_option_id'],

					'product_option_value' => $product_option_value_data,

					'option_id'            => $option['option_id'],

					'name'                 => $option['name'],

					'type'                 => $option['type'],

					'value'                => $option['value'],

					'required'             => $option['required']

				);

			}



			if ($product_info['minimum']) {

				$data['minimum'] = $product_info['minimum'];

			} else {

				$data['minimum'] = 1;

			}



			$data['review_status'] = $this->config->get('config_review_status');



			if ($this->config->get('config_review_guest') || $this->customer->isLogged()) {

				$data['review_guest'] = true;

			} else {

				$data['review_guest'] = false;

			}



			if ($this->customer->isLogged()) {

				$data['customer_name'] = $this->customer->getFirstName() . '&nbsp;' . $this->customer->getLastName();

			} else {

				$data['customer_name'] = '';

			}



			$data['reviews'] = sprintf($this->language->get('text_reviews'), (int)$product_info['reviews']);

			$data['rating'] = (int)$product_info['rating'];



			if(isset($this->request->post['user_id'])) {

				$this->load->model('api/rest');

				$res = $this->model_api_rest->checkWishlist($this->request->post['user_id'], $product_id);

				if($res > 0) {

					$data['wishlisted'] = true;

				}else {

					$data['wishlisted'] = false;

				}

			}



			// Captcha

			if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('review', (array)$this->config->get('config_captcha_page'))) {

				$data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'));

			} else {

				$data['captcha'] = '';

			}



			$data['share'] = $this->url->link('product/product', 'product_id=' . (int)$product_id);



			$data['attribute_groups'] = $this->model_catalog_product->getProductAttributes($product_id);

			//reviews

			$data['reviews'] = array();

			$this->load->model('catalog/review');

			$results = $this->model_catalog_review->getReviewsByProductId($product_id);



			foreach ($results as $result) {

				$data['reviews'][] = array(

					'author'     => $result['author'],

					'text'       => nl2br($result['text']),

					'rating'     => (int)$result['rating'],

					'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))

				);

			}



			$data['products'] = array();



			$results = $this->model_catalog_product->getProductRelated($product_id);



			foreach ($results as $result) {

				if ($result['image']) {

					$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'));

				} else {

					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'));

				}



				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {

					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);

				} else {

					$price = false;

				}



				if ((float)$result['special']) {

					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);

				} else {

					$special = false;

				}



				if ($this->config->get('config_tax')) {

					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);

				} else {

					$tax = false;

				}



				if ($this->config->get('config_review_status')) {

					$rating = (int)$result['rating'];

				} else {

					$rating = false;

				}



				$data['products'][] = array(

					'product_id'  => $result['product_id'],

					'thumb'       => $image,

					'name'        => $result['name'],

					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',

					'price'       => $price,

					'special'     => $special,

					'tax'         => $tax,

					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,

					'rating'      => $rating,

					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])

				);

			}



			$data['tags'] = array();



			if ($product_info['tag']) {

				$tags = explode(',', $product_info['tag']);



				foreach ($tags as $tag) {

					$data['tags'][] = array(

						'tag'  => trim($tag),

						'href' => $this->url->link('product/search', 'tag=' . trim($tag))

					);

				}

			}



			$data['recurrings'] = $this->model_catalog_product->getProfiles($product_id);



			$this->model_catalog_product->updateViewed($product_id);

			$json['Status'] = 'Success';

			$json['Content'] = $data;

			$json['Mssg'] = $this->language->get('text_success');

			

		} else {

			$json['Status'] = 'Success';

			$json['Content'] = array();

			$json['Mssg'] = 'No Product';

		}

		$this->response->addHeader('Content-Type: application/json');

		$this->response->setOutput(json_encode($json));

	}

	public function homebanner() {

		static $module = 0;



		$this->load->model('design/banner');

		$this->load->model('tool/image');



		$data['banners'] = array();

		$setting['banner_id'] = '7';

		$setting['width'] = '250';

		$setting['height'] = '91';

		$results = $this->model_design_banner->getBanner($setting['banner_id']);



		foreach ($results as $result) {

			if (is_file(DIR_IMAGE . $result['image'])) {

				$data['banners'][] = array(

					'title' => $result['title'],

					'link'  => $result['link'],

					'image' => $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height'])

				);

			}

		}

		if(!empty($results)) {

				$json['Status'] = 'Success';

				$json['Content'] = $data;

				$json['Mssg'] = $this->language->get('text_success');

		}else {

			$json['Status'] = 'Fail';

			$json['Content'] = array();

			$json['Mssg'] = 'No Banners';

		}

		$this->response->addHeader('Content-Type: application/json');

		$this->response->setOutput(json_encode($json));

	}

	public function bestsellers() {

		$this->load->language('product/manufacturer');

		$this->load->model('catalog/manufacturer');

		$this->load->model('tool/image');		

		$data['manufacturer'] = array();

		$results = $this->model_catalog_manufacturer->getManufacturers();

		foreach ($results as $result) {

			if ($result['image']) {

					$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));

				} else {

					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));

				}

			$data['manufacturer'][] = array(

				'name' => $result['name'],

				'image' => $image,

				'id' => $result['manufacturer_id']

			);

		}

		if(!empty($results)) {

				$json['Status'] = 'Success';

				$json['Content'] = $data;

				$json['Mssg'] = $this->language->get('text_success');

		}else {

			$json['Status'] = 'Fail';

			$json['Content'] = array();

			$json['Mssg'] = 'No Best Sellers';

		}

		$this->response->addHeader('Content-Type: application/json');

		$this->response->setOutput(json_encode($json));

	}

	public function bestsellersProducts() {

		$this->load->language('product/manufacturer');

		$this->load->model('catalog/manufacturer');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');



		if (isset($this->request->post['manufacturer_id'])) {

			$manufacturer_id = (int)$this->request->post['manufacturer_id'];

		} else {			

			$results = $this->model_catalog_manufacturer->getManufacturers();

			foreach ($results as $result) {				

				$manufacturer_id = $result['manufacturer_id'];

				break;

			}

		}

		$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($manufacturer_id);

		

		if ($manufacturer_info) {

			$data['products'] = array();



			$filter_data = array(

				'filter_manufacturer_id' => $manufacturer_id

			);



			$product_total = $this->model_catalog_product->getTotalProducts($filter_data);



			$results = $this->model_catalog_product->getProducts($filter_data);

			$wish = false;

			foreach ($results as $result) {

				if(isset($this->request->post['user_id'])) {

					$this->load->model('api/rest');

					$res = $this->model_api_rest->checkWishlist($this->request->post['user_id'], $result['product_id']);

					if($res > 0) {

						$wish = true;

					}else {

						$wish = false;

					}

				}

				if ($result['image']) {

					$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));

				} else {

					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));

				}



				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {

					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);

				} else {

					$price = false;

				}



				if ((float)$result['special']) {

					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);

				} else {

					$special = false;

				}



				if ($this->config->get('config_tax')) {

					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);

				} else {

					$tax = false;

				}



				if ($this->config->get('config_review_status')) {

					$rating = (int)$result['rating'];

				} else {

					$rating = false;

				}



				/* inspire Images Start */



				$insp_data['insp_images'] = array();

				$insp_results = $this->model_catalog_product->getProductImages($result['product_id']);

				foreach ($insp_results as $insp_result) {

					$insp_data['insp_images'][] = array('popup' => $this->model_tool_image->resize($insp_result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height')));

				}

				

				if($result['special'] > 0 AND $result['special'] != NULL ){

				$tag_per = ($result['special']*100)/$result['price'];

				$tag_per = round($tag_per);

				if($tag_per == 0){

				$tag_per = 1;

				}else{

				$tag_per = 100-$tag_per;

				}

				$tag = $result['price'] - $result['special'];

				}else{

				$tag = 0;

				$tag_per = 0;

				}



				$options = array();



				foreach ($this->model_catalog_product->getProductOptions($result['product_id']) as $option) {

					$product_option_value_data = array();



					foreach ($option['product_option_value'] as $option_value) {

						if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {

							if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {

								$price = $this->currency->format($this->tax->calculate($option_value['price'], $result['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']);

							} else {

								$price = false;

							}



							$product_option_value_data[] = array(

								'product_option_value_id' => $option_value['product_option_value_id'],

								'option_value_id'         => $option_value['option_value_id'],

								'name'                    => $option_value['name'],

								'image'                   => $this->model_tool_image->resize($option_value['image'], 50, 50),

								'price'                   => $price,

								'price_prefix'            => $option_value['price_prefix']

							);

						}

					}



					$options[] = array(

						'product_option_id'    => $option['product_option_id'],

						'product_option_value' => $product_option_value_data,

						'option_id'            => $option['option_id'],

						'name'                 => $option['name'],

						'type'                 => $option['type'],

						'value'                => $option['value'],

						'required'             => $option['required']

					);

				}

				/* End */



				$data['products'][] = array(

					'product_id'  => $result['product_id'],

					'thumb'       => $image,

					'tag'       => $tag,

					'tag_per' => $tag_per,

					'name'        => $result['name'],

					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',

					'price'       => $price,

					'special'     => $special,

					'tax'         => $tax,

					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,

					'rating'      => $result['rating'],

					'wishlisted'	=> $wish,

					 // Add images Data 

					'insp_images' => $insp_data['insp_images'],

					'options'			=> $options

				);

			}

		}

		if(!empty($data)) {

				$json['Status'] = 'Success';

				$json['Content'] = $data;

				$json['Mssg'] = $this->language->get('text_success');

		}else {

			$json['Status'] = 'Fail';

			$json['Content'] = array();

			$json['Mssg'] = 'No Best Sellers';

		}

		$this->response->addHeader('Content-Type: application/json');

		$this->response->setOutput(json_encode($json));

	}

	public function wishlist() {

		$this->load->language('account/wishlist');

		$this->load->model('api/rest');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$data['products'] = array();

		$userId = $this->request->post['user_id'];

		$results = $this->model_api_rest->getUserWishlist($userId);



		foreach ($results as $result) {

			$product_info = $this->model_catalog_product->getProduct($result['product_id']);



			if ($product_info) {

				if ($product_info['image']) {

					$image = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_wishlist_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_wishlist_height'));

				} else {

					$image = false;

				}



				if ($product_info['quantity'] <= 0) {

					$stock = $product_info['stock_status'];

				} elseif ($this->config->get('config_stock_display')) {

					$stock = $product_info['quantity'];

				} else {

					$stock = $this->language->get('text_instock');

				}



				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {

					$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);

				} else {

					$price = false;

				}



				if ((float)$product_info['special']) {

					$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);

				} else {

					$special = false;

				}



				$data['products'][] = array(

					'product_id' => $product_info['product_id'],

					'thumb'      => $image,

					'name'       => $product_info['name'],

					'model'      => $product_info['model'],

					'stock'      => $stock,

					'price'      => $price,

					'special'    => $special,

					'option'		=> '1 KG'

				);

			} else {

				$this->model_account_wishlist->deleteWishlist($result['product_id']);

			}

		}

		if(!empty($results)) {

				$json['Status'] = 'Success';

				$json['Content'] = $data;

				$json['Mssg'] = $this->language->get('text_success');

		}else {

			$json['Status'] = 'Fail';

			$json['Content'] = array();

			$json['Mssg'] = 'No Products';

		}

		$this->response->addHeader('Content-Type: application/json');

		$this->response->setOutput(json_encode($json));

	}

	public function wishlistcount() {

		$this->load->language('account/wishlist');

		$this->load->model('api/rest');

		

		$userId = $this->request->post['user_id'];

		$results = $this->model_api_rest->getTotalWishlist($userId);



		

		if(!empty($results)) {

				$json['Status'] = 'Success';

				$json['Content'] = $results;

				$json['Mssg'] = 'wishlist';

		}else {

			$json['Status'] = 'Fail';

			$json['Content'] = array();

			$json['Mssg'] = 'No Products';

		}

		$this->response->addHeader('Content-Type: application/json');

		$this->response->setOutput(json_encode($json));

	}

	public function addWishlist() {

		$this->load->language('account/wishlist');



		$json = array();

		$data = array();

		if (isset($this->request->post['product_id'])) {

			$product_id = $this->request->post['product_id'];

		} else {

			$product_id = 0;

		}



		$this->load->model('catalog/product');



		$product_info = $this->model_catalog_product->getProduct($product_id);



		if ($product_info) {

			if ($this->request->post['user_id'] != '') {

				// Edit customers cart

				$this->load->model('api/rest');



				$this->model_api_rest->addWishlist($this->request->post['product_id'], $this->request->post['user_id']);



				$json['success'] = sprintf($this->language->get('text_success'), $this->url->link('product/product', 'product_id=' . (int)$this->request->post['product_id']), $product_info['name'], $this->url->link('account/wishlist'));



				$json['total'] = sprintf($this->language->get('text_wishlist'), $this->model_api_rest->getTotalWishlist($this->request->post['user_id']));

			} else {

				if (!isset($this->session->data['wishlist'])) {

					$this->session->data['wishlist'] = array();

				}



				$this->session->data['wishlist'][] = $this->request->post['product_id'];



				$this->session->data['wishlist'] = array_unique($this->session->data['wishlist']);



				$json['success'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', true), $this->url->link('account/register', '', true), $this->url->link('product/product', 'product_id=' . (int)$this->request->post['product_id']), $product_info['name'], $this->url->link('account/wishlist'));



				$json['total'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));

			}

		}

		if(!empty($json['total'])) {

				$data['Status'] = 'Success';

				$data['Content'] = $json['total'];

				$data['Mssg'] = $json['success'];

		}else {

			$data['Status'] = 'Fail';

			$data['Content'] = array();

			$data['Mssg'] = 'No Products';

		}

		$this->response->addHeader('Content-Type: application/json');

		$this->response->setOutput(json_encode($data));

	}

	public function cartProducts() {

		$user_id = $this->request->post['user_id'];

		$this->load->language('api/cart');

		$this->load->model('api/rest');

		$json = array();



		if (!isset($this->session->data['api_id'])) {

			$json['error']['warning'] = $this->language->get('error_permission');

		} else {

			// Products

			$json['products'] = array();



			$products = $this->model_api_rest->getCartProducts($user_id);

			$sub_total = 0;

			foreach ($products as $product) {

				$product_total = 0;

				$sub_total += $product['total'];

				foreach ($products as $product_2) {

					if ($product_2['product_id'] == $product['product_id']) {

						$product_total += $product_2['quantity'];

					}

				}



				if ($product['minimum'] > $product_total) {

					$json['error']['minimum'][] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);

				}



				$option_data = array();



				foreach ($product['option'] as $option) {

					$option_data[] = array(

						'product_option_id'       => $option['product_option_id'],

						'product_option_value_id' => $option['product_option_value_id'],

						'name'                    => $option['name'],

						'value'                   => $option['value'],

						'type'                    => $option['type']

					);

				}

				$this->load->model('tool/image');

				if ($product['image']) {

					$image = $this->model_tool_image->resize($product['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));

				} else {

					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));

				}

				$json['products'][] = array(

					'cart_id'    => $product['cart_id'],

					'product_id' => $product['product_id'],

					'name'       => $product['name'],

					'thumb'       => $image,

					'model'      => $product['model'],

					'option'     => $option_data,

					'quantity'   => $product['quantity'],

					'stock'      => $product['stock'] ? true : !(!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning')),

					'shipping'   => $product['shipping'],

					'price'      => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']),

					'total'      => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'], $this->session->data['currency']),

					'reward'     => $product['reward']

				);

			}



			// Voucher

			$json['vouchers'] = array();



			if (!empty($this->session->data['vouchers'])) {

				foreach ($this->session->data['vouchers'] as $key => $voucher) {

					$json['vouchers'][] = array(

						'code'             => $voucher['code'],

						'description'      => $voucher['description'],

						'from_name'        => $voucher['from_name'],

						'from_email'       => $voucher['from_email'],

						'to_name'          => $voucher['to_name'],

						'to_email'         => $voucher['to_email'],

						'voucher_theme_id' => $voucher['voucher_theme_id'],

						'message'          => $voucher['message'],

						'price'            => $this->currency->format($voucher['amount'], $this->session->data['currency']),			

						'amount'           => $voucher['amount']

					);

				}

			}



			// Totals

			$this->load->model('setting/extension');



			$totals = array();

			$taxes = $this->model_api_rest->getTaxes($user_id);

			$total = 0;



			// Because __call can not keep var references so we put them into an array. 

			$total_data = array(

				'totals' => &$totals,

				'taxes'  => &$taxes,

				'total'  => &$total

			);

			

			$sort_order = array();



			$results = $this->model_setting_extension->getExtensions('total');



			foreach ($results as $key => $value) {

				$sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');

			}



			array_multisort($sort_order, SORT_ASC, $results);



			foreach ($results as $result) {

				if ($this->config->get('total_' . $result['code'] . '_status')) {

					$this->load->model('extension/total/' . $result['code']);

					

					// We have to put the totals in an array so that they pass by reference.

					$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);

				}

			}



			$sort_order = array();



			foreach ($totals as $key => $value) {

				$sort_order[$key] = $value['sort_order'];

			}



			array_multisort($sort_order, SORT_ASC, $totals);



			$json['totals'] = array();

			$tax_ttl = 0.0;

        $json['totals'][] = array(

 					'title' => "Sub-Total",

 					'text'  => $this->currency->format($sub_total, $this->session->data['currency'])

 				);

			foreach ($totals as $total) {

			    if($total['code'] == 'tax') {

		 	        $json['totals'][] = array(

			 		'title' => $total['title'],

			 		'text'  => $this->currency->format($total['value'], $this->session->data['currency'])

			 	    );

			 	    $tax_ttl += $total['value'];

			    }

			 }

 			

 			$json['totals'][] = array(

 					'title' => "Total",

 					'text'  => $this->currency->format(($sub_total+$tax_ttl), $this->session->data['currency'])

 				);

		}

		$data['Status'] = 'Success';

		$data['Content'] = $json;

		$data['Mssg'] = $this->language->get('text_success');

		$this->response->addHeader('Content-Type: application/json');

		$this->response->setOutput(json_encode($data));

	}

	public function cartCount() {

		$user_id = $this->request->post['user_id'];

		$this->load->language('api/cart');

		$this->load->model('api/rest');

		$json = array();

		// Products

		$json['product_count'] = 0;



		$products = $this->model_api_rest->getProducts($user_id);

		$product_total = 0;

		foreach ($products as $product) {		



			foreach ($products as $product_2) {

				if ($product_2['product_id'] == $product['product_id']) {

					$product_total += $product_2['quantity'];

				}

			}

		}

		$json['product_count'] = $product_total;

		$data['Status'] = 'Success';

		$data['Content'] = $json;

		$data['Mssg'] = $this->language->get('text_success');

		$this->response->addHeader('Content-Type: application/json');

		$this->response->setOutput(json_encode($data));

	}

	public function writereview() {

		$this->load->language('product/product');



		$json = array();



		if ($this->request->server['REQUEST_METHOD'] == 'POST') {

			if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 25)) {

				$json['error'] = $this->language->get('error_name');

			}



			if ((utf8_strlen($this->request->post['text']) < 25) || (utf8_strlen($this->request->post['text']) > 1000)) {

				$json['error'] = $this->language->get('error_text');

			}



			if (empty($this->request->post['rating']) || $this->request->post['rating'] < 0 || $this->request->post['rating'] > 5) {

				$json['error'] = $this->language->get('error_rating');

			}



			// Captcha

			if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('review', (array)$this->config->get('config_captcha_page'))) {

				$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');



				if ($captcha) {

					$json['error'] = $captcha;

				}

			}



			if (!isset($json['error'])) {

				$this->load->model('catalog/review');



				$review = $this->model_catalog_review->addReview($this->request->post['product_id'], $this->request->post);

				$data['Status'] = 'Success';

				$data['Content'] = $review;

				$data['Mssg'] = $this->language->get('text_success');

			}else {

			    $data['Status'] = 'Fail';

				$data['Content'] = '';

				$data['Mssg'] = 'Not added';

			}

		}



		$this->response->addHeader('Content-Type: application/json');

		$this->response->setOutput(json_encode($data));

	}

	public function addAddress() {

		$this->load->language('account/address');

		$json = array();

		$this->load->model('account/address');

		$this->load->model('api/rest');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateAddressForm()) {

			$this->model_api_rest->addAddress($this->request->post['user_id'], $this->request->post);

			$json['Mssg'] = $this->language->get('text_add');

			$data['Status'] = 'Success';

			$data['Content'] = '';

		}else {

			$data['Status'] = 'Fail';

			$data['Content'] = array();

			$data['Mssg'] = 'Not added';

		}		

		$this->response->addHeader('Content-Type: application/json');

		$this->response->setOutput(json_encode($data));

	}

	public function editAddress() {		

		$this->load->language('account/address');

		$this->load->model('account/address');

		$this->load->model('api/rest');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateAddressForm()) {

			$this->model_api_rest->editAddress($this->request->get['address_id'], $this->request->post);

			$json['Mssg'] = $this->language->get('text_add');

			$data['Status'] = 'Success';

			$data['Content'] = '';

		}else {

			$data['Status'] = 'Fail';

			$data['Content'] = array();

			$data['Mssg'] = 'Not added';

		}		

		$this->response->addHeader('Content-Type: application/json');

		$this->response->setOutput(json_encode($data));

	}



	public function deleteAddress() {		

		$this->load->language('account/address');

		$this->load->model('account/address');

		$this->load->model('api/rest');

		if (isset($this->request->get['address_id'])) {

			$this->model_api_rest->deleteAddress($this->request->get['address_id']);

			$json['Mssg'] = $this->language->get('text_add');

			$data['Status'] = 'Success';

			$data['Content'] = '';

		}else {

			$data['Status'] = 'Fail';

			$data['Content'] = array();

			$data['Mssg'] = 'Not added';

		}		

		$this->response->addHeader('Content-Type: application/json');

		$this->response->setOutput(json_encode($data));

	}

	protected function validateAddressForm() {

		if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {

			$this->error['firstname'] = $this->language->get('error_firstname');

		}



		if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {

			$this->error['lastname'] = $this->language->get('error_lastname');

		}



		if ((utf8_strlen(trim($this->request->post['address_1'])) < 3) || (utf8_strlen(trim($this->request->post['address_1'])) > 128)) {

			$this->error['address_1'] = $this->language->get('error_address_1');

		}



		if ((utf8_strlen(trim($this->request->post['city'])) < 2) || (utf8_strlen(trim($this->request->post['city'])) > 128)) {

			$this->error['city'] = $this->language->get('error_city');

		}



		$this->load->model('localisation/country');



		$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);



		if ($country_info && $country_info['postcode_required'] && (utf8_strlen(trim($this->request->post['postcode'])) < 2 || utf8_strlen(trim($this->request->post['postcode'])) > 10)) {

			$this->error['postcode'] = $this->language->get('error_postcode');

		}



		if ($this->request->post['country_id'] == '' || !is_numeric($this->request->post['country_id'])) {

			$this->error['country'] = $this->language->get('error_country');

		}



		if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '' || !is_numeric($this->request->post['zone_id'])) {

			$this->error['zone'] = $this->language->get('error_zone');

		}



		// Custom field validation

		$this->load->model('account/custom_field');



		$custom_fields = $this->model_account_custom_field->getCustomFields($this->config->get('config_customer_group_id'));



		foreach ($custom_fields as $custom_field) {

			if ($custom_field['location'] == 'address') {

				if ($custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {

					$this->error['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);

				} elseif (($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !filter_var($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $custom_field['validation'])))) {

					$this->error['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);

				}

			}

		}



		return !$this->error;

	}

	public function getaddress() {

		$this->load->language('account/address');

		$this->load->model('account/address');

		$this->load->model('api/rest');



		$data['addresses'] = array();

		$userId = $this->request->get['user_id'];

		$results = $this->model_api_rest->getAddresses($userId);



		foreach ($results as $result) {

			if ($result['address_format']) {

				$format = $result['address_format'];

			} else {

				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}'. "\n" . '{mobile}'. "\n" . '{email}';

			}



			$find = array(

				'{firstname}',

				'{lastname}',

				'{company}',

				'{address_1}',

				'{address_2}',

				'{city}',

				'{postcode}',

				'{zone}',

				'{zone_code}',

				'{country}',

				'{mobile}',

				'{email}'

			);



			$replace = array(

				'firstname' => $result['firstname'],

				'lastname'  => $result['lastname'],

				'company'   => $result['company'],

				'address_1' => $result['address_1'],

				'address_2' => $result['address_2'],

				'city'      => $result['city'],

				'postcode'  => $result['postcode'],

				'zone'      => $result['zone'],

				'zone_code' => $result['zone_code'],

				'country'   => $result['country'],

				'mobile'		=> $result['mobile'],

				'email'			=> $result['email']

			);



			$data['addresses'][] = array(

				'address_id' => $result['address_id'],

				'address'    => str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format)))),

				'update'     => $this->url->link('account/address/edit', 'address_id=' . $result['address_id'], true),

				'delete'     => $this->url->link('account/address/delete', 'address_id=' . $result['address_id'], true),

				'mobile'		=> $result['mobile'],

				'email'			=> $result['email']

			);

		}

		if(!empty($data['addresses'])) {

				$json['Status'] = 'Success';

				$json['Content'] = $data['addresses'];

				$json['Mssg'] = $this->language->get('text_success');

		}else {

			$json['Status'] = 'Fail';

			$json['Content'] = array();

			$json['Mssg'] = 'No Address';

		}

		$this->response->addHeader('Content-Type: application/json');

		$this->response->setOutput(json_encode($json));

	}

	public function checkout() {

		$user_id = $this->request->post['user_id'];

		$address_id = $this->request->post['address_id'];

		//$cart_id = $this->request->post['cart_id'];

		$total = $this->request->post['total'];

		$payment_method = $this->request->post['payment_method'];
		$razorpay_payment_id = $this->request->post['txn_id'];

		$this->load->model('api/rest');

		$redirect = '';

		$products = $this->model_api_rest->getProducts($user_id);

		foreach ($products as $product) {

			$product_total = 0;



			foreach ($products as $product_2) {

				if ($product_2['product_id'] == $product['product_id']) {

					$product_total += $product_2['quantity'];

				}

			}



			if ($product['minimum'] > $product_total) {

				$redirect = true;



				break;

			}

		}

		if (!$redirect) {

			$order_data = array();



			$totals = array();

			$taxes = $this->model_api_rest->getTaxes($user_id);

			$total = 0;



			// Because __call can not keep var references so we put them into an array.

			$total_data = array(

				'totals' => &$totals,

				'taxes'  => &$taxes,

				'total'  => &$total

			);



			$this->load->model('setting/extension');



			$sort_order = array();



			$results = $this->model_setting_extension->getExtensions('total');



			foreach ($results as $key => $value) {

				$sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');

			}



			array_multisort($sort_order, SORT_ASC, $results);



			foreach ($results as $result) {

				if ($this->config->get('total_' . $result['code'] . '_status')) {

					$this->load->model('api/' . $result['code']);



					// We have to put the totals in an array so that they pass by reference.

					$this->{'model_api_' . $result['code']}->getTotal($total_data, $user_id);

				}

			}



			$sort_order = array();



			foreach ($totals as $key => $value) {

				$sort_order[$key] = $value['sort_order'];

			}



			array_multisort($sort_order, SORT_ASC, $totals);



			$order_data['totals'] = $totals;



			$this->load->language('checkout/checkout');



			$order_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');

			$order_data['store_id'] = $this->config->get('config_store_id');

			$order_data['store_name'] = $this->config->get('config_name');



			if ($order_data['store_id']) {

				$order_data['store_url'] = $this->config->get('config_url');

			} else {

				if ($this->request->server['HTTPS']) {

					$order_data['store_url'] = HTTPS_SERVER;

				} else {

					$order_data['store_url'] = HTTP_SERVER;

				}

			}

			

			$this->load->model('account/customer');



			if ($user_id != '') {

				$customer_info = $this->model_account_customer->getCustomer($user_id);



				$order_data['customer_id'] = $user_id;

				$order_data['customer_group_id'] = $customer_info['customer_group_id'];

				$order_data['firstname'] = $customer_info['firstname'];

				$order_data['lastname'] = $customer_info['lastname'];

				$order_data['email'] = $customer_info['email'];

				$order_data['telephone'] = $customer_info['telephone'];

				$order_data['custom_field'] = json_decode($customer_info['custom_field'], true);

                $mobile23 = $customer_info['telephone'];
                $user45 = $customer_info['firstname']." ".$customer_info['lastname'];
 		       


			}

			$order_data['address_id'] = $address_id;

			$address = $this->model_api_rest->getAddressDetails($address_id);

			$order_data['payment_firstname'] = $address[$address_id]['firstname'];

			$order_data['payment_lastname'] = $address[$address_id]['lastname'];

			$order_data['payment_company'] = $address[$address_id]['company'];

			$order_data['payment_address_1'] = $address[$address_id]['address_1'];

			$order_data['payment_address_2'] = $address[$address_id]['address_2'];

			$order_data['payment_city'] = $address[$address_id]['city'];

			$order_data['payment_postcode'] = $address[$address_id]['postcode'];

			$order_data['payment_zone'] = $address[$address_id]['zone'];

			$order_data['payment_zone_id'] = $address[$address_id]['zone_id'];

			$order_data['payment_country'] = $address[$address_id]['country'];

			$order_data['payment_country_id'] = $address[$address_id]['country_id'];

			$order_data['payment_address_format'] = $address[$address_id]['address_format'];

			$order_data['payment_custom_field'] = (isset($address[$address_id]['custom_field']) ? $address[$address_id]['custom_field'] : array());




			

			$order_data['payment_code'] = $payment_method;
			if($payment_method=='cod'){
				$order_data['payment_method'] = 'Cash On Delivery';
			}
			else {
				$order_data['payment_method'] = 'Credit Card / Debit Card / Net Banking (Razorpay)';
			}


			

			

			$order_data['shipping_firstname'] = '';

			$order_data['shipping_lastname'] = '';

			$order_data['shipping_company'] = '';

			$order_data['shipping_address_1'] = '';

			$order_data['shipping_address_2'] = '';

			$order_data['shipping_city'] = '';

			$order_data['shipping_postcode'] = '';

			$order_data['shipping_zone'] = '';

			$order_data['shipping_zone_id'] = '';

			$order_data['shipping_country'] = '';

			$order_data['shipping_country_id'] = '';

			$order_data['shipping_address_format'] = '';

			$order_data['shipping_custom_field'] = array();

			$order_data['shipping_method'] = '';

			$order_data['shipping_code'] = '';

			if(date('H') >= 14) $datetime = new DateTime('tomorrow');

			else $datetime = new DateTime();



			$order_data['delivery_date'] =  $datetime->format('d F,Y');

			$order_data['delivery_time'] = '';

				



			$order_data['products'] = array();



			foreach ($this->model_api_rest->getProducts($user_id) as $product) {

				$option_data = array();



				foreach ($product['option'] as $option) {

					$option_data[] = array(

						'product_option_id'       => $option['product_option_id'],

						'product_option_value_id' => $option['product_option_value_id'],

						'option_id'               => $option['option_id'],

						'option_value_id'         => $option['option_value_id'],

						'name'                    => $option['name'],

						'value'                   => $option['value'],

						'type'                    => $option['type']

					);

				}



				$order_data['products'][] = array(

					'product_id' => $product['product_id'],

					'name'       => $product['name'],

					'model'      => $product['model'],

					'option'     => $option_data,

					'download'   => $product['download'],

					'quantity'   => $product['quantity'],

					'subtract'   => $product['subtract'],

					'price'      => $product['price'],

					'total'      => $product['total'],

					'tax'        => '',

					'reward'     => $product['reward']

				);

			}

			//echo json_encode($order_data['products']);exit;

			// Gift Voucher

			$order_data['vouchers'] = array();



			if (!empty($this->session->data['vouchers'])) {

				foreach ($this->session->data['vouchers'] as $voucher) {

					$order_data['vouchers'][] = array(

						'description'      => $voucher['description'],

						'code'             => token(10),

						'to_name'          => $voucher['to_name'],

						'to_email'         => $voucher['to_email'],

						'from_name'        => $voucher['from_name'],

						'from_email'       => $voucher['from_email'],

						'voucher_theme_id' => $voucher['voucher_theme_id'],

						'message'          => $voucher['message'],

						'amount'           => $voucher['amount']

					);

				}

			}

			

			$order_data['comment'] = '';

			$order_data['total'] = $total;



			

			$order_data['affiliate_id'] = 0;

			$order_data['commission'] = 0;

			$order_data['marketing_id'] = 0;

			$order_data['tracking'] = '';

			



			$order_data['language_id'] = $this->config->get('config_language_id');

			$order_data['currency_id'] = $this->currency->getId($this->session->data['currency']);

			$order_data['currency_code'] = $this->session->data['currency'];

			$order_data['currency_value'] = $this->currency->getValue($this->session->data['currency']);

			$order_data['ip'] = $this->request->server['REMOTE_ADDR'];



			if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {

				$order_data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];

			} elseif (!empty($this->request->server['HTTP_CLIENT_IP'])) {

				$order_data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];

			} else {

				$order_data['forwarded_ip'] = '';

			}



			if (isset($this->request->server['HTTP_USER_AGENT'])) {

				$order_data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];

			} else {

				$order_data['user_agent'] = '';

			}



			if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {

				$order_data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];

			} else {

				$order_data['accept_language'] = '';

			}



			$this->load->model('checkout/order');

			//echo json_encode($order_data['products']);exit;

			$this->session->data['order_id'] = $this->model_checkout_order->addOrder($order_data);
              $message = "Hi ".$user45.", thank you for shopping with us. Here is your order No. ".$this->session->data['order_id']." Your items will be shipped shortly.";

		         $this->SendSms($mobile23, $message);
             if($payment_method!='cod'){
			$this->model_checkout_order->addOrderHistory($this->session->data['order_id'],1, 'Payment Successful. Razorpay Payment Id:' . $razorpay_payment_id, true);
		}

			//Notification

			$this->model_api_rest->addNotification($this->session->data['order_id']);

			$this->load->model('tool/upload');



			$data['products'] = array();



			foreach ($this->model_api_rest->getProducts($user_id) as $product) {

				$option_data = array();



				foreach ($product['option'] as $option) {

					if ($option['type'] != 'file') {

						$value = $option['value'];

					} else {

						$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);



						if ($upload_info) {

							$value = $upload_info['name'];

						} else {

							$value = '';

						}

					}



					$option_data[] = array(

						'name'  => $option['name'],

						'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)

					);

				}



				$recurring = '';



				if ($product['recurring']) {

					$frequencies = array(

						'day'        => $this->language->get('text_day'),

						'week'       => $this->language->get('text_week'),

						'semi_month' => $this->language->get('text_semi_month'),

						'month'      => $this->language->get('text_month'),

						'year'       => $this->language->get('text_year'),

					);



					if ($product['recurring']['trial']) {

						$recurring = sprintf($this->language->get('text_trial_description'), $this->currency->format($this->tax->calculate($product['recurring']['trial_price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['trial_cycle'], $frequencies[$product['recurring']['trial_frequency']], $product['recurring']['trial_duration']) . ' ';

					}



					if ($product['recurring']['duration']) {

						$recurring .= sprintf($this->language->get('text_payment_description'), $this->currency->format($this->tax->calculate($product['recurring']['price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['cycle'], $frequencies[$product['recurring']['frequency']], $product['recurring']['duration']);

					} else {

						$recurring .= sprintf($this->language->get('text_payment_cancel'), $this->currency->format($this->tax->calculate($product['recurring']['price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['cycle'], $frequencies[$product['recurring']['frequency']], $product['recurring']['duration']);

					}

				}



				$data['products'][] = array(

					'cart_id'    => $product['cart_id'],

					'product_id' => $product['product_id'],

					'name'       => $product['name'],

					'model'      => $product['model'],

					'option'     => $option_data,

					'recurring'  => $recurring,

					'quantity'   => $product['quantity'],

					'subtract'   => $product['subtract'],

					'price'      => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']),

					'total'      => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'], $this->session->data['currency']),

					'href'       => $this->url->link('product/product', 'product_id=' . $product['product_id'])

				);

			}



			// Gift Voucher

			$data['vouchers'] = array();



			if (!empty($this->session->data['vouchers'])) {

				foreach ($this->session->data['vouchers'] as $voucher) {

					$data['vouchers'][] = array(

						'description' => $voucher['description'],

						'amount'      => $this->currency->format($voucher['amount'], $this->session->data['currency'])

					);

				}

			}



			$data['totals'] = array();



			foreach ($order_data['totals'] as $total) {

				$data['totals'][] = array(

					'title' => $total['title'],

					'text'  => $this->currency->format($total['value'], $this->session->data['currency'])

				);

			}

			$this->load->model('api/rest');

			$res = $this->model_api_rest->cartClear($order_data['customer_id']);

			//$data['payment'] = $this->load->controller('extension/payment/' . $payment_method);

			$json['Status'] = 'Success';

				$json['Content'] = $this->session->data['order_id'];

				$json['Mssg'] = $this->language->get('text_success');

			

		} else {

			$json['Status'] = 'Fail';

			$json['Content'] = array();

			$json['Mssg'] = 'Not added';

		}

		

		$this->response->addHeader('Content-Type: application/json');

		$this->response->setOutput(json_encode($json));

	}

	public function countries() {

		$this->load->language('product/manufacturer');

		$this->load->model('api/rest');

		$data['countries'] = array();

		$results = $this->model_api_rest->getCountries();

		

		if(!empty($results)) {

				$json['Status'] = 'Success';

				$json['Content'] = $results;

				$json['Mssg'] = $this->language->get('text_success');

		}else {

			$json['Status'] = 'Fail';

			$json['Content'] = array();

			$json['Mssg'] = 'No Best Sellers';

		}

		$this->response->addHeader('Content-Type: application/json');

		$this->response->setOutput(json_encode($json));

	}

	public function zones() {

		$this->load->language('product/manufacturer');

		$this->load->model('api/rest');

		$data['countries'] = array();

		$results = $this->model_api_rest->getZones($this->request->post['country_id']);

		

		if(!empty($results)) {

				$json['Status'] = 'Success';

				$json['Content'] = $results;

				$json['Mssg'] = $this->language->get('text_success');

		}else {

			$json['Status'] = 'Fail';

			$json['Content'] = array();

			$json['Mssg'] = 'No Best Sellers';

		}

		$this->response->addHeader('Content-Type: application/json');

		$this->response->setOutput(json_encode($json));

	}

	public function saveDeviceDetails() {

    $this->load->model('api/rest');

	    $result = $this->model_api_rest->saveDeviceDetails($this->request->post);

        if(!empty($result)) {

				$json['Status'] = 'Success';

				$json['Content'] = $result;

				$json['Mssg'] = 'Device saved';

		}else {

			$json['Status'] = 'Fail';

			$json['Content'] = array();

			$json['Mssg'] = 'Not saved';

		}

		$this->response->addHeader('Content-Type: application/json');

		$this->response->setOutput(json_encode($json)); 

	}

	public function sendNotifications() {

		$this->load->language('product/manufacturer');

		$API_ACCESS_KEY ='AAAAYuLYXCs:APA91bHwq5H4VJItZzrJ4_18195N1FeCDxf_zaht-DiuvCbBqcKrFKBFjCxxGrJ0r2Lp0jEg-SEx2JOHTijigqFZlXOSfoMo6a7CYOvBWM5ltl5SQ4ulIPtMAiDkpgEEpFIhmgcuVzG1';

    $passphrase = 'Staple@123';

    $this->load->model('api/rest');

    $res = $this->model_api_rest->getDevices();

    foreach($res as $device) {

      $sent_date = date('Y-m-d H:i:s');

      $device['notification_title'] = 'Staple Station - Order';

           

      $msg = array

          (

              'messageBody'  => $device['notification'],

              'notificationTitle' => $device['notification_title'],

       		   'message_id' => $device['message_id'],

               'user_id' => $device['user_id'], 

              'icon'  => 'myicon', 

              'sound' => 'mySound',

              'click_action' => '',

              'notification_type' => $device['notification_type'] 

          );

      $registrationIds = array($device['device_id_txt']);

      

      $fields = array('registration_ids'  => $registrationIds,'data'  => $msg,'priority'=>'high');

	    $headers = array('Authorization: key=' . $API_ACCESS_KEY,'Content-Type: application/json');

	    //echo json_encode( $fields );exit;

	    $ch = curl_init();

	    curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );

	    curl_setopt( $ch,CURLOPT_POST, true );

	    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );

	    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );

	    curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0);

	    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );

	    curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );

	    $result = curl_exec($ch );

	    curl_close( $ch );

			

      $result = json_decode($result);



      $res = $this->model_api_rest->updateNotification($device);

    }

    if(!empty($result)) {

				$json['Status'] = 'Success';

				$json['Content'] = $results;

				$json['Mssg'] = $this->language->get('text_success');

		}else {

			$json['Status'] = 'Fail';

			$json['Content'] = array();

			$json['Mssg'] = 'No Best Sellers';

		}

		$this->response->addHeader('Content-Type: application/json');

		$this->response->setOutput(json_encode($json));

	}



	public function getNotifications() {

		$this->load->model('api/rest');

	  $result = $this->model_api_rest->getNotifications($this->request->post);

    if(!empty($result)) {

				$json['Status'] = 'Success';

				$json['Content'] = $result;

				$json['Mssg'] = 'All notifications';

		}else {

			$json['Status'] = 'Fail';

			$json['Content'] = array();

			$json['Mssg'] = 'No notifications';

		}

		$this->response->addHeader('Content-Type: application/json');

		$this->response->setOutput(json_encode($json)); 

	}

	public function sendOrderOtp() {

		$this->load->language('api/customer');

		$json = array();

		$otp = rand(10000, 99999);

		 //$message = "Your OTP for order confirmation STAPLE STATION is ".$otp.". please do not share the OTP with anyone. Thank you.";
		 $message ="Your OTP for login to STAPLE STATION is ".$otp.". please do not share the OTP with anyone. Thank you.";

		$res = $this->SendSms($this->request->post['mobile'], $message);
		if($res) {

			$json['Status'] = 'Success';

			$json['Content'] = array('mobile'=>$this->request->post['mobile'],

													'OTP'=>$otp);

			$json['Mssg'] = $this->language->get('text_success');

		}else {

			$json['Status'] = 'Fail';

			$json['Content'] = '';

			$json['Mssg'] = 'Not Sent';

		}

		$this->response->addHeader('Content-Type: application/json');

		$this->response->setOutput(json_encode($json));

	}

	public function sendChangePwdOtp() {

		$this->load->language('api/customer');

		$json = array();

		$otp = rand(10000, 99999);

		$message = "To reset your password, validate with this OTP, ".$otp.".";

		$res = $this->SendSms($this->request->post['mobile'], $message);

		if($res) {

			$json['Status'] = 'Success';

			$json['Content'] = array('mobile'=>$this->request->post['mobile'],

													'OTP'=>$otp);

			$json['Mssg'] = $this->language->get('text_success');

		}else {

			$json['Status'] = 'Fail';

			$json['Content'] = '';

			$json['Mssg'] = 'Not Sent';

		}

		$this->response->addHeader('Content-Type: application/json');

		$this->response->setOutput(json_encode($json));

	}
	public function search() {

		$this->load->language('product/search');



		$this->load->model('catalog/category');



		$this->load->model('catalog/product');



		$this->load->model('tool/image');



		if (isset($this->request->get['search'])) {

			$search = $this->request->get['search'];

		} else {

			$search = '';

		}


		if (isset($this->request->get['tag'])) {

			$tag = $this->request->get['tag'];

		} elseif (isset($this->request->get['search'])) {

			$tag = $this->request->get['search'];

		} else {

			$tag = '';

		}



		if (isset($this->request->get['description'])) {

			$description = $this->request->get['description'];

		} else {

			$description = '';

		}



		if (isset($this->request->get['category_id'])) {

			$category_id = $this->request->get['category_id'];

		} else {

			$category_id = 0;

		}



		if (isset($this->request->get['sub_category'])) {

			$sub_category = $this->request->get['sub_category'];

		} else {

			$sub_category = '';

		}



		if (isset($this->request->get['sort'])) {

			$sort = $this->request->get['sort'];

		} else {

			$sort = 'p.sort_order';

		}



		if (isset($this->request->get['order'])) {

			$order = $this->request->get['order'];

		} else {

			$order = 'ASC';

		}



		if (isset($this->request->get['page'])) {

			$page = $this->request->get['page'];

		} else {

			$page = 1;

		}



		if (isset($this->request->get['limit'])) {

			$limit = (int)$this->request->get['limit'];

		} else {

			$limit = $this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit');

		}



		if (isset($this->request->get['search'])) {

			$this->document->setTitle($this->language->get('heading_title') .  ' - ' . $this->request->get['search']);

		} elseif (isset($this->request->get['tag'])) {

			$this->document->setTitle($this->language->get('heading_title') .  ' - ' . $this->language->get('heading_tag') . $this->request->get['tag']);

		} else {

			$this->document->setTitle($this->language->get('heading_title'));

		}



		$data['breadcrumbs'] = array();



		$data['breadcrumbs'][] = array(

			'text' => $this->language->get('text_home'),

			'href' => $this->url->link('common/home')

		);



		$url = '';



		if (isset($this->request->get['search'])) {

			$url .= '&search=' . urlencode(html_entity_decode($this->request->get['search'], ENT_QUOTES, 'UTF-8'));

		}



		if (isset($this->request->get['tag'])) {

			$url .= '&tag=' . urlencode(html_entity_decode($this->request->get['tag'], ENT_QUOTES, 'UTF-8'));

		}



		if (isset($this->request->get['description'])) {

			$url .= '&description=' . $this->request->get['description'];

		}



		if (isset($this->request->get['category_id'])) {

			$url .= '&category_id=' . $this->request->get['category_id'];

		}



		if (isset($this->request->get['sub_category'])) {

			$url .= '&sub_category=' . $this->request->get['sub_category'];

		}



		if (isset($this->request->get['sort'])) {

			$url .= '&sort=' . $this->request->get['sort'];

		}



		if (isset($this->request->get['order'])) {

			$url .= '&order=' . $this->request->get['order'];

		}



		if (isset($this->request->get['page'])) {

			$url .= '&page=' . $this->request->get['page'];

		}



		if (isset($this->request->get['limit'])) {

			$url .= '&limit=' . $this->request->get['limit'];

		}



		$data['breadcrumbs'][] = array(

			'text' => $this->language->get('heading_title'),

			'href' => $this->url->link('product/search', $url)

		);



		if (isset($this->request->get['search'])) {

			$data['heading_title'] = $this->language->get('heading_title') .  ' - ' . $this->request->get['search'];

		} else {

			$data['heading_title'] = $this->language->get('heading_title');

		}



		$data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));



		$data['compare'] = $this->url->link('product/compare');



		$this->load->model('catalog/category');



		// 3 Level Category Search

		$data['categories'] = array();



		$categories_1 = $this->model_catalog_category->getCategories(0);



		foreach ($categories_1 as $category_1) {

			$level_2_data = array();



			$categories_2 = $this->model_catalog_category->getCategories($category_1['category_id']);



			foreach ($categories_2 as $category_2) {

				$level_3_data = array();



				$categories_3 = $this->model_catalog_category->getCategories($category_2['category_id']);



				foreach ($categories_3 as $category_3) {

					$level_3_data[] = array(

						'category_id' => $category_3['category_id'],

						'name'        => $category_3['name'],

					);

				}



				$level_2_data[] = array(

					'category_id' => $category_2['category_id'],

					'name'        => $category_2['name'],

					'children'    => $level_3_data

				);

			}



			$data['categories'][] = array(

				'category_id' => $category_1['category_id'],

				'name'        => $category_1['name'],

				'children'    => $level_2_data

			);

		}



		$data['products'] = array();



		if (isset($this->request->get['search']) || isset($this->request->get['tag'])) {

			$filter_data = array(

				'filter_name'         => $search,

				'filter_tag'          => $tag,

				'filter_description'  => $description,

				'filter_category_id'  => $category_id,

				'filter_sub_category' => $sub_category,

				'sort'                => $sort,

				'order'               => $order,

				'start'               => ($page - 1) * $limit,

				'limit'               => $limit

			);



			$product_total = $this->model_catalog_product->getTotalProducts($filter_data);



			$results = $this->model_catalog_product->getProducts($filter_data);



			foreach ($results as $result) {

				if ($result['image']) {

					$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));

				} else {

					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));

				}



				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {

					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);

				} else {

					$price = false;

				}



				if ((float)$result['special']) {

					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);

				} else {

					$special = false;

				}



				if ($this->config->get('config_tax')) {

					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);

				} else {

					$tax = false;

				}



				if ($this->config->get('config_review_status')) {

					$rating = (int)$result['rating'];

				} else {

					$rating = false;

				}



				/* inspire Images Start */



				$insp_data['insp_images'] = array();

				$insp_results = $this->model_catalog_product->getProductImages($result['product_id']);



				



				foreach ($insp_results as $insp_result) {

					$insp_data['insp_images'][] = array('popup' => $this->model_tool_image->resize($insp_result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height')));

				}



				if($result['special'] > 0 AND $result['special'] != NULL ){

				$tag_per = ($result['special']*100)/$result['price'];

				$tag_per = round($tag_per);

				if($tag_per == 0){

				$tag_per = 1;

				}else{

				$tag_per = 100-$tag_per;

				}

				$tag = $result['price'] - $result['special'];

				}else{

				$tag = 0;

				$tag_per = 0;

				}

				



				/* End */



				$data['products'][] = array(

					'product_id'  => $result['product_id'],

					'thumb'       => $image,

					'tag'       => $tag,

					'tag_per' => $tag_per,

					'name'        => $result['name'],

					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',

					'price'       => $price,

					'special'     => $special,

					'tax'         => $tax,

					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,

					'rating'      => $result['rating'],

					 // Add images Data 

					'insp_images' => $insp_data['insp_images'],

					//End



					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'] . $url)

				);

			}



			$url = '';



			if (isset($this->request->get['search'])) {

				$url .= '&search=' . urlencode(html_entity_decode($this->request->get['search'], ENT_QUOTES, 'UTF-8'));

			}



			if (isset($this->request->get['tag'])) {

				$url .= '&tag=' . urlencode(html_entity_decode($this->request->get['tag'], ENT_QUOTES, 'UTF-8'));

			}



			if (isset($this->request->get['description'])) {

				$url .= '&description=' . $this->request->get['description'];

			}



			if (isset($this->request->get['category_id'])) {

				$url .= '&category_id=' . $this->request->get['category_id'];

			}



			if (isset($this->request->get['sub_category'])) {

				$url .= '&sub_category=' . $this->request->get['sub_category'];

			}



			if (isset($this->request->get['limit'])) {

				$url .= '&limit=' . $this->request->get['limit'];

			}



			$data['sorts'] = array();



			$data['sorts'][] = array(

				'text'  => $this->language->get('text_default'),

				'value' => 'p.sort_order-ASC',

				'href'  => $this->url->link('product/search', 'sort=p.sort_order&order=ASC' . $url)

			);



			$data['sorts'][] = array(

				'text'  => $this->language->get('text_name_asc'),

				'value' => 'pd.name-ASC',

				'href'  => $this->url->link('product/search', 'sort=pd.name&order=ASC' . $url)

			);



			$data['sorts'][] = array(

				'text'  => $this->language->get('text_name_desc'),

				'value' => 'pd.name-DESC',

				'href'  => $this->url->link('product/search', 'sort=pd.name&order=DESC' . $url)

			);



			$data['sorts'][] = array(

				'text'  => $this->language->get('text_price_asc'),

				'value' => 'p.price-ASC',

				'href'  => $this->url->link('product/search', 'sort=p.price&order=ASC' . $url)

			);



			$data['sorts'][] = array(

				'text'  => $this->language->get('text_price_desc'),

				'value' => 'p.price-DESC',

				'href'  => $this->url->link('product/search', 'sort=p.price&order=DESC' . $url)

			);



			if ($this->config->get('config_review_status')) {

				$data['sorts'][] = array(

					'text'  => $this->language->get('text_rating_desc'),

					'value' => 'rating-DESC',

					'href'  => $this->url->link('product/search', 'sort=rating&order=DESC' . $url)

				);



				$data['sorts'][] = array(

					'text'  => $this->language->get('text_rating_asc'),

					'value' => 'rating-ASC',

					'href'  => $this->url->link('product/search', 'sort=rating&order=ASC' . $url)

				);

			}



			$data['sorts'][] = array(

				'text'  => $this->language->get('text_model_asc'),

				'value' => 'p.model-ASC',

				'href'  => $this->url->link('product/search', 'sort=p.model&order=ASC' . $url)

			);



			$data['sorts'][] = array(

				'text'  => $this->language->get('text_model_desc'),

				'value' => 'p.model-DESC',

				'href'  => $this->url->link('product/search', 'sort=p.model&order=DESC' . $url)

			);



			$url = '';



			if (isset($this->request->get['search'])) {

				$url .= '&search=' . urlencode(html_entity_decode($this->request->get['search'], ENT_QUOTES, 'UTF-8'));

			}



			if (isset($this->request->get['tag'])) {

				$url .= '&tag=' . urlencode(html_entity_decode($this->request->get['tag'], ENT_QUOTES, 'UTF-8'));

			}



			if (isset($this->request->get['description'])) {

				$url .= '&description=' . $this->request->get['description'];

			}



			if (isset($this->request->get['category_id'])) {

				$url .= '&category_id=' . $this->request->get['category_id'];

			}



			if (isset($this->request->get['sub_category'])) {

				$url .= '&sub_category=' . $this->request->get['sub_category'];

			}



			if (isset($this->request->get['sort'])) {

				$url .= '&sort=' . $this->request->get['sort'];

			}



			if (isset($this->request->get['order'])) {

				$url .= '&order=' . $this->request->get['order'];

			}



			$data['limits'] = array();



			$limits = array_unique(array($this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit'), 25, 50, 75, 100));



			sort($limits);



			foreach($limits as $value) {

				$data['limits'][] = array(

					'text'  => $value,

					'value' => $value,

					'href'  => $this->url->link('product/search', $url . '&limit=' . $value)

				);

			}



			$url = '';



			if (isset($this->request->get['search'])) {

				$url .= '&search=' . urlencode(html_entity_decode($this->request->get['search'], ENT_QUOTES, 'UTF-8'));

			}



			if (isset($this->request->get['tag'])) {

				$url .= '&tag=' . urlencode(html_entity_decode($this->request->get['tag'], ENT_QUOTES, 'UTF-8'));

			}



			if (isset($this->request->get['description'])) {

				$url .= '&description=' . $this->request->get['description'];

			}



			if (isset($this->request->get['category_id'])) {

				$url .= '&category_id=' . $this->request->get['category_id'];

			}



			if (isset($this->request->get['sub_category'])) {

				$url .= '&sub_category=' . $this->request->get['sub_category'];

			}



			if (isset($this->request->get['sort'])) {

				$url .= '&sort=' . $this->request->get['sort'];

			}



			if (isset($this->request->get['order'])) {

				$url .= '&order=' . $this->request->get['order'];

			}



			if (isset($this->request->get['limit'])) {

				$url .= '&limit=' . $this->request->get['limit'];

			}



			$pagination = new Pagination();

			$pagination->total = $product_total;

			$pagination->page = $page;

			$pagination->limit = $limit;

			$pagination->url = $this->url->link('product/search', $url . '&page={page}');



			$data['pagination'] = $pagination->render();



			$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($product_total - $limit)) ? $product_total : ((($page - 1) * $limit) + $limit), $product_total, ceil($product_total / $limit));



			if (isset($this->request->get['search']) && $this->config->get('config_customer_search')) {

				$this->load->model('account/search');



				if ($this->customer->isLogged()) {

					$customer_id = $this->customer->getId();

				} else {

					$customer_id = 0;

				}



				if (isset($this->request->server['REMOTE_ADDR'])) {

					$ip = $this->request->server['REMOTE_ADDR'];

				} else {

					$ip = '';

				}



				$search_data = array(

					'keyword'       => $search,

					'category_id'   => $category_id,

					'sub_category'  => $sub_category,

					'description'   => $description,

					'products'      => $product_total,

					'customer_id'   => $customer_id,

					'ip'            => $ip

				);



				$this->model_account_search->addSearch($search_data);

			}

		}



		$data['search'] = $search;

		$data['description'] = $description;

		$data['category_id'] = $category_id;

		$data['sub_category'] = $sub_category;



		$data['sort'] = $sort;

		$data['order'] = $order;

		$data['limit'] = $limit;
		$this->response->addHeader('Content-Type: application/json');

		$this->response->setOutput(json_encode($data));

	}

}


?>
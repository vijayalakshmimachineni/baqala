<?php
class ControllerCommonHeader extends Controller {
	public function index() {
		$data['title'] = $this->document->getTitle();

		if ($this->request->server['HTTPS']) {
			$data['base'] = HTTPS_SERVER;
		} else {
			$data['base'] = HTTP_SERVER;
		}

		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		$data['scripts'] = $this->document->getScripts();
		$data['lang'] = $this->language->get('code');
		$data['direction'] = $this->language->get('direction');

		$this->load->language('common/header');
		
		$data['text_logged'] = sprintf($this->language->get('text_logged'), $this->user->getUserName());

		if (!isset($this->request->get['user_token']) || !isset($this->session->data['user_token']) || ($this->request->get['user_token'] != $this->session->data['user_token'])) {
			$data['logged'] = '';

			$data['home'] = $this->url->link('common/dashboard', '', true);
		} else {
			$data['logged'] = true;

			$data['home'] = $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true);
			$data['logout'] = $this->url->link('common/logout', 'user_token=' . $this->session->data['user_token'], true);
			$data['profile'] = $this->url->link('common/profile', 'user_token=' . $this->session->data['user_token'], true);

      $data['text_processing_status'] = $this->language->get('text_processing_status');
	  $data['text_complete_status'] = $this->language->get('text_complete_status');
	  $data['text_return'] = $this->language->get('text_return');
      $this->load->language('sale/order');
			$data['orders'] = $this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'], true);
			// Products
			$this->load->model('catalog/product');

			$product_total = $this->model_catalog_product->getTotalProducts(array('filter_quantity' => 0));

			$data['product_total'] = $product_total;
			// Orders
			$this->load->model('sale/order');

			// Processing Orders
			$data['processing_status_total'] = $this->model_sale_order->getTotalOrders(array('filter_order_status' => implode(',', $this->config->get('config_processing_status'))));
			$data['processing_status'] = $this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'] . '&filter_order_status=' . implode(',', $this->config->get('config_processing_status')), 'SSL');

			// Complete Orders
			$data['complete_status_total'] = $this->model_sale_order->getTotalOrders(array('filter_order_status' => implode(',', $this->config->get('config_complete_status'))));
			$data['complete_status'] = $this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'] . '&filter_order_status=' . implode(',', $this->config->get('config_complete_status')), 'SSL');

			// Returns
			$this->load->model('sale/return');

			$return_total = $this->model_sale_return->getTotalReturns(array('filter_return_status_id' => $this->config->get('config_return_status_id')));

			$data['return_total'] = $return_total;

			$data['return'] = $this->url->link('sale/return', 'user_token=' . $this->session->data['user_token'], 'SSL');

			$data['alerts'] = $this->model_sale_order->getTotalOrders(array('filter_order_status' => implode(',', $this->config->get('config_processing_status'))));
			
			$this->load->model('sale/order');
		
		$order_total = $this->model_sale_order->getTotalOrders();

		$data['total'] = $order_total;
      
		
			$this->load->model('user/user');
	
			$this->load->model('tool/image');
	
			$user_info = $this->model_user_user->getUser($this->user->getId());
	
			if ($user_info) {
				$data['firstname'] = $user_info['firstname'];
				$data['lastname'] = $user_info['lastname'];
				$data['username']  = $user_info['username'];
				$data['user_group'] = $user_info['user_group'];
	
				if (is_file(DIR_IMAGE . $user_info['image'])) {
					$data['image'] = $this->model_tool_image->resize($user_info['image'], 45, 45);
				} else {
					$data['image'] = $this->model_tool_image->resize('profile.png', 45, 45);
				}
			} else {
				$data['firstname'] = '';
				$data['lastname'] = '';
				$data['user_group'] = '';
				$data['image'] = '';
			}			
			
			// Online Stores
			$data['stores'] = array();

			$data['stores'][] = array(
				'name' => $this->config->get('config_name'),
				'href' => HTTP_CATALOG
			);

			$this->load->model('setting/store');

			$results = $this->model_setting_store->getStores();

			foreach ($results as $result) {
				$data['stores'][] = array(
					'name' => $result['name'],
					'href' => $result['url']
				);
			}
		}

		return $this->load->view('common/header', $data);
	}
}
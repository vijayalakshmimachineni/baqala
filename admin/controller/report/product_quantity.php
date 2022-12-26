<?php
class ControllerReportProductQuantity extends Controller {
	public function index() {     
		$this->load->language('report/product_quantity');

		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('report/product_quantity', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$this->load->model('report/product');

		$filter_data = array(
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$data['products'] = array();
				
		$product_quantity = $this->model_report_product->getProductsQuantity($data); 
		
		$product_quantity = $this->model_report_product->getProductsQuantity(); 
		
		$results = $this->model_report_product->getProductsQuantity($data);
		
foreach ($results as $result) {
					
			$data['products'][] = array(
				'product_id'    => $result['product_id'],
				'name'  		  => $result['name'],
				'model' 	  	 => $result['model'],
				'quantity'	  => $result['quantity']		
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_id'] = $this->language->get('column_id');
		$data['column_name'] = $this->language->get('column_name');
		$data['column_model'] = $this->language->get('column_model');
		$data['column_quantity'] = $this->language->get('column_quantity');
		$data['column_action'] = $this->language->get('column_action');
		
 
		$data['button_reset'] = $this->language->get('button_reset');
		$data['token'] = $this->session->data['token'];

		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['reset'] = $this->url->link('report/product_viewed/reset', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->session->data['error'])) {
			$data['error_warning'] = $this->session->data['error'];

			unset($this->session->data['error']);
		} elseif (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$pagination = new Pagination();
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('report/product_viewed', 'token=' . $this->session->data['token'] . '&page={page}', 'SSL');



		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('report/product_quantity.tpl', $data));
	}

	public function reset() {
		$this->load->language('report/product_quantity');

		if (!$this->user->hasPermission('modify', 'report/product_quantity')) {
			$this->session->data['error'] = $this->language->get('error_permission');
		} else {
			$this->load->model('report/product');

			$this->model_report_product->reset();

			$this->session->data['success'] = $this->language->get('text_success');
		}

		$this->response->redirect($this->url->link('report/product_quantity', 'token=' . $this->session->data['token'], 'SSL'));
	}
}
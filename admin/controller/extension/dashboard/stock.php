<?php
class ControllerExtensionDashboardstock extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/dashboard/stock');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('dashboard_stock', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=dashboard', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=dashboard', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/dashboard/stock', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/dashboard/stock', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=dashboard', true);

		if (isset($this->request->post['dashboard_stock_width'])) {
			$data['dashboard_stock_width'] = $this->request->post['dashboard_stock_width'];
		} else {
			$data['dashboard_stock_width'] = $this->config->get('dashboard_stock_width');
		}
		
		$data['columns'] = array();
		
		for ($i = 3; $i <= 12; $i++) {
			$data['columns'][] = $i;
		}
				
		if (isset($this->request->post['dashboard_stock_status'])) {
			$data['dashboard_stock_status'] = $this->request->post['dashboard_stock_status'];
		} else {
			$data['dashboard_stock_status'] = $this->config->get('dashboard_stock_status');
		}

		if (isset($this->request->post['dashboard_stock_sort_order'])) {
			$data['dashboard_stock_sort_order'] = $this->request->post['dashboard_stock_sort_order'];
		} else {
			$data['dashboard_stock_sort_order'] = $this->config->get('dashboard_stock_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/dashboard/stock_form', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/dashboard/stock')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
	
	public function dashboard() {
		$this->load->language('extension/dashboard/stock');
		$data['user_token'] = $this->session->data['user_token'];
		// Total Orders
		$this->load->model('catalog/product');
		$data['products'] = $this->model_catalog_product->getlowproducts();
		$data['order'] = $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'], true);
		return $this->load->view('extension/dashboard/stock_info', $data);
	}
}

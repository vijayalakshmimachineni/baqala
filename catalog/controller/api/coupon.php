<?php
class ControllerApiCoupon extends Controller {
	public function index() {
		$this->load->language('api/coupon');

		// Delete past coupon in case there is an error
		unset($this->session->data['coupon']);

		$json = array();

		if (!isset($this->session->data['api_id'])) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			$this->load->model('extension/total/coupon');

			if (isset($this->request->post['coupon'])) {
				$coupon = $this->request->post['coupon'];
			} else {
				$coupon = '';
			}

			$coupon_info = $this->model_extension_total_coupon->getCoupon($coupon);

			if ($coupon_info) {
				$this->session->data['coupon'] = $this->request->post['coupon'];
				$data['Status'] = 'Success';
				$data['Content'] = $coupon_info;
				$data['Mssg'] = $this->language->get('text_success');
		
				//$json['success'] = $this->language->get('text_success');
			} else {
				$data['Status'] = 'Fail';
				$data['Content'] = '';
				$data['Mssg'] = $this->language->get('error_coupon');
				//$json['error'] = $this->language->get('error_coupon');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));
	}
	public function list() {
		$this->load->language('api/coupon');
		$data = array();
		$res = array();
		$this->load->model('extension/total/coupon');
		$res = $this->model_extension_total_coupon->getCoupons();
		if ($res) {
			//$this->session->data['coupon'] = $this->request->post['coupon'];
			$data['Status'] = 'Success';
			$data['Content'] = $res;
			$data['Mssg'] = 'Coupons List';
	
			//$json['success'] = $this->language->get('text_success');
		} else {
			$data['Status'] = 'Fail';
			$data['Content'] = '';
			$data['Mssg'] = 'No Coupons';
			//$json['error'] = $this->language->get('error_coupon');
		}
			$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));
	}
}

<?php
class ControllerCheckoutSuccess extends Controller {
	public function index() {
		$this->load->language('checkout/success');
        		$data['breadcrumbs'] = array();

		if (isset($this->session->data['order_id'])) {
			$this->load->model('checkout/order');
 		$data['orderDetails'] = "Order Id : ".$this->session->data['order_id'];
 		$this->load->model('checkout/order');
       $data['orderuserDetails'] = $this->model_checkout_order->getOrder($this->session->data['order_id']);
       $user = $data['orderuserDetails']['firstname']." ".$data['orderuserDetails']['lastname'];
       $mobile = $data['orderuserDetails']['telephone'];
 		$message = "Hi ".$user.", thank you for shopping with us. Here is your order No. ".$this->session->data['order_id']." Your items will be shipped shortly.";

		$res = $this->SendSms($mobile, $message);

			$this->cart->clear(); 

			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['guest']);
			unset($this->session->data['comment']);
			unset($this->session->data['order_id']);
			unset($this->session->data['coupon']);
			unset($this->session->data['reward']);
			unset($this->session->data['voucher']);
			unset($this->session->data['vouchers']);
			unset($this->session->data['totals']);
		}

		$this->document->setTitle($this->language->get('heading_title'));


		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_basket'),
			'href' => $this->url->link('checkout/cart')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_checkout'),
			'href' => $this->url->link('checkout/checkout', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_success'),
			'href' => $this->url->link('checkout/success')
		);

		if ($this->customer->isLogged()) {
			$data['text_message'] = sprintf($this->language->get('text_customer'), $this->url->link('account/account', '', true), $this->url->link('account/order', '', true), $this->url->link('account/download', '', true), $this->url->link('information/contact'));
		} else {
			$data['text_message'] = sprintf($this->language->get('text_guest'), $this->url->link('information/contact'));
		}

		$data['continue'] = $this->url->link('common/home');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('common/success', $data));
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
}
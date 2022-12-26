<?php
class ModelApiSubTotal extends Model {
	public function getTotal($total, $user_id=0) {
		$this->load->language('extension/total/sub_total');
		$this->load->model('api/rest');
		$sub_total = $this->model_api_rest->getSubTotal($user_id);

		if (!empty($this->session->data['vouchers'])) {
			foreach ($this->session->data['vouchers'] as $voucher) {
				$sub_total += $voucher['amount'];
			}
		}

		$total['totals'][] = array(
			'code'       => 'sub_total',
			'title'      => $this->language->get('text_sub_total'),
			'value'      => $sub_total,
			'sort_order' => $this->config->get('sub_total_sort_order')
		);

		$total['total'] += @$sub_total;
	}
}

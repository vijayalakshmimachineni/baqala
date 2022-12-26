<?php
class ControllerExtensionModuleManufacturer extends Controller {
	public function index() {
		$this->load->language('extension/module/manufacturer');

		$data['heading_title'] = $this->language->get('heading_title');
		
		if (isset($this->request->get['manufacturer_id'])) {
			$data['manufacturer_id'] =  (string)$this->request->get['manufacturer_id'];
		} else {
			$data['manufacturer_id'] = array();
		}

		$this->load->model('catalog/manufacturer');

		$this->load->model('catalog/product');

		$data['manufacturers'] = array();

		$manufacturers = $this->model_catalog_manufacturer->getManufacturers();

		foreach ($manufacturers as $manufacturer) {
		
			$data['manufacturers'][] = array(
				'manufacturer_id' => $manufacturer['manufacturer_id'],
				'image_name'         => $this->model_tool_image->resize($manufacturer['image'],490,300),
				'name'        => $manufacturer['name'] ,
				'href'        => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $manufacturer['manufacturer_id'])
			);
		}

		return $this->load->view('extension/module/manufacturer', $data);
	}
}
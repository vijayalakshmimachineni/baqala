<?php
class ModelExtensionReportManufacturer extends Model {
	public function getManufacturers($data = array()) {
		$sql = "SELECT m.manufacturer_id, m.name, COUNT(DISTINCT o.order_id) AS `orders`, SUM(o.total) AS total FROM `" . DB_PREFIX . "manufacturer` m, `" . DB_PREFIX . "order_product` o WHERE m.manufacturer_id IN (SELECT  manufacturer_id FROM `" . DB_PREFIX . "product` WHERE product_id = o.product_id)";

		$sql .= " GROUP BY m.manufacturer_id ORDER BY total DESC";

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalManufacturers($data = array()) {
		$sql = "SELECT COUNT(DISTINCT manufacturer_id) AS total FROM `" . DB_PREFIX . "manufacturer`";
		$query = $this->db->query($sql);

		return $query->row['total'];
	}
}
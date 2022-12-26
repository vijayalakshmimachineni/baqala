<?php
class ModelReportProductCustom extends Model {
    public function getCustomProducts($data = array()) {
        $sql = "SELECT pd.name, p.model, p.viewed FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) 
        LEFT JOIN " . DB_PREFIX . "order_product op ON p.product_id = op.product_id
        WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.viewed > 0 
        AND ISNULL(op.product_id) ORDER BY p.viewed DESC";

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

    public function getTotalCustomProducts($data) {
        $sql = "SELECT count(*) as total FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) 
        LEFT JOIN " . DB_PREFIX . "order_product op ON p.product_id = op.product_id
        WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.viewed > 0 
        AND ISNULL(op.product_id)";

        $query = $this->db->query($sql);

        return $query->row['total'];
    }
}
?>
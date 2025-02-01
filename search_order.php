<?php
require_once('../wp-load.php');
include_once('../wp-content/plugins/woocommerce/includes/class-wc-order.php');

if (isset($_POST['customer_name'])) {
    $customer_name = sanitize_text_field($_POST['customer_name']);
    $args = [
        'status' => 'any',
        'limit' => -1,
    ];
    $orders = wc_get_orders($args);

    $options = '';
    foreach ($orders as $order) {
        $order_name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
        if (stripos($order_name, $customer_name) !== false) {
            $order_number = $order->get_order_number();
            $options .= "<option value='{$order->get_id()}'>{$order_number} - {$order_name}</option>";
        }
    }

    if (!empty($options)) {
        echo json_encode(['success' => true, 'options' => $options]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se encontraron pedidos para ese nombre.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Nombre del cliente no proporcionado']);
}

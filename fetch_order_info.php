<?php
define('WP_USE_THEMES', false);
require_once('../wp-load.php');

if (isset($_POST['order_id']) && class_exists('WooCommerce')) {
    $order_id = intval($_POST['order_id']);
    $order = wc_get_order($order_id);

    if ($order) {
        $billing = $order->get_address('billing');

        // Obtener los productos del pedido
        $products = array();
        foreach ($order->get_items() as $item_id => $item) {
            $_product = $item->get_product();
            $product_data = array(
                'name' => $_product ? $_product->get_name() : 'Producto desconocido',
                'quantity' => $item->get_quantity(),
                'subtotal' => wc_price($item->get_subtotal()),
                'options' => array()
            );

            // Obtener las opciones del producto
            $meta_data = $item->get_formatted_meta_data();
            foreach ($meta_data as $meta) {
                $product_data['options'][] = $meta->display_value;
            }

            $products[] = $product_data;
        }

        $response = array(
            'success' => true,
            'customer_name' => $billing['first_name'] . ' ' . $billing['last_name'],
            'customer_email' => $billing['email'],
            'customer_phone' => $billing['phone'],
            'customer_address' => $billing['address_1'] . ', ' . $billing['city'] . ', ' . $billing['state'] . ', ' . $billing['postcode'],
            'products' => $products
        );

        echo json_encode($response);
        exit();
    }
}

echo json_encode(array('success' => false));
exit();

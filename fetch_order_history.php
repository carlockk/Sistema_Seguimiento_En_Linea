<?php
define('WP_USE_THEMES', false);
require_once('../wp-load.php');

if (isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);
    $order = wc_get_order($order_id);

    if ($order) {
        $notes = wc_get_order_notes(['order_id' => $order_id]);
        foreach ($notes as $note) {
            echo "<div class='order-history-item'>";
            echo "<p><strong>" . esc_html($note->content) . "</strong></p>";
            echo "<p><em>" . esc_html($note->date_created->date('d-m-Y H:i')) . "</em></p>";
            echo "</div>";
        }
    } else {
        echo "<p>No se encontró el historial del pedido.</p>";
    }
} else {
    echo "<p>Error: ID de pedido no proporcionado.</p>";
}

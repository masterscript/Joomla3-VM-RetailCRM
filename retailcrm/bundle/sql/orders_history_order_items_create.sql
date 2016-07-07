INSERT INTO
    `s_virtuemart_order_items`
(
    `virtuemart_order_id`,
    `virtuemart_order_item_id`,
    `order_item_name`,
    `product_quantity`,
    `product_item_price`
)
SELECT
     IF(:order_id IS NOT NULL, :order_id, ''),
     IF(:items_catalog_items_id IS NOT NULL, :items_catalog_items_id, ''),
     IF(:orders_items_name IS NOT NULL, :orders_items_name, ''),
     IF(:orders_items_quantity IS NOT NULL, :orders_items_quantity, ''),
     IF(:orders_items_price IS NOT NULL, :orders_items_price, '')
FROM
    `s_virtuemart_order_items`

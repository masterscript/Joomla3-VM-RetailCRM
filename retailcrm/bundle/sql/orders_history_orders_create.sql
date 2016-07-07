INSERT IGNORE INTO
    `s_virtuemart_orders`
(
	`virtuemart_user_id`,
    `virtuemart_shipmentmethod_id`,
    `virtuemart_paymentmethod_id`,
    `order_status`,
    `created_on`,
    `virtuemart_vendor_id`,
    `virtuemart_order_id`
)
SELECT
    IF(:customerId IS NOT NULL, :customerId, ''),
    IF(:deliveryType IS NOT NULL, :deliveryType, ''),
    IF(:paymentType IS NOT NULL, :paymentType, ''),
    IF(:paymentStatus IS NOT NULL, :paymentStatus, ''),
    IF(:createdAt IS NOT NULL, :createdAt, NOW()),
    1,
    (MAX(`virtuemart_order_id`) + 1)
FROM
    `s_virtuemart_orders`

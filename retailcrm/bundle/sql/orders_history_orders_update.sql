UPDATE IGNORE `s_virtuemart_orders`
SET 
    IF(:customerId IS NOT NULL, `virtuemart_user_id` = :customerId, ''),
    IF(:deliveryType IS NOT NULL, `virtuemart_shipmentmethod_id` = :deliveryType, ''),
    IF(:paymentType IS NOT NULL, `virtuemart_paymentmethod_id` = :paymentType, ''),
    IF(:paymentStatus IS NOT NULL,  `order_status` = :paymentStatus, 0),
    IF(:createdAt IS NOT NULL, `created_on` = :createdAt, NOW())
WHERE
    `virtuemart_order_id` = :orderExternalId;

SELECT
    o.virtuemart_order_id AS externalId,
    (
        CASE
            o.virtuemart_user_id
        WHEN
            0
        THEN
            NULL
        ELSE
            o.virtuemart_user_id
        END
    ) AS customerId,
    us.first_name AS firstName,
    us.last_name AS lastName,
    us.middle_name AS patronymic,
    us.email AS email,
    us.phone_1 AS phone,
    (
        CASE
            o.virtuemart_shipmentmethod_id
        WHEN
            0
        THEN
            NULL
        ELSE
            o.virtuemart_shipmentmethod_id
        END
    ) AS deliveryIndex,
    con.country_name AS deliveryCountry,
    reg.state_name  AS deliveryRegion,
    us.city AS deliveryCity,
    us.address_1 AS deliveryAddress,
    del.shipment_element AS deliveryType,
    de.shipment_name AS deliveryService,
    o.virtuemart_paymentmethod_id AS paymentType,
    (
        CASE
            o.order_status
        WHEN
            'U'
        THEN
            'paid'
        WHEN
            'C'
        THEN
            'paid'
		WHEN
            'P'
        THEN
            'not-paid'
        ELSE
            'more'
        END
    ) AS paymentStatus,
    o.created_on AS createdAt,
    (
        SELECT
            GROUP_CONCAT(
                CONCAT_WS(
                    ';',
                    virtuemart_order_item_id,
                    order_item_name,
                    product_quantity,
                    product_final_price
                )
                SEPARATOR '|'
            )
        FROM
            s_virtuemart_order_items
        WHERE
           virtuemart_order_item_id != 0
        AND
            virtuemart_order_id = o.virtuemart_order_id
        GROUP BY
            virtuemart_order_id
    ) AS items
FROM
    s_virtuemart_orders AS o
INNER JOIN
    s_virtuemart_order_userinfos AS us
ON
    (us.virtuemart_order_id = o.virtuemart_order_id)
INNER JOIN
    s_virtuemart_shipmentmethods_en_gb AS de
ON
    (de.virtuemart_shipmentmethod_id = o.virtuemart_shipmentmethod_id)
INNER JOIN
    s_virtuemart_shipmentmethods AS del
ON
    (del.virtuemart_shipmentmethod_id = o.virtuemart_shipmentmethod_id)
INNER JOIN
    s_virtuemart_states AS reg
ON
    (reg.virtuemart_state_id = us.virtuemart_state_id)
INNER JOIN
    s_virtuemart_countries AS con
ON
    (con.virtuemart_country_id = us.virtuemart_country_id)
WHERE (
    (
        ( (SELECT COUNT(virtuemart_order_id) FROM s_virtuemart_order_userinfos
        WHERE virtuemart_order_id = us.virtuemart_order_id
        GROUP BY virtuemart_order_id) > 1 ) AND (address_type = 'ST')
    ) OR ( (SELECT COUNT(virtuemart_order_id) FROM s_virtuemart_order_userinfos
        WHERE virtuemart_order_id = us.virtuemart_order_id
        GROUP BY virtuemart_order_id) = 1 )
)
AND
	FIND_IN_SET(o.virtuemart_order_id, :orderIds)
ORDER BY
    o.virtuemart_order_id ASC

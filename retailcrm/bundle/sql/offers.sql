SELECT
	pp.virtuemart_product_price_id AS id,
	p.virtuemart_product_id AS productId,
	product_in_stock AS quantity,
	CONCAT('http://',veg.vendor_url,'/index.php/component/virtuemart/',peg.slug) AS url,
	(product_price + 
	(SELECT GROUP_CONCAT(CONCAT_WS(';',
		CASE calc_value_mathop
			WHEN '+' THEN
				calc_value
			WHEN '-' THEN
				-calc_value
			WHEN '+%' THEN
				((calc_value / 100) * product_price)
			WHEN '-%' THEN
				-((calc_value / 100) * product_price)
			ELSE
				0
		END
            )SEPARATOR '|')
	FROM s_virtuemart_calcs AS c
	INNER JOIN s_virtuemart_products AS ps ON ps.virtuemart_vendor_id = c.virtuemart_vendor_id
	INNER JOIN s_virtuemart_product_prices AS pp ON pp.virtuemart_product_id = ps.virtuemart_product_id
	WHERE ps.virtuemart_product_id = p.virtuemart_product_id)) AS price,
    product_price AS purchasePrice,
    virtuemart_category_id AS categoryId,
    CONCAT('http://',veg.vendor_url,'/',file_url) AS picture,
    CONCAT(mf_name, ' ', product_name) AS name,
    product_name AS productName,
    product_sku AS article,
    CONCAT(product_length, 'x', product_width, 'x', product_height) AS product_size,
    CONCAT(product_weight, ' ',product_weight_uom) AS weight,
    mf_name AS vendor
FROM s_virtuemart_products AS p
INNER JOIN s_virtuemart_product_prices AS pp ON pp.virtuemart_product_id = p.virtuemart_product_id
INNER JOIN s_virtuemart_product_categories AS pc ON pc.virtuemart_product_id = p.virtuemart_product_id
LEFT JOIN s_virtuemart_product_medias AS pm ON pm.virtuemart_product_id = p.virtuemart_product_id
LEFT JOIN s_virtuemart_medias AS m ON m.virtuemart_media_id = pm.virtuemart_media_id
INNER JOIN s_virtuemart_products_en_gb AS peg ON peg.virtuemart_product_id = p.virtuemart_product_id
INNER JOIN s_virtuemart_product_manufacturers AS pmf ON pmf.virtuemart_product_id = p.virtuemart_product_id
INNER JOIN s_virtuemart_manufacturers_en_gb AS meg ON meg.virtuemart_manufacturer_id = pmf.virtuemart_manufacturer_id
INNER JOIN s_virtuemart_vendors_en_gb AS veg ON veg.virtuemart_vendor_id = p.virtuemart_vendor_id

SELECT
	c.virtuemart_category_id AS id,
	category_parent_id AS parentId,
	category_name AS name
FROM
	s_virtuemart_categories AS c
LEFT JOIN s_virtuemart_category_categories AS cc ON cc.id = c.virtuemart_category_id
INNER JOIN s_virtuemart_categories_en_gb AS ceg ON ceg.virtuemart_category_id = c.virtuemart_category_id

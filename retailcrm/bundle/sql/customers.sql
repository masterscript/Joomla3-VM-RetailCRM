SELECT
	virtuemart_user_id AS externalId,
	first_name AS firstName,
	last_name AS lastName,
	middle_name AS patronymic,
	phone_1 AS phones,
	address_1 AS address
FROM
	s_virtuemart_order_userinfos

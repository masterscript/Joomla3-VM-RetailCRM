INSERT IGNORE INTO
	`s_virtuemart_order_userinfos`
(
	`virtuemart_user_id`,
	`address_type`,
	`first_name`,
	`last_name`,
	`middle_name`,
	`phone_1`,
	`zip`,
	`address_1`
)
SELECT 
	IF(:customerId IS NOT NULL, :customerId, (MAX(`virtuemart_user_id`) + 1)),
	'BT',
	IF(:firstName IS NOT NULL, :firstName, ''),
	IF(:lastName IS NOT NULL, :lastName, ''),
	IF(:patronymic IS NOT NULL, :patronymic, ''),
	IF(:phone IS NOT NULL, :phone, ''),
	IF(:postcode IS NOT NULL, :postcode, ''),
	IF(:address IS NOT NULL, :address, '')	
FROM 
	`s_virtuemart_order_userinfos`

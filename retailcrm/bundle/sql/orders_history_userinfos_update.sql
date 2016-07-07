UPDATE IGNORE `s_virtuemart_userinfos`
SET
    IF(:firstName IS NOT NULL,  `first_name` = :firstName, ''),
    IF(:lastName IS NOT NULL, `last_name` = :lastName, ''),
    IF(:patronymic IS NOT NULL, `middle_name` = :patronymic, ''),
    IF(:phone IS NOT NULL, `phone_1` = :phone, ''),
    IF(:postcode IS NOT NULL, `zip` = :postcode, ''),
    IF(:address IS NOT NULL, `address_1` = :address, '')
WHERE
    `virtuemart_user_id` = :customerId;

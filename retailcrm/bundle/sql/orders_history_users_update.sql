UPDATE IGNORE `s_users`
SET 
	IF(:firstName IS NOT NULL, `name` = :firstName, '')
WHERE
    `id` = :customerId;

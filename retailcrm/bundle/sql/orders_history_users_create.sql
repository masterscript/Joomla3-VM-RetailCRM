INSERT IGNORE INTO
	`s_users`
(
	`id`,
	`name`,
	`username`,
	`password`
)
SELECT 
	IF(:customerId IS NOT NULL, :customerId, (MAX(`id`) + 1)),
	IF(:firstName IS NOT NULL, :firstName, (MAX(`id`) + 1)),
	IF(:customerId IS NOT NULL, :customerId, (MAX(`id`) + 1)),
	IF(:customerId IS NOT NULL, :customerId, (MAX(`id`) + 1))
FROM 
	`s_users`

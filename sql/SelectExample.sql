SELECT
m.memberId,
m.username,
m.firstName,
m.lastName,
m.street
FROM members AS m
WHERE
	firstName = "Joe" 
ORDER BY lastName, firstName DESCfuri01dbfuri01db
SELECT
m.memberId,
m.username,
m.firstName,
m.lastName
FROM members AS m
WHERE
firstName = "Joe"
ORDER BY lastName, firstName DESC
Overdose Customer Password Hash Upgrade
=================

Tags: Bcrypt, $2y$10$

Fixes customer login issue from Magento 1 migration to Magento 2.

This module checks if encryption of logged in user is Bcrypt and rehashes it to new algorithm.

## TODO

Create last password upgrade checker and send admin nothification to disable this module.

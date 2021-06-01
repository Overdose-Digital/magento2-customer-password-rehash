#Overdose Customer Password Hash Upgrade

Tags: Bcrypt, $2y$10$

Fixes customer login issue from Magento 1 migration to Magento 2.
It checks if encryption of logged in user is Bcrypt and rehashes it to new algorithm.
Supports Magento Community and Enterprise.

## Install instructions:
- If NOT packegist: `composer config repositories.overdose/magento2-customer-password-rehash vcs git@github.com:Overdose-Digital/magento2-customer-password-rehash.git`
- Allways: `composer require overdose/magento2-customer-password-rehash:1.1.0` (DISCLAYMER: check version before run this command)

## Functionality
- test

## TODO
Create last password upgrade checker and send admin nothification to disable this module.

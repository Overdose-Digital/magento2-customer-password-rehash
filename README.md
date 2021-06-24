# Overdose Customer Password Hash Upgrade
This module rehashes old hashed passwords and checks passwords in db for rehashing.

## Install instructions:
- If NOT packegist: `composer config repositories.overdose/magento2-customer-password-rehash vcs git@github.com:Overdose-Digital/magento2-customer-password-rehash.git`
- Allways: `composer require overdose/magento2-customer-password-rehash:1.1.0` (DISCLAYMER: check version before run this command).

**NB:** Before using pls make sure that you set general contact in Store Email Addresses.

## Functionality
- Tags: Bcrypt, $2y$10$
- Fixes customer login issue from Magento 1 migration to Magento 2.
- It checks if encryption of logged in user is Bcrypt and rehashes it to new algorithm.
- Checks all passwords in db and if all of them are rehashed send info mail to admin to disable this module.
- Can be configured amount of months which will be subtracted from current date for missing check users who visited store a long time ago.
- Supports Magento Community and Enterprise. 

## Configurations:
- `rehash_passwd/general/od_amount_months`. See `const KEY_OD_AMOUNT_MONTHS`, Amount months which need to be subtracted from now for missing check users, who visited store a long time ago. Default value - 12 months.

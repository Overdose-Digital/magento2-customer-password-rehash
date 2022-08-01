# Overdose Customer Password Hash Upgrade
This module rehashes old hashed passwords and checks passwords in db for rehashing.

## Install instructions
- `composer config repositories.overdose/magento2-customer-password-rehash vcs git@github.com:Overdose-Digital/magento2-customer-password-rehash.git`
- `composer require overdose/magento2-customer-password-rehash`.

**NB:** Before using pls make sure that you set general contact in Store Email Addresses.

## Functionality
- Fixes customer login issue from Magento 1 migration to Magento 2.
- It checks if encryption of logged in user is Bcrypt and rehashes it to new algorithm.
- Checks all passwords in db and if all of them are rehashed send info mail to admin to disable this module. (Check Configurations section)
- Supports Magento Community and Enterprise. 

## Configurations
- `rehash_passwd/general/od_amount_months`. Amount months that need to be subtracted from now for missing check users who visited the store a long time ago. Default value - 12 months. See `const KEY_OD_AMOUNT_MONTHS`.

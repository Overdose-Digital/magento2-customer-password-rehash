Overdose Customer Password Hash Upgrade
=================

Tags: Bcrypt, $2y$10$

Fixes customer login issue from Magento 1 migration to Magento 2.
It checks if encryption of logged in user is Bcrypt and rehashes it to new algorithm.

## Installation

Add repo to your local config:
```
composer config repositories.od-password-rehash-github vcs https://github.com/Overdose-Digital/magento2-customer-password-rehash.git
```

Install package:
```
composer require overdose/module-cmscontent
```

## TODO

Create last password upgrade checker and send admin nothification to disable this module.

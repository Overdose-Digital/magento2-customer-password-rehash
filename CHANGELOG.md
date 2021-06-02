# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.0] - 31-05-2021
### Added
- Cron which checks all passwords in Db for rehash. When all of them are rehashed, will send mail to admin for disabling module.
- Added config for amount of months which admin can set for missing old customers (ancient visitors)


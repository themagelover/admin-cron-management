# Admin Cron Management

Give the admin user the possibility to check the scheduled cron jobs, and run the cron manually.
This extension is totally free, easy to install and use.

The code is written in the simplest way, easy to understand and extend.
No timeline is needed, and no direct commands are involved.

The best one for security and performance!

## Installation

#### Install via composer
```
composer require themagelover/admin-cron-management
```

#### Deploy the extension
```
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy
bin/magento module:enable TheMageLover_AdminCronManagement
```

## User guide

#### Cron Scheduled Jobs
_System > Tools > Cron Scheduled Jobs_

- We list down all the scheduled jobs - getting from the cron_schedule table 
- User can sort, filter, and export the records
- There is an option to delete the record if needed

#### Cron Executor
_System > Tools > Cron Executor_
- We list down all the existing cron
- User can check the Job Code, Group, Status, Instance, Method, and Scheduled Time
- User can sort, filter, and export the records
- There are some actions in the **Actions** dropdown
    - Enable/Disable the Cron
    - Run the cron manually
- We can run a single/multiple cron at the same time
- Message will be displayed to the User after the cron run
- The log will be recorded at the **Cron Schedule Jobs** menu


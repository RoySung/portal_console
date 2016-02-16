Portal Console
==============
<img src="https://googledrive.com/host/0B7QSKrzy_mRTZGp0Vlh0UDJKbEU" width="350"/>

Introduction
------------
**Portal Console** is a support tool that can more easily to create test environment or use other current exosite portal api tools.

This project is under dev, welcome any ideas or suggestions, and you can also generate new command by follow Generate Command

Installation
-------------
Portal Console utilizes [Composer](https://getcomposer.org/) to manage its dependencies. So, before using Portal Console, make sure you have Composer installed on your machine.

```
~$ git clone git@github.com:GeneXu/portal_console.git
~$ cd portal_console/
~$ composer install
```
> **Note:** After composer isntall dependencies complete, it will auto create config parameters file and tell you provide database_host, database_port, database_name...etc, but we will not use those config, just let it be default.

Generate Command
----------------
### Step 1 : Create new command
```
~$ php bin/console generate:command
Bundle name: AppBundle 
Command name: ${tools name}:${feature] 
Do you confirm generation [yes]? yes
```
- Use defaulf app bundle
- Set tools name: e.g rpc, portals api, provision...etc.
- Set feature: tools feature

### Step 2 : Start codeing

- [How to Create a Console Command](http://symfony.com/doc/current/cookbook/console/console_command.html)
- [Symfony Console Component](http://symfony.com/doc/current/components/console/introduction.html)
- [The Console Helpers - Progress Bar ](http://symfony.com/doc/current/components/console/helpers/progressbar.html)

### Step 3 : Add command to run file
````
use AppBundle\Command\NewCommand;
$application->add(new NewCommand());
````

Usage
-----

### rpc:clone_resource
```
php run rpc:clone_resource <CIK> <RID> [--scale=int] [--repeat=int] 
```
### rpc:create_script
```
php run rpc:create_script <CIK> [--scale=int] [--repeat=int]
```
### rpc:listing_resource
```
php run rpc:listing_resource <CIK> <RID> [--type="client","dataport","datarule","dispatch"]
```

## News ##

**31/07/2012 - php-autoloader v1.0.1 was released!**

This version:
  * adds compatibility with `PhpUnit 3.6`
  * adds a few new unit tests and improves some of existing ones

[Grab it from here.](http://php-autoloader.googlecode.com/files/php-autoloader.v1.0.1.zip)

**31/07/2012 - php-autoloader examples - version 0.4 is available**

New examples work with version 1.0.1 of php-autoloader thus fixing several potential issues as previous version (v0.3) had incorrect version of php-autoloader included.

[Get it from here](http://php-autoloader.googlecode.com/files/php-autoloader-examples.v0.4.zip).

**13/04/2010 - php-autoloader v1.0.0 was released!**

First stable version is here, featuring:
  * Stable version of php-autoloader library working out-of-the-box
  * `GenerateAutoLoaderIndexTask` - Phing task that can be easily incorporated into your build process
  * PHPUnit tests suite (90+ tests!) providing around 97% code coverage for source code


## Intro ##

Yet another approach to PHP auto-loading. It may even become your favourite one :)

_BTW Reading all that stuff below seems boring? You want some real action? [Check out examples page!](Examples.md)_

## Logo ##

Superb project's logo was created by Ben Dansie from [Montage Studio](http://montagestudio.org) who kindly gave me permission to use this image (thanks!). You can see original image [here](http://php-autoloader.googlecode.com/hg/img/Big_Yellow_Mech1.jpg) or [on Montage Studio site](http://montagestudio.org/wp-content/uploads/2009/06/BD_BigYellowMech.jpg).

## Rationale ##
Although there are a lot of PHP Autoloaders out there most of them fails to recognize at least one of two very important aspects of PHP's source code:

  1. PHP doesn't enforce any source code structure (e.g. like Java packages).
  1. Source code doesn't change after deployment to a server.

That's why I don't see much point in providing PHP Autoloader that is tuned to some specific code convention. Or Autoloader that manages dynamic index stored in a file. Or Autoloader that traverses all the source code directory trees at the beginning of your script.

Having said that, all mentioned examples inspired this Autoloader so they cannot be that bad ;)

Map (or index) file containing mapping between PHP class names and file names can be created just before deployment and then accessed as **static resource** with very little overhead for your project.

## Goals ##
First of all, this project is trying to provide easy to use PHP Autoloader that is able to scan directory trees with source code and store the result - mapping between class names and file names - into 'index file'.

Then it is a simple matter of loading this 'index file' at the beginning of your script and almost forget about PHP's 'require'/'include'!

The second goal is to provide Phing task that can be easily entangled into build process so Autoloader's index file can be created automatically before deployment.

## Requirements ##
  * PHP 5.2 is required to run.
  * Almost all source code files in this project have no external dependencies (except PHP's own SPL).
  * File(s) in _task_ directory are required to be run from [Phing](http://phing.info/trac) build files.
  * Obviously all unit tests requires [PHPUnit](http://www.phpunit.de)

## References ##
Real inspiration for writing this Autoloader:
  * [An Awesome Autoloader for PHP - very good article about different aspects of Autoloaders](http://gen5.info/q/2009/01/09/an-awesome-autoloader-for-php)

Examples of different approaches to PHP auto-loading:
  * [Very similar approach to auto-loading](http://anthonybush.com/projects/autoloader/)
  * [Autoloader by A.J. Brown which requires traversing source code directory tree every time script is run](http://ajbrown.org/blog/2008/12/02/an-auto-loader-using-php-tokenizer.html)
  * [Autoloader which manages index file dynamically](http://php-autoloader.malkusch.de/en/)
  * [Zend Framework Autoloader](http://framework.zend.com/manual/1.10/en/zend.loader.autoloader.html)
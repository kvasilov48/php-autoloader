## Intro ##

This page provides some useful examples how to use (and also abuse!) PHP-Autoloader.

You can download source code for all examples [from here](http://php-autoloader.googlecode.com/files/php-autoloader-examples.v0.4.zip).

## What you need ##

### Requirements ###

  * PHP 5.2 with SPL

### Code Structure ###

We always assume following code structure in examples:

```
  SampleProject // example main directory
  |
  |-lib
  | |
  | -autoloader // autoloader's directory
  |  |
  |  |-src
  |    |
  |    - ... // all autoloader's src content - nothing else required
  |
  |-src                   // This is directory with autoloader's index content
  | |
  | |-c1_b_interfaces.php // interfaces interfaceB and Interface_C1
  | |-class_a.php         // class ClassA implements interface_a
  | |-ClassB.php          // class class_b extends Different_Kind_Of_Naming_ClassD and implements interfaceB
  | |-ClassC_and_D.php    // class classC implements Interface_C1 and InterfaceC2, class Different_Kind_Of_Naming_ClassD doesn't implement anything
  | |-InterfaceA.php      // interface interface_a

  | |-InterfaceC2.php     // interface InterfaceC2 extends interface_a
  | |-NoClass.php         // some php code, no classes
  | 
  |-build.xml           // Phing build file for SampleProject (not obligatory!)
  |-example.php         // example code
  |-generate_index.php  // script generating index file
  |-index.idx           // This will be (usually!!!) our index file
```

Some important notes:
  * Weird names of files, classes and interfaces are here to prove that no code convention is being followed and everything is allowed (_but don't try it at home_)
  * `SampleProject/lib` directory contains latest source code of this Autoloader put into `autoloader` sub-directory
  * `SampleProject/src` directory contains classes that will be stored in Autoloader's index file - usually called `index.idx`
  * `SampleProject/build.xml` is Phing build file for this project - it is used in advanced examples
  * `SampleProject/example.php` is file with source code for current example
  * `SampleProject/generate_index.php` is file that is used to generate index file for current example
  * `SampleProject/index.idx` is index file we will use in most examples

## Example 1: Let's generate this index file ##

Ok, we're pretty much ready to start - so let's do it!

  1. First of all create **empty generate\_index.php file** in your `SampleProject` directory (see [Code Structure](Examples#Code_Structure.md) for more details).
  1. Then add following code to `generate_index.php` file:
```
<?php
require 'lib/autoloader/src/generate.php';
```
  1. Run the `generate_index.php` with PHP:
```
php generate_index.php
```

**Expected results:**
  * File with **.idx extension** and very weird name (at least for those not-md5-literate) is created (e.g. in my case it is `687a2aaed26dd0983e9b12b06075452c.idx` - but it depends on your path)

## Example 2 in which we test generated index file ##

Assuming you followed [Example 1](Examples#Example_1:_Let's_generate_this_index_file.md) and confirmed expected result (_and NOT deleted the index file_) then all you have to do is:
  1. Put following code into example.php:
```
<?php
require 'lib/autoloader/src/autoload.php';

$a = new ClassA();
$a->helloFromA();
```
  1. Run the example.php with PHP:
```
php example.php
```

**Expected results:**
  * No errors :)
  * Console output:
```
hello this is ClassA, please listen..
```

## How to change index file parameters (aka Example 3) ##

So you know how to generate default index file but you'd like to change few things... Well... it's open source project so come on - download source code and play with it!

Argh! But why? I want something that WILL work?!

Ok, Ok! You can use few built-in mechanisms that makes following things easy:
  * Change path and name of index file
  * Turn off index file compression (useful for debugging or in rare hardware configurations where processor is slower than reading files from hard drive)

Let's start with first one - changing path and name of index file.

### Re-generate index ###

  1. Remove old "default" index file (one with `.idx` extension).
  1. Create **empty generate\_index.php file** (_or remove contents of old one_) in your `SampleProject` directory (see [Code Structure](Examples#Code_Structure.md) for more details).
  1. Then put the following code into `generate_index.php` file:
```
<?php
define('PHP_AUTOLOADER_INDEX_PATH', './index.idx');
require 'lib/autoloader/src/generate.php';
```
  1. Run the `generate_index.php` with PHP:
```
php generate_index.php
```

... and you're done.

Yes you're right - all you had to do is to **define PHP\_AUTOLOADER\_INDEX\_PATH _before_ including (=running!) `generate.php`**.

### Test auto-loading ###

Now let's test it - run the code from `example.php` as defined in [Example no. 2](Examples#Example_2_in_which_we_test_generated_index_file.md).

Does it work? **No** _(unless you not removed old index file.. naughty!)_.

To make it work you need to define the path to index before running `autoload.php` script - yes, in the same way as in case of script generating index file.

So:
  1. Open `example.php` from [Example no. 2](Examples#Example_2_in_which_we_test_generated_index_file.md)
  1. Add line `define('PHP_AUTOLOADER_INDEX_PATH', './index.idx');` just before `require 'lib/autoloader/src/autoload.php';` to make it look like:
```
<?php
define('PHP_AUTOLOADER_INDEX_PATH', './index.idx');
require 'lib/autoloader/src/autoload.php';

$a = new ClassA();
$a->helloFromA();
```
  1. And run `example.php` with PHP

... and you're really done.

Or maybe you're still curious how to turn off compression of index file?

Then try next example!

**Expected results:**
  * `index.idx` file in your `SampleProject` directory
  * No errors while running both scripts
  * Console output on running `example.php`:
```
hello this is ClassA, please listen..
```

## Why do you need Example 4 to explain simple matter of turning off compression? ##

Boredom?

At least let's do this quickly then!

### Change generate index script ###
  1. Add line `define('PHP_AUTOLOADER_INDEX_STORAGE_NO_COMPRESSION', true);` to `generate_index.php` so it looks like:
```
<?php
define('PHP_AUTOLOADER_INDEX_STORAGE_NO_COMPRESSION', true);
define('PHP_AUTOLOADER_INDEX_PATH', './index.idx');
require 'lib/autoloader/src/generate.php';
```
  1. Run `generate_index.php` with PHP

### Change auto-loading example script ###
  1. Add line `define('PHP_AUTOLOADER_INDEX_STORAGE_NO_COMPRESSION', true);` to `example.php`:
```
<?php
define('PHP_AUTOLOADER_INDEX_STORAGE_NO_COMPRESSION', true);
define('PHP_AUTOLOADER_INDEX_PATH', './index.idx');
require 'lib/autoloader/src/autoload.php';

$a = new ClassA();
$a->helloFromA();
```
  1. Run `example.php` with PHP

As you can see you have to define `PHP_AUTOLOADER_INDEX_STORAGE_NO_COMPRESSION` variable in both scripts to turn off files compression.

**Expected results:**
  * `index.idx` file in your `SampleProject` directory that is human-readable
  * No errors while running both scripts
  * Our favourite console output on running `example.php`:
```
hello this is ClassA, please listen..
```
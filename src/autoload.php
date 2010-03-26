<?php
/**
 * Script responsible for simple loading of default autoloader.
 *
 * @author    M.Olszewski
 * @since     2010-03-26
 * @copyright Copyright (c) 2010 by M.Olszewski. All rights reserved.
 */


require_once dirname(__FILE__) . '/common.php';
require_once dirname(__FILE__) . '/autoload/AutoLoader.php';
require_once dirname(__FILE__) . '/autoload/CompressedFileIndexStorage.php';


// get default index path
$path = autoload_get_default_index_path();

$storage = new autoload_CompressedFileIndexStorage(new SplFileInfo($path));
$autoLoader = new autoload_AutoLoader();
$autoLoader->addIndexStorage($storage);
$autoLoader->register();

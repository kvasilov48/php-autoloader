<?php
/**
 * Common functions used by both autoload.php and generate.php scripts.
 *
 * @author    M.Olszewski
 * @since     2010-03-26
 * @copyright Copyright (c) 2010 by M.Olszewski. All rights reserved.
 */

/**
 * Gets default path to the index storage, based upon path to calling script.
 *
 * @return string Returns default path to the index storage.
 */
function autoload_get_default_index_path()
{
  $caller = filter_var($_SERVER['SCRIPT_FILENAME'], FILTER_SANITIZE_STRING);
  $dir    = dirname(realpath($caller));
  $name   = md5($dir);
  $ext    = '.idx';

  return $dir . '/' . $name . $ext;
}

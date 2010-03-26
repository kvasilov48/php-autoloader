<?php
/**
 * FileScanner
 *
 * @package   autoload
 * @author    M.Olszewski
 * @since     2010-03-25
 * @copyright Copyright (c) 2010 by M.Olszewski. All rights reserved.
 */


/**
 * File scanner interface responsible for scanning given path and returning mapping between found interfaces/classes
 * names and file names.
 *
 * Implementing classes should also allow adding user-specific file extensions and exclusions.
 *
 * This interface also defines some default extensions and exclusions that can be used by default in implementing
 * classes.
 *
 * @author  M.Olszewski
 * @package autoload
 */
interface autoload_FileScanner
{
  /**
   * Default extension - PHP files.
   *
   * @var string
   */
  const DEFAULT_EXTENSION_PHP = '.php';
  /**
   * Default extension - INC files.
   *
   * @var string
   */
  const DEFAULT_EXTENSION_INC = '.inc';
  /**
   * Default exclusion pattern - hidden files/directories.
   *
   * @var string
   */
  const DEFAULT_EXCLUSION_HIDDEN = '/\/\.\w+/';

  /**
   * Adds given extension (or extensions) to this scanner.
   *
   * All files found by scanner are checked against list of extensions. If file's extension is found then
   * file is processed.
   *
   * @param string|array $extensions Extension (or extensions) of files that should be scanned by
   * this scanner.
   */
  public function addExtension($extensions);

  /**
   * Adds given exclusion pattern (or patterns) to this scanner.
   *
   * All files found by scanner are checked against list of exclusion patterns. If file's name matches any
   * of these patterns then it is not processed.
   *
   * Patterns must be compatible with PCER format.
   *
   * @param string|array $exclusions Exclusion pattern (or patterns) of files that should not be scanned by
   * this scanner.
   */
  public function addExclusion($exclusions);

  /**
   * Scans given path in search of classes and interfaces.
   *
   * @param string  $path Path that will be scanned.
   * @param boolean $useRealPath Determines whether returned index should have relative (default) or absolute
   * filenames.
   *
   * @return array Returns index containing mapping between class names and file names.
   */
  public function scan($path, $useRealPath = false);
}

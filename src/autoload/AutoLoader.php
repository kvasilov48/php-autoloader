<?php
/**
 * AutoLoader
 *
 * @package   autoload
 * @author    M.Olszewski
 * @since     2010-03-24
 * @copyright Copyright (c) 2010 by M.Olszewski. All rights reserved.
 */



require_once dirname(__FILE__) . '/FileScanner.php';
require_once dirname(__FILE__) . '/IndexStorage.php';



/**
 * Class responsible for providing auto-loading capabilities.
 *
 * Provides:
 * <ul>
 * <li>Use of multiple index storages (see: {@link addIndexStorage()})</li>
 * <li>Simple way to create index storages (see: {@link scanAndStore()})</li>
 * </ul>
 *
 * @author  M.Olszewski
 * @package autoload
 */
class autoload_AutoLoader
{
  /**
   * @var array
   */
  private $storages = array();
  /**
   * @var array
   */
  private $index = array();


  /**
   * Constructs instance of {@link autoload_Autoloader}.
   */
  public function __construct()
  {
    // Intentionally left empty.
  }

  /**
   * Adds specified {@link autoload_IndexStorage} to this {@link autoload_AutoLoader} if it is not already added.
   *
   * It also uses specified index storage to load its index content so it can be used during class auto-loading.
   *
   * @param autoload_IndexStorage $storage Index storage to add.
   *
   * @return boolean Returns true if index storage was added, false otherwise.
   */
  public function addIndexStorage(autoload_IndexStorage $storage)
  {
    $added = false;
    if (false == in_array($storage, $this->storages))
    {
      $this->storages[] = $storage;
      $content = $storage->load();
      $this->index = array_merge($this->index, $content);

      $added = true;
    }

    return $added;
  }


  /**
   * Loads content from all defined index storages and registers this class on the SPL autoloader stack.
   *
   * @return bool Returns true if registration was successful, false otherwise.
   */
  public function register()
  {
    // as spl_autoload_register() disables __autoload() and this might be unwanted, we put it onto autoload stack
    if (function_exists('__autoload'))
    {
      spl_autoload_register('__autoload');
    }

    return spl_autoload_register(array($this, 'classAutoLoad'));
  }

  /**
   * Tries to autoload class with given name using all class indices associated with this autoloader.
   *
   * @param string $className Name of the class.
   *
   * @return boolean Returns true if class is loaded, false otherwise.
   */
  public function classAutoLoad($className)
  {
    assert('is_string($className)');

    if (class_exists($className, false) || interface_exists($className, false))
    {
      return false;
    }

    $path = $this->searchFor($className);

    if ($path !== null)
    {
      require_once $path;
    }

    return true;
  }

  private function searchFor($className)
  {
    return isSet($this->index[$className])? $this->index[$className] : null;
  }

  /**
   * Scans given paths using specified scanner and stores them using specified storage.
   *
   * This methods detects duplicated entries and throws SPL UnexpectedValueException.
   *
   * @param string|array $paths All paths to scan. This parameter may be a string with single path or paths separated
   * by path separator or it can an array with multiple strings (each string is treated as single path, no
   * path separator is allowed).
   * @param autoload_FileScanner $scanner Scanner used to scan the paths.
   * @param autoload_IndexStorage $storage Storage used to store found index content.
   *
   * @return array Index content that has been found and stored.
   */
  public function scanAndStore($paths, autoload_FileScanner $scanner, autoload_IndexStorage $storage)
  {
    if (is_string($paths))
    {
      $paths = explode(PATH_SEPARATOR, $paths);
    }
    if (is_array($paths) == false)
    {
      throw new InvalidArgumentException(__METHOD__ . '(): $paths is not an array! $paths=' . $paths);
    }
    foreach ($paths as $path)
    {
      if (is_string($path) == false)
      {
        throw new InvalidArgumentException(__METHOD__ . '(): $path is not a string! $path=' . $path);
      }
    }

    $allIndexes = array();
    foreach ($paths as $path)
    {
      $index = $scanner->scan($path);

      // detect duplicates - no class name can be present in existing indexes and index from scanned path
      $intersections = array_intersect_key($index, $allIndexes);
      if (false == empty($intersections))
      {
        throw new UnexpectedValueException(__METHOD__ . '(): ' . $path . ' contains class names that are already indexed! duplicates: ' . var_export($intersections, true));
      }

      // union
      $allIndexes = $allIndexes + $index;
    }

    $storage->store($allIndexes);

    return $allIndexes;
  }
}

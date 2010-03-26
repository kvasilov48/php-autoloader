<?php
/**
 * MemoryIndexStorage
 *
 * @package   autoload
 * @author    M.Olszewski
 * @since     2010-03-26
 * @copyright Copyright (c) 2010 by M.Olszewski. All rights reserved.
 */


require_once dirname(__FILE__) . '/IndexStorage.php';


/**
 * Simple implementation of {@link autoload_IndexStorage} that stores index content in memory.
 *
 * @author  M.Olszewski
 * @package autoload
 */
class autoload_MemoryIndexStorage implements autoload_IndexStorage
{
  /**
   * @var array
   */
  private $content;

  /**
   * @see autoload_IndexStorage::store()
   */
  public function store(array $content)
  {
    $this->content = $content;
  }

  /**
   * @see autoload_IndexStorage::load()
   */
  public function load()
  {
    return $this->content;
  }
}

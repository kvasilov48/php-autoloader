<?php
/**
 * CompressedFileIndexStorage
 *
 * @package   autoload
 * @author    M.Olszewski
 * @since     2010-03-26
 * @copyright Copyright (c) 2010 by M.Olszewski. All rights reserved.
 */


require_once dirname(__FILE__) . '/FileIndexStorage.php';


/**
 * Extension for {@link autoload_FileIndexStorage} which adds compression and decompression of the index content
 * on store and load.
 *
 * @author  M.Olszewski
 * @package autoload
 */
class autoload_CompressedFileIndexStorage extends autoload_FileIndexStorage
{
  /**
   * @var int
   */
  private $compression;

  /**
   * Constructs instance of {@link autoload_CompressedFileIndexStorage} that will load or store index content
   * from/in given filename with specified compression.
   *
   * @param SplFileInfo $fileName Name of the file where index content is stored.
   * @param int $compression Compression level.
   */
  public function __construct(SplFileInfo $fileName, $compression = 6)
  {
    parent::__construct($fileName);

    if (is_int($compression) == false)
    {
      throw new InvalidArgumentException(__METHOD__ . '(): $compression is not an integer! $compression=' . $compression);
    }

    $this->compression = $compression;
  }

  /**
   * Compresses given index content after it is serialized.
   *
   * @see autoload_FileIndexStorage::beforeStore()
   */
  protected function beforeStore(array $content)
  {
    $serialized = parent::beforeStore($content);
    $compressed = gzcompress($serialized, $this->compression);

    if ($compressed === false)
    {
      $error = error_get_last();
      throw new RuntimeException(__METHOD__ . '(): cannot compressed serialized content! Error message: '.
                                 $error['message']);
    }

    return $compressed;
  }

  /**
   * Decompresses given index content before it is unserialized.
   *
   * @see autoload_FileIndexStorage::beforeStore()
   */
  protected function afterLoad($content)
  {
    $uncompressed = gzuncompress($content);
    if ($uncompressed === false)
    {
      $error = error_get_last();
      throw new RuntimeException(__METHOD__ . '(): cannot uncompressed read content! Error message: '.
                                 $error['message']);
    }

    $unserialized = parent::afterLoad($uncompressed);

    return $unserialized;
  }
}

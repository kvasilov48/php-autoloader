<?php
/**
 * FileIndexStorage
 *
 * @package   autoload
 * @author    M.Olszewski
 * @since     2010-03-26
 * @copyright Copyright (c) 2010 by M.Olszewski. All rights reserved.
 */


require_once dirname(__FILE__) . '/IndexStorage.php';


/**
 * Base class for all index storages that are using files.
 *
 * This class default behaviour is to serialize array with index content before storing and
 * unserialize this array after loading.
 *
 * @author  M.Olszewski
 * @package autoload
 */
class autoload_FileIndexStorage implements autoload_IndexStorage
{
  /**
   * @var SplFileInfo
   */
  private $fileName;


  /**
   * Constructs instance of {@link autoload_FileIndexStorage} that will load or store index content
   * from/in given filename.
   *
   * @param SplFileInfo $fileName Name of the file where index content is stored/loaded.
   */
  public function __construct(SplFileInfo $fileName)
  {
    $this->fileName = $fileName;
  }

  /**
   * Serializes and stores given content in file specified during construction of this
   * {@link autoload_FileIndexStorage}.
   *
   * @see autoload_IndexStorage::store()
   */
  public function store(array $content)
  {
    if ($this->fileName->isDir())
    {
      throw new RuntimeException(__METHOD__ . '(): $fileName is not a file! $fileName=' . $fileName);
    }
    if ($this->fileName->isFile() && (false == $this->fileName->isWritable()))
    {
      throw new RuntimeException(__METHOD__ . '(): $fileName is not writable! $fileName=' . $fileName);
    }

    $readyContent = $this->beforeStore($content);

    $fileObject   = $this->fileName->openFile('w');
    $writtenBytes = $fileObject->fwrite($readyContent);

    if ($writtenBytes != strlen($readyContent))
    {
      throw new RuntimeException(__METHOD__ . '(): invalid number of bytes written! Expected: '.
                                 strlen($compressed) . ' Actual: ' . $writtenBytes);
    }
  }

  /**
   * This method is always called before index content is stored in the file in {@link store()} method.
   *
   * Main purpose of this method is to transform specified array representing index content into the string that
   * can be written into the file.
   *
   * Default implementation serializes specified array.
   *
   * @param array $content Array with index content.
   *
   * @return string Returns string representing file-writable index content.
   */
  protected function beforeStore(array $content)
  {
    return serialize($content);
  }

  /**
   * Loads content of the file specified during construction of this {@link autoload_FileIndexStorage},
   * unserializes it and returns it.
   *
   * @see autoload_IndexStorage::load()
   */
  public function load()
  {
    if ($this->fileName->isDir())
    {
      throw new RuntimeException(__METHOD__ . '(): $fileName is not a file! $fileName=' . $fileName);
    }
    if ($this->fileName->isFile() && (false == $this->fileName->isReadable()))
    {
      throw new RuntimeException(__METHOD__ . '(): $fileName is not readable! $fileName=' . $fileName);
    }

    $fileObject = $this->fileName->openFile('r');
    $content = null;
    while (!$fileObject->eof())
    {
      $content .= $fileObject->fgets();
    }

    $readyContent = $this->afterLoad($content);
    if (is_array($readyContent) == false)
    {
      throw new UnexpectedValueException(__METHOD__ . '(): $readyContent is not an array! $readyContent=' . $readyContent);
    }

    return $readyContent;
  }

  /**
   * This method is always called after index content is loaded from the file and before it is returned
   * from {@link load()} method.
   *
   * Main purpose of this method is to transform specified file content into the array.
   *
   * Default implementation assumes that file content is a serialized array so it performs unserialization.
   *
   * @param string $content Index content loaded from file as string.
   *
   * @return array Returns array with index content.
   */
  protected function afterLoad($content)
  {
    return unserialize($content);
  }
}

<?php
/**
 * FileScannerImpl
 *
 * @package   autoload
 * @author    M.Olszewski
 * @since     2010-03-25
 * @copyright Copyright (c) 2010 by M.Olszewski. All rights reserved.
 */


require_once dirname(__FILE__) . '/FileScanner.php';


/**
 * Implementation of {@link autoload_FileScanner} interface that uses Tokenizer (basic PHP extension) to
 * detect class and interface names.
 *
 * @author  M.Olszewski
 * @package autoload
 */
class autoload_TokenizerFileScanner implements autoload_FileScanner
{
  /**
   * @var array
   */
  private $extensions;
  /**
   * @var array
   */
  private $exclusions;


  /**
   * Constructs instance of {@link autoload_FileScannerImpl}.
   *
   * @param boolean $useDefault Determines whether default extensions and exclusions should be used.
   */
  public function __construct($useDefault = true)
  {
    if ($useDefault)
    {
      $this->extensions = array(self::DEFAULT_EXTENSION_PHP, self::DEFAULT_EXTENSION_INC);
      $this->exclusions = array(self::DEFAULT_EXCLUSION_HIDDEN);
    }
  }


  /**
   * @see autoload_FileScanner::addExtension()
   */
  public function addExtension($extensions)
  {
    if (true == is_string($extensions))
    {
      $extensions = array($extensions);
    }
    if (is_array($extensions) == false)
    {
      throw new InvalidArgumentException(__METHOD__ . '(): $extensions is not an array! $extensions=' . $extensions);
    }
    foreach ($extensions as $extension)
    {
      if (is_string($extension) == false)
      {
        throw new InvalidArgumentException(__METHOD__ . '(): $extension is not a string! $extension=' . $extension);
      }
    }

    $this->extensions = self::mergeUniquely($this->extensions, $extensions);
  }

  /**
   * @see autoload_FileScanner::addExclusion()
   */
  public function addExclusion($exclusions)
  {
    if (true == is_string($exclusions))
    {
      $exclusions = array($exclusions);
    }
    if (is_array($exclusions) == false)
    {
      throw new InvalidArgumentException(__METHOD__ . '(): $exclusions is not an array! $exclusions=' . $exclusions);
    }
    foreach ($exclusions as $exclusion)
    {
      if (is_string($exclusion) == false)
      {
        throw new InvalidArgumentException(__METHOD__ . '(): $exclusion is not a string! $exclusion=' . $exclusion);
      }
    }

    $this->exclusions = self::mergeUniquely($this->exclusions, $exclusions);
  }

  private static function mergeUniquely(array& $array1, array& $array2)
  {
    $diff = array_diff($array1, $array2);
    return array_merge($array1, $diff);
  }

  /**
   * @see autoload_FileScanner::scan()
   */
  public function scan($path, $useRealPath = false)
  {
    if (is_string($path) == false)
    {
      throw new InvalidArgumentException(__METHOD__ . '(): $path is not a string! $path=' . $path);
    }
    if (file_exists($path) == false)
    {
      throw new InvalidArgumentException(__METHOD__ . '(): $path is not referencing existing file or directory! $path=' . $path);
    }

    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path),
                                           RecursiveIteratorIterator::SELF_FIRST);

    $class2File = array();

    foreach ($files as $fileName => $fileInfo)
    {
      if ($useRealPath)
      {
        $fileName = $fileInfo->getRealPath();
      }
      if ($fileInfo->isFile() &&
          $fileInfo->isReadable() &&
          $this->checkIfIncluded($fileName) &&
          $this->checkIfNotExcluded($fileName))
      {
        $this->scanFile($fileName, $fileInfo, $class2File);
      }
    }

    return $class2File;
  }

  private function checkIfIncluded($fileName)
  {
    $included = false;
    foreach ($this->extensions as $extension)
    {
      if (substr($fileName, -strlen($extension)) === $extension)
      {
        $included = true;
        break;
      }
    }

    return $included;
  }

  private function checkIfNotExcluded($fileName)
  {
    $notExcluded = true;
    foreach ($this->exclusions as $exclusion)
    {
      if (0 != preg_match($exclusion, $fileName))
      {
        $notExcluded = false;
        break;
      }
    }

    return $notExcluded;
  }

  private function scanFile($fileName, SplFileInfo $fileInfo, array& $class2File)
  {
    $content = file_get_contents($fileName);
    if ($content !== false)
    {
      $tokens = token_get_all($content);
      for($i = 0, $size = count($tokens); $i < $size; $i++)
      {
        switch($tokens[$i][0])
        {
          case T_CLASS:
          case T_INTERFACE:
          {
            $i += 2; //skip the whitespace token
            $className = $tokens[$i][1];
            if (false == isSet($class2File[$className]))
            {
              $class2File[$className] = $fileName;
            }
            else
            {
              throw new UnexpectedValueException(__METHOD__ . '(): ' . $className . ' is already defined in file: '
                                                 . $class2File[$className] . ' Please rename its duplicate found in ' . $fileName);
            }
          }
          break;
        }
      }
    }
  }
}

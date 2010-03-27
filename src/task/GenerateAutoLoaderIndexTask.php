<?php
/**
 * GenerateAutoLoaderIndexTask
 *
 * @package   task
 * @author    M.Olszewski
 * @since     2010-03-26
 * @copyright Copyright (c) 2010 by M.Olszewski. All rights reserved.
 */


require_once 'phing/Task.php';


/**
 * Phing task responsible for generating index file for {@link AutoLoader}.
 *
 * @author  M.Olszewski
 * @package task
 */
class GenerateAutoLoaderIndexTask extends Task
{
  /**
   * File sets.
   *
   * @var array
   */
  private $filesets = array();
  /**
   * Path to index file.
   *
   * @var SplFileInfo
   */
  private $indexPath;
  /**
   * Defines level of compression. Zero means no compression.
   *
   * @var int
   */
  private $compression = 6;

  /**
   * Creates file set and adds it to this task.
   *
   * @return FileSet Returns created file set.
   */
  public function createFileSet()
  {
    $num = array_push($this->filesets, new FileSet());
    return $this->filesets[$num-1];
  }

  /**
   * Sets path to index file.
   *
   * @param string $indexPath Path to index file.
   */
  public function setIndexPath($indexPath)
  {
    $this->indexPath = new SplFileInfo((string) $indexPath);
  }

  /**
   * Sets compression level of the index file.
   *
   * Zero means no compression.
   *
   * By default compression is set to 6.
   *
   * @param int $compression Compression level of index file.
   */
  public function setCompression($compression)
  {
    $this->compression = (int) $compression;
  }

  /**
   * Initialisation method for this task, checks whether include path contains required files.
   */
  public function init()
  {
    require_once 'PEAR.php';

    // check whether all required files are on path
    $required = array('/src/autoload/AutoLoader.php' => false,
                      '/src/autoload/CompressedFileIndexStorage.php' => false,
                      '/src/autoload/FileIndexStorage.php' => false,
                      '/src/autoload/TokenizerFileScanner.php' => false);

    $paths = explode(PATH_SEPARATOR, get_include_path());

    foreach ($paths as $path)
    {
      foreach ($required as $file => $done)
      {
        if ((false == $done) && file_exists($path . $file))
        {
          require_once $path . $file;
          $required[$file] = true;
        }
      }
    }

    if (in_array(false, $required))
    {
      $missing = '';
      foreach ($required as $file => $done)
      {
        if (false == $done)
        {
          $missing .= $file . ', ';
        }
      }

      throw new BuildException('The GenerateAutoLoaderIndexTask requires following files: ' . $missing);
    }

    return true;
  }

  /**
   * Main method of this task scanning all files and directories specified in file sets and storing
   * the result in index file defined by index path.
   */
  public function main()
  {
    if ($this->indexPath == null)
    {
      throw new BuildException('The GenerateAutoLoaderIndexTask detected that index path is not defined!');
    }
    if ($this->indexPath->isDir())
    {
      throw new BuildException('The GenerateAutoLoaderIndexTask detected that index path is directory! index path: ' . $this->indexPath);
    }
    if ($this->indexPath->isFile() && (false == $this->indexPath->isWritable()))
    {
      throw new BuildException('The GenerateAutoLoaderIndexTask detected that index path refers to not-writable file! index path: ' . $this->indexPath);
    }

    $project = $this->getProject();

    // setup storage
    $storage = null;
    if ($this->compression > 0)
    {
      $storage = new autoload_CompressedFileIndexStorage($this->indexPath, $this->compression);
    }
    else
    {
      $storage = new autoload_FileIndexStorage($this->indexPath);
    }

    $scanner = new autoload_TokenizerFileScanner();
    $finalContent = array();
    $counter = 0;

    foreach ($this->filesets as $fs)
    {
      $fsDir    = $fs->getDir($project);
      $ds       = $fs->getDirectoryScanner($project);
      $srcFiles = $ds->getIncludedFiles();
      $oldDir   = getcwd();

      // Change directory to File Set's one
      chdir($fsDir);

      foreach ($srcFiles as $file)
      {
        $content = $scanner->scan($file, false);
        $intersections = array_intersect_key($content, $finalContent);
        if (false == empty($intersections))
        {
          chdir($currentDir);
          throw new BuildException('The GenerateAutoLoaderIndexTask detected that file set no. ' . $counter . ' contains class names that are already indexed! duplicates: ' . var_export($intersections, true));
        }

        $finalContent = $finalContent + $content;
      }
      $counter++;

      chdir($oldDir);
    }

    $storage->store($finalContent);
  }
}

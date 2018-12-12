<?php
defined("ABSPATH") or die("");
if (!defined('DUPLICATOR_PRO_VERSION')) exit; // Exit if accessed directly

require_once (DUPLICATOR_PRO_PLUGIN_PATH . 'classes/package/class.pack.archive.filters.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH . 'classes/package/class.pack.archive.zip.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH . 'classes/package/duparchive/class.pack.archive.duparchive.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH . 'classes/package/class.pack.archive.shellzip.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH . 'classes/class.exceptions.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH . 'classes/class.io.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH . 'lib/forceutf8/src/Encoding.php');
//require_once(DUPLICATOR_PRO_PLUGIN_PATH  . 'lib/snaplib/class.snaplib.u.util.php');

/**
 * Class for handling archive setup and build process
 *
 * Standard: PSR-2 (almost)
 * @link http://www.php-fig.org/psr/psr-2
 *
 * @package DUP_PRO
 * @subpackage classes/package
 * @copyright (c) 2017, Snapcreek LLC
 * @license	https://opensource.org/licenses/GPL-3.0 GNU Public License
 * @since 1.0.0
 *
 * @notes: Trace process time
 *	$timer01 = DUP_PRO_U::getMicrotime();
 *	DUP_PRO_LOG::trace("SCAN TIME-B = " . DUP_PRO_U::elapsedTime(DUP_PRO_U::getMicrotime(), $timer01));
 *
 */
class DUP_PRO_Archive
{
//del    const ScanStatusComplete = 'complete';
//    const ScanStatusRunning = 'running';
//    const ScanStatusFirst = 'first';
    //PUBLIC
    //Includes only the dirs set on the package
    public $ExportOnlyDB;
    public $FilterDirs;
    public $FilterExts;
    public $FilterFiles;
    //Includes all FilterInfo except warnings
    public $FilterDirsAll  = array();
    public $FilterExtsAll  = array();
    public $FilterFilesAll = array();
    public $FilterOn;
    public $File;
    public $Format;
    public $PackDir;
    public $Size  = 0;
    public $Dirs  = array();
    public $DirCount = 0;
    public $RecursiveLinks  = array();
    public $Files = array();
    public $FileCount = 0;
    public $file_count = -1;
    public $FilterInfo;
    public $ListDelimiter = ";\n";
//del    public $ScanTimeStart;
//    public $ScanStatus = self::ScanStatusFirst;
    //PROTECTED
    protected $Package;
    private $global;
    private $tmpFilterDirsAll = array();
    private $wpCorePaths = array();
    private $FileListHandle = null;
    private $DirListHandle = null;
//del    private $isForcedScanQuit = false;

    public function __construct($package)
    {
        $this->Package    = $package;
        $this->FilterOn   = false;
        $this->FilterInfo = new DUP_PRO_Archive_Filter_Info();
        $this->global     = DUP_PRO_Global_Entity::get_instance();
        $this->ExportOnlyDB = false;

        $rootPath = DUP_PRO_U::safePath(rtrim(DUPLICATOR_PRO_WPROOTPATH, '//'));

        $this->wpCorePaths[] = DUP_PRO_U::safePath("{$rootPath}/wp-admin");
        $this->wpCorePaths[] = DUP_PRO_U::safePath(WP_CONTENT_DIR . "/uploads");
        $this->wpCorePaths[] = DUP_PRO_U::safePath(WP_CONTENT_DIR . "/languages");
        $this->wpCorePaths[] = DUP_PRO_U::safePath(WP_PLUGIN_DIR);
        $this->wpCorePaths[] = DUP_PRO_U::safePath(get_theme_root());
        $this->wpCorePaths[] = DUP_PRO_U::safePath("{$rootPath}/wp-includes");
    }

    /**
     * Builds the archive file
     *
     * @returns null
     */
    public function buildFile($package, $build_progress)
    {
        DUP_PRO_LOG::trace("Building archive");

        try {
            $this->Package = $package;
            if (!isset($this->PackDir) && !is_dir($this->PackDir)) throw new Exception("The 'PackDir' property must be a valid directory.");
            if (!isset($this->File)) throw new Exception("A 'File' property must be set.");

            $completed = false;

            switch ($this->Format) {
                case 'TAR': break;

                case 'DAF':
                    $completed = DUP_PRO_Dup_Archive::create($this, $build_progress);
                    $this->Package->Update();
                    break;

                default:
                    $this->Format = 'ZIP';

                    if ($build_progress->current_build_mode == DUP_PRO_Archive_Build_Mode::Shell_Exec) {
                        DUP_PRO_LOG::trace('Doing shell exec zip');
                        $completed = DUP_PRO_ShellZip::create($this, $build_progress);
                    } else {
                        $zipArchive = new DUP_PRO_ZipArchive();
                        $completed  = $zipArchive->create($this, $build_progress);
                    }

                    $this->Package->Update();
                    break;
            }

            if ($completed) {
                if ($build_progress->failed) {
                    DUP_PRO_LOG::traceError("Error building archive");
                    $this->Package->set_status(DUP_PRO_PackageStatus::ERROR);
                } else {
                    $filepath    = DUP_PRO_U::safePath("{$this->Package->StorePath}/{$this->Package->Archive->File}");
                    $this->Size	 = @filesize($filepath);
                    $this->Package->set_status(DUP_PRO_PackageStatus::ARCDONE);
                    DUP_PRO_LOG::trace("filesize of archive = {$this->Size}");
                    DUP_PRO_LOG::trace("Done building archive");
                }
            } else {
                DUP_PRO_LOG::trace("Archive chunk completed");
            }

        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }

    /**
     * Create filters info and generate meta data about the dirs and files needed for the build
     *
     * @link http://msdn.microsoft.com/en-us/library/aa365247%28VS.85%29.aspx Windows filename restrictions
     *
     * @returns object	Returns a copy of this object containing scanner results
     */
    public function buildScanStats()
    {
    //del    $global = DUP_PRO_Global_Entity::get_instance();
        $this->createFilterInfo();
        
        $rootPath = DUP_PRO_U::safePath(rtrim(DUPLICATOR_PRO_WPROOTPATH, '//'));
        $rootPath = (trim($rootPath) == '') ? '/' : $rootPath;

//del        $scanPath = DUPLICATOR_PRO_SSDIR_PATH_TMP.'/'.$this->Package->ScanFile;
//        $this->Files = new DUP_PRO_ScanList($scanPath,'file',$global->package_scan_size,true);
//        $this->Dirs = new DUP_PRO_ScanList($scanPath,'dir',$global->package_scan_size,true);
        //If the root directory is a filter then skip it all
        if (in_array($this->PackDir, $this->FilterDirsAll) || $this->Package->Archive->ExportOnlyDB) {
             $this->initFileListHandles();             
            $this->closeFileListHandles();
            $this->Dirs = array();
//del            $this->ScanStatus = self::ScanStatusComplete;
        } else {
            //$this->Dirs[] = $this->PackDir;
            $this->initFileListHandles();
            $this->addToList($this->PackDir,'dir');
//del            DUP_PRO_Log::trace(print_r($this->Dirs,true));
//            die();
            $this->getFileLists($rootPath);
               $this->setFileDirCount(); 
//del            if($this->ScanStatus == self::ScanStatusComplete){
//                if(!$this->Dirs->has($this->PackDir)){
//                    $this->Dirs[] = $this->PackDir;
//                }
                $this->setDirFilters();
                $this->setFileFilters();
                $this->setTreeFilters();
                $this->closeFileListHandles();
           //del     $this->setFileDirCount();
       //del     }
        }

   //del     if($this->ScanStatus == self::ScanStatusComplete) {
            $this->FilterDirsAll = array_merge($this->FilterDirsAll, $this->FilterInfo->Dirs->Unreadable);
            $this->FilterFilesAll = array_merge($this->FilterFilesAll, $this->FilterInfo->Files->Unreadable);
            sort($this->FilterDirsAll);
            sort($this->FilterFilesAll);
            
            return $this;
        //del}
//del        //Added this to make sure __destruct() is being called
//        $this->Files = null;
//        $this->Dirs = null;
    }
    
    /**
     * Get the file path to the archive file
     *
     * @return string	Returns the full file path to the archive file
     */
    public function getSafeFilePath()
    {
        return DUP_PRO_U::safePath(DUPLICATOR_PRO_SSDIR_PATH."/{$this->File}");
    }

    /**
     * Get the store URL to the archive file
     *
     * @return string	Returns the full URL path to the archive file
     */
    public function getURL()
    {
        return DUPLICATOR_PRO_SSDIR_URL."/{$this->File}";
    }

    /**
     * Parse the list of ";" separated paths to make paths/format safe
     *
     * @param string $dirs A list of dirs to parse
     *
     * @return string	Returns a cleanup up ";" separated string of dir paths
     */
    public static function parseDirectoryFilter($dirs = "")
    {
        $dirs			= str_replace(array("\n", "\t", "\r"), '', $dirs);
        $filters		= "";
        $dir_array		= array_unique(explode(";", $dirs));
        $clean_array	= array();
        foreach ($dir_array as $val) {
            if (strlen($val) >= 2) {
                $clean_array[] = DUP_PRO_U::safePath(trim(rtrim($val, "/\\"))) ;
            }
        }

        if (count($clean_array)) {
            $clean_array  = array_unique($clean_array);
            sort($clean_array);
            $filters = implode(';', $clean_array) . ';';
        }
        return $filters;
    }

    /**
     * Parse the list of ";" separated extension names to make paths/format safe
     *
     * @param string $extensions A list of file extension names to parse
     *
     * @return string	Returns a cleanup up ";" separated string of extension names
     */
    public static function parseExtensionFilter($extensions = "")
    {
        $filter_exts = "";
        if (strlen($extensions) >= 1 && $extensions != ";") {
            $filter_exts = str_replace(array(' ', '.'), '', $extensions);
            $filter_exts = str_replace(",", ";", $filter_exts);
            $filter_exts = DUP_PRO_STR::appendOnce($extensions, ";");
        }
        return $filter_exts;
    }

    /**
     * Parse the list of ";" separated paths to make paths/format safe
     *
     * @param string $files		A list of file paths to parse
     *
     * @return string	Returns a cleanup up ";" separated string of file paths
     */
    public static function parseFileFilter($files = "")
    {
        $files			= str_replace(array("\n", "\t", "\r"), '', $files);
        $filters		= "";
        $file_array		= array_unique(explode(";", $files));
        $clean_array	= array();
        foreach ($file_array as $val) {
            if (strlen($val) >= 2) {
                $clean_array[] = DUP_PRO_U::safePath(trim(rtrim($val, "/\\"))) ;
            }
        }

        if (count($clean_array)) {
            $clean_array  = array_unique($clean_array);
            sort($clean_array);
            $filters = implode(';', $clean_array) . ';';
        }
        return $filters;
    }

    /**
     * Creates all of the filter information meta stores
     *
     * @todo: Create New Section Settings > Packages > Filters
     * Two new check boxes one for directories and one for files
     * Readonly list boxes for directories and files
     *
     * @return null
     */
    private function createFilterInfo()
    {        
        DUP_PRO_LOG::traceObject('Filter files', $this->FilterFiles);
        
        $this->FilterInfo->Dirs->Core = array();
        
        //FILTER: INSTANCE ITEMS
        if ($this->FilterOn) {
            $this->FilterInfo->Dirs->Instance  = array_map('DUP_PRO_U::safePath', explode(";", $this->FilterDirs, -1));
            $this->FilterInfo->Exts->Instance  = explode(";", $this->FilterExts, -1);
            $this->FilterInfo->Files->Instance = array_map('DUP_PRO_U::safePath', explode(";", $this->FilterFiles, -1));
        }

        //FILTER: GLOBAL ITMES
        if ($GLOBALS['DUPLICATOR_PRO_GLOBAL_DIR_FILTERS_ON']) {
            $this->FilterInfo->Dirs->Global = $GLOBALS['DUPLICATOR_PRO_GLOBAL_DIR_FILTERS'];
        }

        $GLOBALS['DUPLICATOR_PRO_GLOBAL_FILE_FILTERS'][] = DUPLICATOR_PRO_WPROOTPATH . '.htaccess';
		$GLOBALS['DUPLICATOR_PRO_GLOBAL_FILE_FILTERS'][] = DUPLICATOR_PRO_WPROOTPATH . 'wp-config.php';

        if ($GLOBALS['DUPLICATOR_PRO_GLOBAL_FILE_FILTERS_ON']) {
            $this->FilterInfo->Files->Global = $GLOBALS['DUPLICATOR_PRO_GLOBAL_FILE_FILTERS'];
        }

        //FILTER: CORE ITMES
        //Filters Duplicator free packages & All pro local directories
        $storages = DUP_PRO_Storage_Entity::get_all();
        foreach ($storages as $storage) {
            if ($storage->storage_type == DUP_PRO_Storage_Types::Local && $storage->local_filter_protection) {
                $this->FilterInfo->Dirs->Core[] = DUP_PRO_U::safePath($storage->local_storage_folder);
            }
        }

        $this->FilterDirsAll  = array_merge($this->FilterInfo->Dirs->Instance, $this->FilterInfo->Dirs->Global, $this->FilterInfo->Dirs->Core, $this->Package->Multisite->getDirsToFilter());
        $this->FilterExtsAll  = array_merge($this->FilterInfo->Exts->Instance, $this->FilterInfo->Exts->Global, $this->FilterInfo->Exts->Core);
        $this->FilterFilesAll = array_merge($this->FilterInfo->Files->Instance, $this->FilterInfo->Files->Global, $this->FilterInfo->Files->Core);
        $this->tmpFilterDirsAll = $this->FilterDirsAll;

        //PHP 5 on windows decode patch
        if (! DUP_PRO_U::$PHP7_plus && DUP_PRO_U::isWindows()) {
            foreach ($this->tmpFilterDirsAll as $key => $value) {
                if ( preg_match('/[^\x20-\x7f]/', $value)) {
                    $this->tmpFilterDirsAll[$key] = utf8_decode($value);
                }
            }
        }
    }

    /**
     * Get All Directories then filter
     *
     * @return null
     */
    private function setDirFilters()
    {
        $this->FilterInfo->Dirs->Warning    = array();
        $this->FilterInfo->Dirs->Unreadable = array();
        $this->FilterInfo->Dirs->AddonSites = array();
        $unset_key_list = array();

        //Filter directories invalid test checks for:
        // - characters over 250
        // - invlaid characters
        // - empty string
        // - directories ending with period (Windows incompatable)

        $this->Dirs = $this->getDirArray();
        
        foreach ($this->Dirs as $key => $dirPath) {
            $name = basename($dirPath);

            //Locate invalid directories and warn
            $invalid_encoding = preg_match('/(\/|\*|\?|\>|\<|\:|\\|\|)/', $name) ||
                preg_match('/[^\x20-\x7f]/', $name);

            $invalid_name = strlen($dirPath) > 250
                || trim($name) === ''
                || (strrpos($name, '.') == strlen($name) - 1 && substr($name, -1) === '.')
                || $invalid_encoding;

            if($invalid_encoding) {
                $dirPath = Encoding::toUTF8($dirPath);

                $unset_key_list[] = $key;
               // if(!$this->Dirs->has($dirPath))  $this->Dirs[] = $dirPath;
                 if(!in_array($dirPath, $this->Dirs))  $this->Dirs[] = $dirPath;
            }

            if($invalid_name) {
                if (($this->global->archive_build_mode === DUP_PRO_Archive_Build_Mode::ZipArchive)
                    || ($this->global->archive_build_mode === DUP_PRO_Archive_Build_Mode::DupArchive)) {


                    if ($invalid_name) {
                        $this->FilterInfo->Dirs->Warning[] = $dirPath;
                    }
                }
            }

            //Dir is not readble remove and flag
            if (! is_readable($this->Dirs[$key])) {
                $unset_key_list[] = $key;

                $this->FilterInfo->Dirs->Unreadable[] = $dirPath;
            }

//del            //Have to add filter dirs all check here, so it will work after rescan
//            if(in_array($dirPath,$this->FilterDirsAll)){
//                $unset_key_list[] = $key;
//            }

            //Check for other WordPress installs
            if ($name === 'wp-admin') {
                $parent_dir = realpath(dirname($this->Dirs[$key]));
                if ($parent_dir != realpath(DUPLICATOR_PRO_WPROOTPATH)) {
                    if (file_exists("$parent_dir/wp-includes")) {
                        if (file_exists("$parent_dir/wp-config.php")) {
                            // Ensure we aren't adding any critical directories
                            $parent_name = basename($parent_dir);
                            if (($parent_name != 'wp-includes') && ($parent_name != 'wp-content') && ($parent_name != 'wp-admin')) {
                                $this->FilterInfo->Dirs->AddonSites[] =  str_replace("\\", '/',$parent_dir);
                            }
                        }
                    }
                }
            }
        }

        DUP_PRO_LOG::traceObject('filter dirs array', $this->FilterDirsAll);
        DUP_PRO_LOG::traceObject('filter exts array', $this->FilterExtsAll);
        DUP_PRO_LOG::traceObject('filter files array', $this->FilterFilesAll);

        //Remove unreadable items outside of main loop for performance.
        //Important to reindex array since we are unsetting some - otherwise JSON turns into an object rather than array
        if (count($unset_key_list)) {
            foreach ($unset_key_list as $key) {
                if(array_key_exists($key, $this->Dirs)) {
          //del      if($this->Dirs->offsetExists($key)) {
                    unset($this->Dirs[$key]);
                }
            }
            $this->Dirs = array_values($this->Dirs);
        }

        $this->updateDirList($this->Dirs);
        $this->Dirs = array();
        
      //del  $this->Dirs->clearUnsetted();
    }

    /**
     * Get all files and filter out error prone subsets
     *
     * @return null
     */
    private function setFileFilters()
    {
        //Init for each call to prevent concatination from stored entity objects
        $this->Size                          = 0;
        $this->FilterInfo->Files->Size       = array();
        $this->FilterInfo->Files->Warning    = array();
        $this->FilterInfo->Files->Unreadable = array();
        $unset_key_list = array();

         $this->Files = $this->getFileArray();
              
        foreach ($this->Files as $key => $filePath) {

            $fileName = basename($filePath);

            if (! is_readable($filePath)) {
                $unset_key_list[] = $key;
                $this->FilterInfo->Files->Unreadable[] = $filePath;
                continue;
            }

            //File Warnings
            $invalid_encoding = preg_match('/(\/|\*|\?|\>|\<|\:|\\|\|)/', $fileName) ||
                preg_match('/[^\x20-\x7f]/', $fileName);

            $invalid_name = strlen($filePath) > 250
                || trim($fileName) === ''
                || $invalid_encoding;

            if($invalid_encoding) {
                $filePath = Encoding::toUTF8($filePath);
                $fileName = Encoding::toUTF8($fileName);

                // Need to encode the file path properly
                $unset_key_list[] = $key;
                $this->Files[] = $filePath;
            }

//del            //Have to add filter files all check here, so it will work after rescan
//            if(in_array($filePath,$this->FilterFilesAll)){
//                $unset_key_list[] = $key;
//            }

            if ($invalid_name) {
                if (($this->global->archive_build_mode === DUP_PRO_Archive_Build_Mode::ZipArchive)
                    || ($this->global->archive_build_mode === DUP_PRO_Archive_Build_Mode::DupArchive)) {
                    $this->FilterInfo->Files->Warning[] = array(
                        'name'	=> $fileName,
                        'dir'	=> pathinfo($filePath, PATHINFO_DIRNAME),
                        'path'	=> $filePath);
                }
            }

          //del  if(!in_array($key,$unset_key_list)){
                $fileSize = @filesize($filePath);
                $fileSize = empty($fileSize) ? 0 : $fileSize;
                $this->Size += $fileSize;

                if ($fileSize > DUPLICATOR_PRO_SCAN_WARNFILESIZE) {
                    $this->FilterInfo->Files->Size[] = array(
                        'ubytes' => $fileSize,
                        'bytes'  => DUP_PRO_U::byteSize($fileSize, 0),
                        'name'	 => $fileName,
                        'dir'	 => pathinfo($filePath, PATHINFO_DIRNAME),
                        'path'	 => $filePath);
                }
            }
     //del }


        //Remove unreadable items outside of main loop for performance
        if (count($unset_key_list)) {
            foreach ($unset_key_list as $key) {
                unset($this->Files[$key]);
            }
            
             $this->Files = array_values($this->Files);
        }

        $this->updateFileList($this->Files);

       $this->Files = array();
       //del $this->Files->clearUnsetted();
    }

    /**
     * Recursive function to get all directories in a wp install
     *
     * @notes:
     *	Older PHP logic which is more stable on older version of PHP
     *	NOTE RecursiveIteratorIterator is problematic on some systems issues include:
     *  - error 'too many files open' for recursion
     *  - $file->getExtension() is not reliable as it silently fails at least in php 5.2.17
     *  - issues with when a file has a permission such as 705 and trying to get info (had to fallback to path-info)
     *  - basic conclusion wait on the SPL libs until after php 5.4 is a requirments
     *  - tight recursive loop use caution for speed
     *
     * @return array	Returns an array of directories to include in the archive
     */
    private function getFileLists($path)
    {
//del        global $global;
//        if(empty($global) || !isset($global)){
//            $global = DUP_PRO_Global_Entity::get_instance();
//        }
//
//        global $start_time;
//        if(empty($start_time) || !isset($start_time)){
//            $start_time = DUP_PRO_U::getMicrotime();
//        }
//
//        $doChunking = $global->package_scan_mode == DUP_PRO_Scan_Mode::Multithreaded;
//        if($doChunking){
//            $elapsed_time = abs(DUP_PRO_U::getMicrotime() - $start_time);
//            $continue_scanning = $elapsed_time < $global->php_max_worker_time_in_sec;
//        }else{
//            $continue_scanning = true;
//        }

        $handle = @opendir($path);
        if ($handle) {
              while (($file = readdir($handle)) !== false) {
//del            while (($file = readdir($handle)) !== false && $continue_scanning) {
//
//                if($doChunking){
//                    $elapsed_time = abs(DUP_PRO_U::getMicrotime() - $start_time);
//                    if($elapsed_time > $global->php_max_worker_time_in_sec){
//                        $continue_scanning = false;
//                        $this->isForcedScanQuit = true;
//                        $this->ScanStatus = self::ScanStatusRunning;
//                    }
//                }

                if ($file == '.' || $file == '..') {
                    continue;
                }

                $fullPath = str_replace("\\", '/', "{$path}/{$file}");

                if (is_dir($fullPath)) {
//del                    if($doChunking && $this->Dirs->has($fullPath)){
////                        DUP_PRO_Log::trace("Have to Continue");
//                        continue;
//                    }

                    $add = true;
                    if(!is_link($fullPath)){
                        foreach ($this->tmpFilterDirsAll as $key => $val) {
                            $trimmedFilterDir = rtrim($val, '/');
                            if ($fullPath == $trimmedFilterDir || strpos($fullPath, $trimmedFilterDir . '/') !== false) {
                                $add = false;
                                unset($this->tmpFilterDirsAll[$key]);
                                break;
                            }
                        }
                    }else{
//del                        if(in_array($fullPath,$this->RecursiveLinks) || in_array($fullPath,$this->FilterDirsAll)){
//                            continue;
//                        }
                        //Convert relative path of link to absolute paths
                        chdir($fullPath);
                        $link_path = realpath(readlink($fullPath));
                        chdir(dirname(__FILE__));

                        $link_pos = strpos($fullPath,$link_path);
                        if($link_pos === 0 && (strlen($link_path) <  strlen($fullPath))){
                            $add = false;
                            $this->RecursiveLinks[] = $fullPath;
                            $this->FilterDirsAll[] = $fullPath;
                        }
                    }

                    if ($add) {
                        $this->getFileLists($fullPath);
  //$this->Dirs[] = $fullPath;

-                       $this->addToList($fullPath,'dir');
//del
//                        if(!$this->isForcedScanQuit){
//                            if(!$this->Dirs->has($fullPath))  $this->Dirs[] = $fullPath;
//                        }
                    }
                } else {
                    // Note: The last clause is present to perform just a filename check
                     if ( ! (in_array(pathinfo($file, PATHINFO_EXTENSION) , $this->FilterExtsAll)
                   //del if ( ! (($doChunking && $this->Files->has($fullPath))
                   //     || in_array(pathinfo($file, PATHINFO_EXTENSION) , $this->FilterExtsAll)
                        || in_array($fullPath, $this->FilterFilesAll)
                        || in_array($file, $this->FilterFilesAll))) {

                        //$this->Files[] = $fullPath;

-                       $this->addToList($fullPath,'file');
                        //$this->Files[] = $fullPath;
                    }
                }
            }
            closedir($handle);
        }
          
        return $this->Dirs;
//del        if(!$this->isForcedScanQuit){
//            $this->ScanStatus = self::ScanStatusComplete;
//        }
    }

    /**
     * Initializes the file list handles. Handles are set-up as properties for
     * performance improvement. Otherwise each handle would be opened and closed
     * with each path added.
     */
    private function initFileListHandles()
    {
        $file_list = DUPLICATOR_PRO_SSDIR_PATH_TMP."/{$this->Package->NameHash}_files.txt";
        $dir_list = DUPLICATOR_PRO_SSDIR_PATH_TMP."/{$this->Package->NameHash}_dirs.txt";

        //
        if($this->FileListHandle === null){
            $this->FileListHandle = fopen($file_list,"a+");
            ftruncate($this->FileListHandle,0);
        }

        if($this->DirListHandle === null){
            $this->DirListHandle = fopen($dir_list,"a+");
            ftruncate($this->DirListHandle,0);
        }
    }

    /**
     * Closes file and dir list handles
     */
    private function closeFileListHandles()
    {
        fclose($this->FileListHandle);
        $this->FileListHandle = null;
        fclose($this->DirListHandle);
        $this->DirListHandle = null;
    }

    /**
     * @param $path string Path to type
     * @param $type string Type of path, 'dir' or 'file'
     */
    private function addToList($path, $type)
    {

        if($type == 'file'){
            fwrite($this->FileListHandle,$path.$this->ListDelimiter);
        }elseif($type == 'dir'){
            fwrite($this->DirListHandle,$path.$this->ListDelimiter);
        }
    }

    /**
     * Sets the number of scanned dirs an files
     */
    private function setFileDirCount()
    {
        rewind($this->FileListHandle);
        rewind($this->DirListHandle);

        $fileCount = 0;

        while (!feof($this->FileListHandle)) {
            $fileCount += substr_count(fread($this->FileListHandle, 8192), "\n");
        }

        $dirCount = 0;

        while (!feof($this->DirListHandle)) {
            $dirCount += substr_count(fread($this->DirListHandle, 8192), "\n");
        }

        $this->FileCount = $fileCount;
        $this->DirCount  = $dirCount;
//del        $this->FileCount = count($this->Files);
//        $this->DirCount  = count($this->Dirs);
    }

    /**
     * @return array The scanned file paths as array
     */
    private function getFileArray()
    {
        rewind($this->FileListHandle);
        return array_filter(explode($this->ListDelimiter,stream_get_contents($this->FileListHandle)));
    }

    /**
     * Rewrites the contents of the file list file with the contents
     * of the $fileList array
     *
     * @param $fileList array The scanned file paths as array
     */
    private function updateFileList($fileList)
    {
        ftruncate($this->FileListHandle,0);
        fwrite($this->FileListHandle,implode($this->ListDelimiter,$fileList));
    }

    /**
     * @return array The scanned dir paths as array
     */
    private function getDirArray()
    {
        rewind($this->DirListHandle);
        return array_filter(explode($this->ListDelimiter,stream_get_contents($this->DirListHandle)));
    }

    /**
     * Rewrites the contents of the dir list file with the contents
     * of the $dirList array
     *
     * @param $dirList array The scanned file paths as array
     */
    private function updateDirList($dirList)
    {
        ftruncate($this->DirListHandle,0);
        fwrite($this->DirListHandle,implode($this->ListDelimiter,$dirList));
    }

    /**
     *  Builds a tree for both file size warnings and name check warnings
     *  The trees are used to apply filters from the scan screen
     *
     *  @return null
     */
    private function setTreeFilters()
    {
        //-------------------------
        //SIZE TREE
        //BUILD: File Size tree
        $dir_group = SnapLibUtil::arrayGroupBy($this->FilterInfo->Files->Size, "dir" );
        ksort($dir_group);
        foreach ($dir_group as $dir => $files) {
            $sum = 0;
            foreach ($files as $key => $value) {
                $sum += $value['ubytes'];
            }

            //Locate core paths, wp-admin, wp-includes, etc.
            $iscore = 0;
            foreach ($this->wpCorePaths as $core_dir) {
                if (strpos(DUP_PRO_U::safePath($dir), DUP_PRO_U::safePath($core_dir)) !== false) {
                    $iscore = 1;
                    break;
                }
            }

            $this->FilterInfo->TreeSize[] = array(
                'size' => DUP_PRO_U::byteSize($sum, 0),
                'dir' => $dir,
                'sdir' => str_replace(DUPLICATOR_PRO_WPROOTPATH, '', $dir),
                'iscore' => $iscore,
                'files' => $files
            );
        }

        //-------------------------
        //NAME TREE
        //BUILD: Warning tree for file names
        $dir_group = SnapLibUtil::arrayGroupBy($this->FilterInfo->Files->Warning, "dir" );
        ksort($dir_group);
        foreach ($dir_group as $dir => $files) {

            //Locate core paths, wp-admin, wp-includes, etc.
            $iscore = 0;
            foreach ($this->wpCorePaths as $core_dir) {
                if (strpos($dir, $core_dir) !== false) {
                    $iscore = 1;
                    break;
                }
            }

            $this->FilterInfo->TreeWarning[] = array(
                'dir' => $dir,
                'sdir' => str_replace(DUPLICATOR_PRO_WPROOTPATH, '', $dir),
                'iscore' => $iscore,
                'count' => count($files),
                'files' => $files);
        }

        //BUILD: Warning tree for dir names
        foreach ($this->FilterInfo->Dirs->Warning as $dir) {
            $add_dir = true;
            foreach ($this->FilterInfo->TreeWarning as $key => $value) {
                if ($value['dir'] == $dir) {
                    $add_dir = false;
                    break;
                }
            }
            if ($add_dir) {

                //Locate core paths, wp-admin, wp-includes, etc.
                $iscore = 0;
                foreach ($this->wpCorePaths as $core_dir) {
                    if (strpos(DUP_PRO_U::safePath($dir), DUP_PRO_U::safePath($core_dir)) !== false) {
                        $iscore = 1;
                        break;
                    }
                }

                $this->FilterInfo->TreeWarning[] = array(
                    'dir' => $dir,
                    'sdir' => str_replace(DUPLICATOR_PRO_WPROOTPATH, '', $dir),
                    'iscore' => $iscore,
                    'count' => 0);
            }
        }

        function _sortDir($a, $b){
            return strcmp($a["dir"], $b["dir"]);
        }
        usort($this->FilterInfo->TreeWarning, "_sortDir");
    }
}

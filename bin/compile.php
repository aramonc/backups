<?php
/**
 * Compiles the phar with the commands available commands
 */

$exclude = array('.git', 'test', 'tests');

/**
 * @param SplFileInfo $current
 * @param $key
 * @param RecursiveIterator $iterator
 * @return bool
 */
$nonPHPFilter = function(SplFileInfo $current, $key, RecursiveIterator $iterator) use($exclude) {
    if($iterator->hasChildren() && !in_array(strtolower($current->getFilename()), $exclude)){
        return true;
    }
    return $current->isFile() && $current->getExtension() == 'php';
};

$dirFlags = FilesystemIterator::KEY_AS_FILENAME | FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::SKIP_DOTS;

/**
 * @param string $path
 * @return RecursiveIteratorIterator
 */
$getIterator = function ($path) use($dirFlags, $nonPHPFilter) {
    $dirIterator = new RecursiveDirectoryIterator($path, $dirFlags);
    $filterIterator = new RecursiveCallbackFilterIterator($dirIterator, $nonPHPFilter);
    $recursiveIterator = new RecursiveIteratorIterator($filterIterator, RecursiveIteratorIterator::SELF_FIRST);

    return $recursiveIterator;
};

$baseDir = realpath(dirname(__DIR__));

$phar = new Phar($baseDir . '/build/backup.phar', 0, 'backup');

$phar->startBuffering();
$autoload = $baseDir . '/vendor/autoload.php';
$runner = $baseDir . '/bin/backup_runner.php';

$phar->addFile($autoload, '/vendor/autoload.php');
$phar->addFile($runner, '/bin/backups.php');

$phar->buildFromIterator($getIterator($baseDir . '/src'), $baseDir);
$phar->buildFromIterator($getIterator($baseDir . '/vendor/composer'), $baseDir);
$phar->buildFromIterator($getIterator($baseDir . '/vendor/league'), $baseDir);
$phar->buildFromIterator($getIterator($baseDir . '/vendor/phpseclib'), $baseDir);
$phar->buildFromIterator($getIterator($baseDir . '/vendor/symfony'), $baseDir);
$phar->buildFromIterator($getIterator($baseDir . '/vendor/mustangostang'), $baseDir);
$phar->buildFromIterator($getIterator($baseDir . '/vendor/ocramius'), $baseDir);
$phar->buildFromIterator($getIterator($baseDir . '/vendor/zendframework'), $baseDir);

$stub = file_get_contents($baseDir . '/bin/stub.php');
$phar->setStub($stub);

$phar->stopBuffering();


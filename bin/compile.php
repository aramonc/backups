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

$aggregator = new AppendIterator();
$aggregator->append($getIterator($baseDir . '/src'));
$aggregator->append($getIterator($baseDir . '/vendor/composer'));
$aggregator->append($getIterator($baseDir . '/vendor/league'));
$aggregator->append($getIterator($baseDir . '/vendor/phpseclib'));
$aggregator->append($getIterator($baseDir . '/vendor/pimple'));
$aggregator->append($getIterator($baseDir . '/vendor/symfony'));

$phar = new Phar($baseDir . '/build/backup.phar', 0, 'backup');

$phar->startBuffering();
$autoload = $baseDir . '/vendor/autoload.php';
$runner = $baseDir . '/bin/backup_runner.php';

$phar->addFile($autoload, '/vendor/autoload.php');
$phar->addFile($runner, '/bin/backups.php');

/** @var SPLFileInfo $file */
foreach($aggregator as $file) {
    $path = $file->getRealPath();
    $path = str_replace($baseDir . '/', '', $path);
    $path = strtr($path, '\\', '/');

    echo $path . "\n";
    if($file->isDir()) {
        $phar->addEmptyDir('/' . $path);
    }

    if($file->isFile() && !$file->isDir()) {
        $phar->addFile($file->getRealPath(), $path);
    }
}

$stub = file_get_contents($baseDir . '/bin/stub.php');
$phar->setStub($stub);

$phar->stopBuffering();

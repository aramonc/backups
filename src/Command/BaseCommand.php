<?php

namespace Ils\Command;

use Ils\Exception\ConfigFileNotFound;
use Ils\Exception\ParserNotDetected;
use League\Flysystem\Adapter\Sftp;
use League\Flysystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Zend\Config\Reader\ReaderInterface;
use Zend\ServiceManager\ServiceManager;

class BaseCommand extends Command
{
    protected $sm;

    protected function configure()
    {
        $this
            ->addOption('conf', 'c', InputOption::VALUE_REQUIRED, 'Configuration for the database connection')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'The file name of the backup')
            ->addOption('location', 'l', InputOption::VALUE_REQUIRED, 'The path to store the backups')
            ->addOption('send', 's', InputOption::VALUE_NONE, 'Send the backup to a remote server')
            ->addOption('send-config', null, InputOption::VALUE_OPTIONAL, 'Configuration of where to send the backup')
            ->addOption('compress', null, InputOption::VALUE_NONE, 'Compress the file(s) generated')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Test backup, but don\'t actually create it');
    }

    /**
     * @return ServiceManager
     */
    protected function getServiceManager()
    {
        return $this->sm;
    }

    /**
     * @param ServiceManager $sm
     */
    public function setServiceManager(ServiceManager $sm)
    {
        $this->sm = $sm;
    }

    /**
     * @param $path
     * @return array
     * @throws \Ils\Exception\ConfigFileNotFound
     * @throws \Ils\Exception\ParserNotDetected
     */
    protected function getConfig($path)
    {
        $file = new \SplFileInfo($path);
        if(!$file->isFile()) {
            throw new ConfigFileNotFound($path);
        }

        /** @var ReaderInterface $parser */
        $parser = null;
        switch($file->getExtension()) {
            case 'yml':
                $parser = $this->getServiceManager()->get('YamlParser');
                break;
            case 'ini':
                $parser = $this->getServiceManager()->get('IniParser');
                break;
            case 'xml':
                $parser = $this->getServiceManager()->get('YamlParser');
                break;
            case 'json':
                $parser = $this->getServiceManager()->get('YamlParser');
                break;
        }

        if(is_null($parser)) {
            throw new ParserNotDetected($path);
        }

        return $parser->fromFile($path);
    }

    /**
     * @param OutputInterface $output
     * @param $name
     * @param array $files
     * @param bool $compress
     * @return \DirectoryIterator|string
     */
    protected function packageFiles(OutputInterface $output, $name, array $files, $compress = false)
    {
        $command = $this->getApplication()->find('package');
        $arguments = array(
            '--name' => $name,
        );
        if($compress) {
            $arguments['--compress'] = $compress;
        }
        $arguments['files'] = $files;
        $input = new ArrayInput($arguments);
        return $command->run($input, $output);
    }

    protected function sendFiles(\SplFileInfo $path, $location, $config)
    {
        $adapter = new Sftp($config);
        $fs = new Filesystem($adapter);

        $stream = fopen($path->getRealPath(), 'r+');
        $fs->writeStream($location . DIRECTORY_SEPARATOR . $path->getFilename(), $stream);
        fclose($stream);

        unlink($path->getRealPath());
    }
} 
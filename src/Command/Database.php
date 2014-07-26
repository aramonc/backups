<?php
namespace Ils\Command;

use Ils\BackupInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Database extends BaseCommand {

    protected function configure()
    {
        $this->setName('databases')
            ->setDescription('Creates backups of the databases specified in the configuration file');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->getConfig($input->getOption('conf'));
        $files = array();
        foreach($config['database'] as $type => $dbConfig) {
            $svc = $this->getBUService($type);
            $svc->setConfig($dbConfig)
                ->setUseNice($config['use_nice'])
                ->setPath($config['tmp_storage'])
                ->setDryRun($input->getOption('dry-run'))
                ->run($output);
            $files = array_merge($files, $svc->getFiles());
        }

        $packageName = $config['tmp_storage'] . DIRECTORY_SEPARATOR . $input->getOption('name');
        $code = $this->packageFiles($output, $packageName, $files, !!$config['gzip']);

        $found = glob($packageName . '*');

        if(isset($config['remote']) && !empty($config['remote']) && count($found) > 0) {
            $code = $code | $this->sendFiles($output, new \SplFileInfo($found[0]), $input->getOption('location'), $input->getOption('conf'));
            if($code === 0) {
                $this->removeFiles($output, array($found[0]));
            }
        }

        $code = $code | $this->removeFiles($output, $files);

        return $code;
    }

    /**
     * @param $type
     * @return BackupInterface
     */
    protected function getBUService($type)
    {
        $service = null;
        switch($type) {
            case 'mysql':
                $service = $this->getServiceManager()->get('mysql');
                break;
        }

        return $service;
    }
} 
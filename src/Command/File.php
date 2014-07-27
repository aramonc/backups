<?php
namespace Ils\Command;

use Ils\BackupInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class File extends BaseCommand {

    protected function configure()
    {
        $this->setName('files')
            ->setDescription('Creates backups of the directories specified in the configuration file');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->getConfig($input->getOption('conf'));
        $code = 0;
        if(!is_array($config['files']) || empty($config['files'])) {
            return $code;
        }

        // Package the files
        $packageName = $config['tmp_storage'] . DIRECTORY_SEPARATOR . $input->getOption('name');
        $code = $code | $this->packageFiles($output, $packageName, $config['files'], !!$config['gzip']);
        $found = glob($packageName . '*');

        if(isset($config['remote']) && !empty($config['remote']) && count($found) > 0) {
            $code = $code | $this->sendFiles($output, new \SplFileInfo($found[0]), $input->getOption('location'), $input->getOption('conf'));
            if($code === 0) {
                $code = $code | $this->removeFiles($output, array($found[0]));
            }
        }

        return $code;
    }


} 
<?php
namespace Ils\Command;

use Ils\BackupInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class Package extends BaseCommand {

    protected $compress = false;
    protected $compressionLib = null;

    protected function configure()
    {
        $this->setName('package')
            ->addOption('compress', null, InputOption::VALUE_NONE, "Use compression")
            ->addArgument('files', InputArgument::IS_ARRAY, "List of files to package")
            ->setDescription('Package a list of files or directories using tar or zip. Preference for Tar with GZ');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $files = $input->getArgument('files');

        if(empty($files)) {
            return '';
        }

        if($input->getOption('compress')) {
            $this->setCompress(true);
        }

        if($this->canUse('tar')) {
            $this->package($files, $input->getOption('name'), 'tar');
        }

        return '';
    }

    /**
     * @return string
     */
    protected function getCompressionLib()
    {
        return 'gz';
    }

    /**
     * @param $compress
     * @return $this
     */
    protected function setCompress($compress)
    {
        if($compress) {
            $this->compressionLib = $this->getCompressionLib();
        }

        $this->compress = $compress && $this->compressionLib;

        return $this;
    }

    /**
     * @return bool
     */
    protected function canUse($command)
    {
        system('which ' . $command, $code);
        return $code === 0;
    }

    /**
     * @param array $files
     * @param string $name
     * @param string $format
     */
    protected function package($files, $name, $format)
    {
        $cmd = system('which ' . $format);
        $options = 'cpf';
        $name .= '.tar';

        if($this->compress) {
            switch ($this->compressionLib) {
                case 'gz':
                    $options = 'z' . $options;
                    $name .= '.gz';
                    break;
                case 'bz2':
                    $options = 'j' . $options;
                    $name .= '.bz2';
                    break;
            }
        }

        $cmd .= ' -' . $options . ' ' . $name . ' ' . implode(' ', $files);
        print_r($cmd);
        system($cmd);
    }
} 
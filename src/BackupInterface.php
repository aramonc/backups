<?php
namespace Ils;

use Symfony\Component\Console\Output\OutputInterface;

interface BackupInterface {


    /**
     * @param OutputInterface $output
     * @return mixed|void
     */
    public function run(OutputInterface $output);

    /**
     * @return array
     */
    public function getConfig();

    /**
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config);

    /**
     * @return string
     */
    public function getPath();

    /**
     * Path where to output the backup file(s)
     * @param string $path
     * @return $this
     */
    public function setPath($path);

    /**
     * @return boolean
     */
    public function isUseNice();

    /**
     * @param boolean $useNice
     * @return $this
     */
    public function setUseNice($useNice);

    /**
     * @return boolean
     */
    public function isDryRun();

    /**
     * @param boolean $dryRun
     * @return $this
     */
    public function setDryRun($dryRun);

} 
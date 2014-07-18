<?php
namespace Ils\Component;


use Ils\BackupInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MySQLDump implements BackupInterface
{

    /** @var  array */
    protected $config = array();
    /** @var  string */
    protected $path = '/tmp/';
    /** @var  bool */
    protected $singleFiles = true;
    /** @var array */
    protected $databases = array();
    /** @var bool */
    protected $all = false;
    /** @var bool */
    protected $useNice = false;
    /** @var bool */
    protected $dryRun = false;


    /**
     * @param OutputInterface $output
     * @return void
     */
    public function run(OutputInterface $output)
    {
        if ($this->all && $this->singleFiles) {
            $this->databases = $this->getDatabasesList();
            $this->all = false;
        }

        if ($this->singleFiles) {
            $cmd = array();
            foreach ($this->databases as $db) {
                $cmd[] = $this->getCommand($db);
            }
            $cmd = implode(" && ", $cmd);
        } else {
            $cmd = $this->getCommand($this->databases);
        }

        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $output->writeln("Now executing:");
            $output->writeln("\"" . $cmd . "\"");
        }

        if($this->isDryRun()) {
            return;
        }

        exec($cmd, $result, $exit);
        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $output->writeln(sprintf("Result (%s):", $exit));
            foreach($result as $line) {
                $output->writeln($line);
            }
        }
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
        $this->all = !isset($this->config['databases']) ||
            !is_array($this->config['databases']) ||
            count($this->config['databases']) == 0 ||
            strtolower($this->config['databases'][0]) == 'all';
        $this->singleFiles = (bool)$this->config['files'];

        if (!$this->all) {
            $this->databases = $this->config['databases'];
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setPath($path)
    {
        $path = new \SplFileInfo($path);
        if($path->isDir()){
            $this->path = $path->getRealPath() . DIRECTORY_SEPARATOR;
        }

        return $this;
    }

    /**
     * @return boolean
     */
    public function isUseNice()
    {
        return $this->useNice;
    }

    /**
     * @param boolean $useNice
     * @return $this
     */
    public function setUseNice($useNice)
    {
        $this->useNice = (bool)$useNice;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isDryRun()
    {
        return $this->dryRun;
    }

    /**
     * @param boolean $dryRun
     * @return $this
     */
    public function setDryRun($dryRun)
    {
        $this->dryRun = $dryRun;
        return $this;
    }



    /**
     * @param string|array $dbName
     * @return string
     */
    protected function getCommand($dbName)
    {
        $cmd = "";
        if ($this->isUseNice()) {
            $cmd .= "/usr/bin/nice -n 19 ";
        }
        $cmd .= "mysqldump ";
        $cmd .= "-u %s ";
        $cmd .= "-p'%s' ";
        $cmd .= "-h %s ";
        $cmd .= "-P %s ";
        $cmd .= "--skip-opt ";
        $cmd .= "--add-drop-database ";
        $cmd .= "--create-options ";
        $cmd .= "--quick ";
        $cmd .= "--compact ";
        $cmd .= "--extended-insert ";
        $cmd .= "--routines ";
        $cmd .= "--result-file=%s ";

        $username = $this->getConfig()['username'];
        $password = $this->getConfig()['password'];
        $host = $this->getConfig()['host'];
        $port = isset($this->getConfig()['port']) && !empty($this->getConfig()['port']) ? $this->getConfig(
        )['port'] : '3306';
        $filePath = $this->getPath();

        if ($this->all) {
            $filePath .= $host;
            $cmd .= "--all-databases ";
        } else {
            if(is_array($dbName)) {
                $filePath .= "databases.sql";
                $dbName = implode(" ", $dbName);
            } else {
                $filePath .= $dbName . ".sql";
            }

            $cmd .= "--databases " . $dbName;
        }

        return trim(sprintf($cmd, $username, $password, $host, $port, $filePath));
    }

    /**
     * @return array
     */
    protected function getDatabasesList()
    {
        exec(
            sprintf("mysqlshow -u %s -p'%s'", $this->getConfig()['username'], $this->getConfig()['password']),
            $list
        );
        $dbs = array();
        foreach($list as $i => $db) {
            $db = str_replace('|', '', $db);
            $db = str_replace('Databases', '', $db);
            $db = trim(preg_replace('/\+-+\+/', '', $db));
            if(!empty($db)){
                $dbs[] = $db;
            }
        }

        return $dbs;
    }
}
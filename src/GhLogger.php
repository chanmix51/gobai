<?php

class GhLoggerException extends RuntimeException
{
}

class GhLogger
{
    const ERROR_LEVEL = 255;
    const DEBUG = 1;
    const NOTICE = 2;
    const WARNING = 4;
    const ERROR = 8;

    protected $enabled = false;
    protected $filename;
    protected $file;

    public function getFileName()
    {
        if ($this->filename == null)
        {
            $this->filename = dirname(__FILE__).'/GhLogger.log';
        }

        return $this->filename;
    }

    public function enableIf($condition = true)
    {
        $this->enabled = (bool) $condition;
    }

    public function disable()
    {
        $this->enabled = false;
    }

    public function writeIfEnabled($message, $level = self::DEBUG)
    {
        if ($this->enabled)
        {
            $this->writeLog($message, $level);
        }
    }

    public function writeIfEnabledAnd($condition, $message, $level = self::DEBUG)
    {
        if ($this->enabled)
        {
            $this->writeIf($condition, $message, $level);
        }
    }

    public function writeLog($message, $level = self::DEBUG)
    {
        $this->writeLine($message, $level);
    }

    public function writeIf($condition, $message, $level = self::DEBUG)
    {
        if ($condition)
        {
            $this->writeLog($message, $level);
        }
    }

    public function __construct($filename = null)
    {
        $this->filename = $filename;

        if (!$this->file = fopen($this->getFileName(), 'a+'))
        {
            throw new GhLoggerException(sprintf("Could not open file '%s' for writing.", $this->getFileName()));
        }

        $this->writeLine("\n===================== STARTING =====================", 0);
    }

    public function __destruct()
    {
        $this->writeLine("\n===================== ENDING =====================", 0);
        fclose($this->file);
    }

    protected function writeLine($message, $level)
    {
        if ($level & self::ERROR_LEVEL)
        {
            $date = new DateTime();
            $en_tete = $date->format('d/m/Y H:i:s');
            switch($level)
            {
            case self::NOTICE:
                $en_tete = sprintf("%s (notice)", $en_tete);
                break;
            case self::WARNING:
                $en_tete = sprintf("%s WARNING", $en_tete);
                break;
            case self::ERROR:
                $en_tete = sprintf("\n%s **ERROR**", $en_tete);
                break;
            }

            $message = sprintf("%s -- %s\n",  $en_tete, $message);
            fwrite($this->file, $message);
        }
    }
}


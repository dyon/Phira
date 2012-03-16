<?php

// define namespace
namespace phira;

// phiraManager class
class phiraLogger
{
  // levels of error reporting
	const
    ERROR = 100,
    WARN = 200,
    INFO = 300,
    DEBUG = 400;

  // logger instance, and default log_location location
	protected static
    $_instance,
    $log_location = 'php://stdout';
    
  // stream variable and deefault log verbosity
	protected
    $stream,
    $verbosity = self::INFO;

  // return instance of phiraLogger
	public static function getInstance($log_location = NULL, $verbosity = NULL)
  {
    if (!(self::$_instance instanceof self))
    {
      if ($log_location === NULL)
      {
        $log_location = self::$log_location;
      }
      else
      {
        self::$log_location = $log_location;
      }
        
      self::$_instance = new self($log_location);
    }
    
    if ($verbosity !== NULL)
      self::$_instance->setVerbosity($verbosity);
      
    return self::$_instance;
  }

  // class constructor
	protected function __construct($log_location)
  {
		$mode = ($log_location == 'php://stdout' ? 'w' : 'a');
		$this->stream = fopen($log_location, $mode);
    
    if (!$this->stream)
      throw new \Exception('Can not write to log_location ('.$log_location.').');
	}

  // set log location
	public static function setLogLocation($log_location)
  {
		self::$log_location = $log_location;
	}
  
  // set level of logging to record
	public function setVerbosity($verbosity)
  {
		$this->verbosity = $verbosity;
	}
  
  // write log to log_location
	protected function _write($verbosity, &$log_msg)
  {
		if ($verbosity > $this->verbosity)
      return;

		if (fwrite($this->stream, date('Y-m-d H:i:s').' - '.self::_verbosityToString($verbosity).' : '.$log_msg."\n") === FALSE)
      throw new \Exception('Can not write to log_location ('.self::$log_location.').');
		
    fflush($this->stream);
	}

  // return string name of log level
	protected static function _verbosityToString($verbosity)
  {
		switch ($verbosity)
    {
			case self::ERROR: return 'ERROR';
			case self::WARN: return 'WARN';
			case self::INFO: return 'INFO';
			case self::DEBUG: return 'DEBUG';
		}
	}
  
  // level of log entry
	public function error($log_msg) { $this->_write(self::ERROR, $log_msg); }
	public function warn($log_msg) { $this->_write(self::WARN, $log_msg); }
	public function info($log_msg) { $this->_write(self::INFO, $log_msg); }
	public function debug($log_msg)	{ $this->_write(self::DEBUG, $log_msg); }
  
}
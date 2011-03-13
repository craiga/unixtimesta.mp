<?php

class Logger
{
	const INFO = 0;
	const DEBUG = 1;
	const WARNING = 2;
	const ERROR = 3;
	const SERIOUS = 4;
	const CRITICAL = 5;
	
	private static $_Log = array();
	
	public static function Log($message, $level = Logger::DEBUG)
	{
		self::$_Log[] = array(
			"level" => $level,
			"message" => $message,
			"time" => time()
		);
	}
	
	public static function GetLog($level = Logger::WARNING)
	{
		$log = array();
		foreach(self::$_Log as $logEntry)
		{
			if($logEntry["level"] >= $level)
			{
				$log[] = $logEntry;
			}
		}
		return $log;
	}
}
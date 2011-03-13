<?php

class LogInjectionResponseFilter extends FooterInjectionResponseFilter
{
	private $_logLevel;
	private $_template;
	
	public function __construct(Template $template)
	{
		$this->_template = $template;
	}
	
	protected function _shouldFilter($body, $requestIdentifier, $parameters)
	{
		$shouldFilter = parent::_shouldFilter($body, $requestIdentifier, $parameters);
		if($shouldFilter)
		{
			if(preg_match("/\?(?:.*&)?log=(\d+)/", $requestIdentifier, $matches) == 1)
			{
				$this->_logLevel = $matches[1];
				$shouldFilter = true;
			}
			else
			{
				$shouldFilter = false;
			}
		}
		return $shouldFilter;
	}
	
	
	protected function _getInjection()
	{
		$log = Logger::GetLog($this->_logLevel);
		foreach($log as $logEntry)
		{
			$this->_template->gotoNext("log");
			$this->_template->setVar("log.level", $logEntry["level"]);
			$this->_template->setVar("log.message", htmlspecialchars($logEntry["message"]));
			$this->_template->setVar("log.time", $logEntry["time"]);
		}
		
		return $this->_template->toString();
	}
}
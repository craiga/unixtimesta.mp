<?php

class TestResponse extends Response
{
	private $_testName;
	
	public function __construct($testName)
	{
		$this->_testName = $testName;
		
		parent::__construct();
	}
	
	protected function _getUnfilteredResponseBody()
	{
		$test = eval(sprintf('return new %s();', $this->_testName));
		ob_start();
		$test->run(new DefaultReporter());
		$testResults = ob_get_clean();
		return $testResults;
	}
	
	protected function _getHTTPStatus()
	{
		return 200;
	}
	
	protected function _getContentType()
	{
		return "text/html";
	}
	
	protected function _getTextEncoding()
	{
		return "utf-8";
	}
	
	protected function _getHeaders()
	{
		return array();
	}
}
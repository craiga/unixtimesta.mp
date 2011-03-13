<?php

class TestRequestHandler extends RequestHandler
{
	private $_template;
	
	public function __construct(Template $template)
	{
		$this->_template = $template;
	}
	
	protected function _handleRequest($requestIdentifier, $parameters, $requestMethod)
	{
		$response = false;
		if($this->_requestIdentifierMatches("/^\/tests(\?.*)?$/i", $requestIdentifier))
		{
			global $classDirectories;
			foreach($classDirectories as $classDirectory)
			{
				$iterator = new DirectoryIterator($classDirectory);
				$tests = $this->_findTests($iterator);
				foreach($tests as $test)
				{
					$this->_template->gotoNext("test");
					$this->_template->setVar("test.name", $test);
				}
			}
			
			$response = new TemplateResponse($this->_template);
		}
		else if($this->_requestIdentifierMatches("/^\/test\/(\w*)(?:\?.*)?$/i", $requestIdentifier, $matches))
		{
			// preg_match("/^\/test\/(\w*)$/i", $requestIdentifier, $matches);
			$response = new TestResponse($matches[1]);
		}
		return $response;
	}
	
	private function _findTests(DirectoryIterator $iterator)
	{
		$tests = array();
		while($iterator->valid())
		{
			if($iterator->isReadable())
			{
				if($iterator->isFile() && preg_match("/(\w*Test)\.php$/", $iterator->getFilename(), $matches) == 1)
				{
					$tests[] = $matches[1];
				}
			}
			
			$iterator->next();
		}
		return $tests;
	}
}
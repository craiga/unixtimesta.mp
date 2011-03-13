<?php

class TemplateRequestHandler extends RequestHandler
{
	private $_pattern;
	private $_template;
	
	/**
	 * @throws InvalidArgumentException
	 */
	public function __construct($pattern, Template $template)
	{
		if(!is_string($pattern))
		{
			throw new InvalidArgumentException();
		}
		if(!($template instanceof Template))
		{
			throw new InvalidArgumentException();
		}
		
		$this->_pattern = $pattern;
		$this->_template = $template;
	}
	
	protected function _handleRequest($requestIdentifier, $parameters, $requestMethod)
	{
		$response = false;
		if($this->_requestIdentifierMatches($this->_pattern, $requestIdentifier))
		{
			$response = new TemplateResponse($this->_template);
		}
		return $response;
	}
}

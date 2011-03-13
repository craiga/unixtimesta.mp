<?php

class FilesystemRequestHandler extends RequestHandler
{
	private $_pattern;
	private $_fileName;
	
	/**
	 * @throws InvalidArgumentException
	 */
	public function __construct($pattern, $fileName)
	{
		if(!is_string($pattern))
		{
			throw new InvalidArgumentException();
		}
		if(!is_string($fileName))
		{
			throw new InvalidArgumentException();
		}
		
		$this->_pattern = $pattern;
		$this->_fileName = $fileName;
	}
	
	protected function _handleRequest($requestIdentifier, $parameters, $requestMethod)
	{
		$response = false;
		if($this->_requestIdentifierMatches($this->_pattern, $requestIdentifier, $matches))
		{
			// If the address matches, replace all $n placeholders with the
			// appropriate submatches.
			$fileName = $this->_fileName;
			for($index = count($matches) - 1; $index > -1; $index--)
			{
				$fileName = str_replace(sprintf("$%d", $index), $matches[$index], $fileName);
			}
			$response = new FilesystemResponse($fileName);
		}
		return $response;
	}
}
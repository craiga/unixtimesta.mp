<?php

abstract class Response
{
	private $_filter;
	
	public function __construct(ResponseFilter $filter = null)
	{
		if(!is_null($filter))
		{
			$this->setFilter($filter);
		}
	}
	
	/**
	 * Send the response.
	 * @throws RuntimeException, CannotSendHeadersException, InvalidBodyExceptionException, InvalidHTTPStatusException, InvalidContentTypeException, InvalidTextEncodingException, InvalidHeaderListException
	 */
	public function send($requestIdentifier = null, $parameters = null)
	{
		// if request identifier is not supplied, get it from $_SERVER
		if(is_null($requestIdentifier))
		{
			if(!isset($_SERVER) || !isset($_SERVER["REQUEST_URI"]))
			{
				throw new RuntimeException("Request Identifier wasn't supplied to constructor, and \$_SERVER[\"REQUEST_URI\"] is not available.");
			}
			$requestIdentifier = $_SERVER["REQUEST_URI"];
		}
		
		// if parameters are not supplied, use $_POST
		if(is_null($parameters))
		{
			if(!isset($_POST))
			{
				throw new RuntimeException("Parameters weren't supplied to constructor, and \$_POST is not available.");
			}
			$parameters = $_POST;
		}
		
		if(headers_sent())
		{
			throw new CannotSendHeadersException();
		}
		$body = $this->getResponseBody($requestIdentifier, $parameters);
		foreach($this->getResponseHeaders($requestIdentifier, $parameters) as $header)
		{
			header($header);
		}
		echo($body);
	}
	
	/**
	 * Set the active filter.
	 * @throws InvalidResponseFilterException
	 */
	public function setFilter(ResponseFilter $filter)
	{
		// Not every version of PHP actually respects type hinting, and since
		// I've written the test case...
		if(!($filter instanceof ResponseFilter))
		{
			throw new InvalidResponseFilterException();
		}
		
		$this->_filter = $filter;
	}
	
	/**
	 * @throws InvalidBodyException
	 */
	public function getResponseBody($requestIdentifier, $parameters)
	{
		$responseBody = $this->_getUnfilteredResponseBody();
		if(!is_string($responseBody))
		{
			throw new InvalidBodyException();
		}
		if(isset($this->_filter))
		{
			$responseBody = $this->_filter->filterResponseBody($responseBody, $requestIdentifier, $parameters);
		}
		return $responseBody;
	}
	
	/**
	 * @throws InvalidHTTPStatusException, InvalidContentTypeException, InvalidTextEncodingException, InvalidHeaderListException
	 */
	public function getResponseHeaders($requestIdentifier, $parameters)
	{
		$headers = $this->_getUnfilteredResponseHeaders();
		if(isset($this->_filter))
		{
			$headers = $this->_filter->filterResponseHeaders($headers, $requestIdentifier, $parameters);
		}
		return $headers;
	}
	
	/**
	 * @throws InvalidHTTPStatusException, InvalidContentTypeException, InvalidTextEncodingException, InvalidHeaderListException
	 */
	protected function _getUnfilteredResponseHeaders()
	{
		$headers = array();
		$headers[] = $this->_getStatusHeader();
		$headers[] = $this->_getContentTypeHeader();
		
		$otherHeaders = $this->_getHeaders();
		if(!is_array($otherHeaders))
		{
			throw new InvalidHeaderListException($otherHeaders);
		}
		
		foreach($otherHeaders as $header)
		{
			$headers[] = $header;
		}
		
		return $headers;
	}
	
	/**
	 * @throws InvalidHTTPStatusException
	 */
	private function _getStatusHeader()
	{
		$statusCode = $this->_getHTTPStatus();
		if(!is_int($statusCode) || $statusCode < 1)
		{
			throw new InvalidHTTPStatusException();
		}
		$s = "HTTP/1.1 " . (string)$statusCode;
		switch($statusCode)
		{
			case 404:
				$s = "HTTP/1.1 404 Not Found";
				break;
		}
		return $s;
	}
	
	/**
	 * @throws InvalidContentTypeException, InvalidTextEncodingException
	 */
	private function _getContentTypeHeader()
	{
		$contentType = $this->_getContentType();
		if(preg_match("/^\w+\/.+$/", $contentType) != 1)
		{
			throw new InvalidContentTypeException($contentType);
		}
		$textEncoding = $this->_getTextEncoding();
		if(!is_string($textEncoding))
		{
			throw new InvalidTextEncodingException($textEncoding);
		}
		
		return sprintf("Content-type: %s; charset=%s", $contentType, $textEncoding);
	}
	
	
	abstract protected function _getUnfilteredResponseBody();
	abstract protected function _getHTTPStatus();
	abstract protected function _getContentType();
	abstract protected function _getTextEncoding();
	abstract protected function _getHeaders();
}
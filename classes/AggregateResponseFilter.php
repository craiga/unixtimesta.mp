<?php

class AggregateResponseFilter extends ResponseFilter
{
	private $_filters;
	
	/**
	 * @throws InvalidArgumentException
	 */
	public function __construct(array $filters = array())
	{
		if(!is_array($filters))
		{
			throw new InvalidArgumentException("List of filters should be supplied as an array");
		}
		$this->_filters = $filters;
	}
	
	public function filterResponseBody($body, $requestIdentifier, $parameters)
	{
		foreach($this->_filters as $filter)
		{
			$body = $filter->filterResponseBody($body, $requestIdentifier, $parameters);
		}
		return $body;
	}
	
	public function filterResponseHeaders($headers, $requestIdentifier, $parameters)
	{
		foreach($this->_filters as $filter)
		{
			$headers = $filter->filterResponseHeaders($headers, $requestIdentifier, $parameters);
		}
		return $headers;
	}
}

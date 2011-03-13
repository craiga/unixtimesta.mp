<?php

abstract class ResponseFilter
{
	/**
	 * Filter the response as a string.
	 * @return The filtered response string.
	 */
	public function filterResponseBody($original, $requestIdentifier, $parameters)
	{
		return $original;
	}
	
	/**
	 * Filter the headers sent.
	 * @return The filtered headers.
	 */
	public function filterResponseHeaders($headers, $requestIdentifier, $parameters)
	{
		return $headers;
	}
}
<?php

class InvalidContentTypeException extends InvalidArgumentException
{
	public function __construct($invalidContentType)
	{
		parent::__construct(sprintf("The %s \"%s\" is not a valid content type.", gettype($invalidContentType), $invalidContentType));
	}
}
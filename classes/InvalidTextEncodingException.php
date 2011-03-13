<?php

class InvalidTextEncodingException extends InvalidArgumentException
{
	public function __construct($invalidTextEncoding)
	{
		parent::__construct(sprintf("The %s \"%s\" is not a valid text encoding.", gettype($invalidTextEncoding), $invalidTextEncoding));
	}
	
}
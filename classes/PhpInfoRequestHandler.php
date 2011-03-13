<?php

class PhpInfoRequestHandler extends RequestHandler
{
	protected function _handleRequest($requestIdentifier, $parameters, $requestMethod)
	{
		$response = false;
		if($this->_requestIdentifierMatches('/^\/phpinfo$/i', $requestIdentifier))
		{
			ob_start();
			phpinfo();
			$phpinfo = ob_get_clean();
			$response = new StaticResponse($phpinfo, 200, "text/html");
		}
		return $response;
	}
}
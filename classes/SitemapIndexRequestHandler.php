<?php

class SitemapIndexRequestHandler extends RequestHandler
{
	private $_template;
	
	public function __construct(Template $template)
	{
		$this->_template = $template;
	}

	protected function _handleRequest($requestIdentifier, $parameters, $requestMethod)
	{
		$response = false;
		if($this->_requestIdentifierMatches("/^\/sitemapindex(\?.+)?$/", $requestIdentifier))
		{
			// get offset
			$baseOffset = 0;
			if(isset($_GET['offset']))
			{
				$baseOffset = (int)$_GET['offset'];
			}
			
			// set offsets in template
			$count = 0;
			for($offset = $baseOffset; $offset <= 3600 * 24; $offset = $offset + 10)
			{
				$this->_template->gotoNext("url");
				$this->_template->setVar("url.loc", sprintf("http://unixtimesta.mp/sitemap?offset=%d", $offset));
				// // max number of entries is 50,000
				// $count++;
				// if($count >= 50000)
				// {
				// 	break;
				// }
			}
			
			// create response
			$response = new TemplateResponse($this->_template);
		}
		
		return $response;
	}
}

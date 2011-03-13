<?php

class SitemapRequestHandler extends RequestHandler
{
	private $_template;
	
	public function __construct(Template $template)
	{
		$this->_template = $template;
	}

	protected function _handleRequest($requestIdentifier, $parameters, $requestMethod)
	{
		$response = false;
		if($this->_requestIdentifierMatches("/^\/sitemap(\?.+)?$/", $requestIdentifier))
		{
			// get offset
			$offset = 0;
			if(isset($_GET['offset']))
			{
				$offset = (int)$_GET['offset'];
			}
			
			// build sitemap string
			$sitemap = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
			$count = 0;
			$max = PHP_INT_MAX;
			// $max = 99999;
			for($time = $offset; $time <= $max; $time = $time + 3600 * 24)
			{
				$sitemap .= sprintf("<url><loc>http://unixtimesta.mp/%d</loc></url>\n", $time);
				// max number of entries is 50,000
				$count++;
				if($count >= 50000)
				{
					break;
				}
			}
			$sitemap .= "</urlset>";
			
			// create response
			$response = new StringResponse($sitemap);
		}
		
		return $response;
	}
}

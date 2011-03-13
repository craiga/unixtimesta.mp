<?php

abstract class HeaderInjectionResponseFilter extends PatternReplacementResponseFilter
{
	protected function _getPattern($body, $requestIdentifier, $parameters)
	{
		return "/<body([^>]*)>/";
	}
	
	protected function _getReplacement($body, $requestIdentifier, $parameters)
	{
		return sprintf("\n<body$1>\n%s\n", $this->_getInjection());
	}
	
	/**
	 * Do not filter any content which doesn't end in closing body and html
	 * tags or includes the tag <meta name="sol-no-injection" value="header" />
	 */
	protected function _shouldFilter($body, $requestIdentifier, $parameters)
	{
		$shouldFilter = false;
		
		// check for something that looks like HTML
		if(preg_match("/<\/body>\s*<\/html>\s*$/", $body) == 1)
		{
			// check for meta tag
			if(preg_match("/<meta\s+name=([\"'])sol-no-injection\\1\s+value=([\"'])header\\1/", $body) == 0)
			{
				$shouldFilter = true;
			}
		}
		
		return $shouldFilter;
	}
	
	abstract protected function _getInjection();
}
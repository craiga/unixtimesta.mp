<?php

abstract class PatternReplacementResponseFilter extends ResponseFilter
{
	/**
	 * @throw PatternReplacementFailureException
	 */
	public function filterResponseBody($body, $requestIdentifier, $parameters)
	{
		$replacement = $body;
		if($this->_shouldFilter($body, $requestIdentifier, $parameters))
		{
			$pattern = $this->_getPattern($body, $requestIdentifier, $parameters);
			$patternReplacement = $this->_getReplacement($body, $requestIdentifier, $parameters);
			
			$replacement = preg_replace($pattern, $patternReplacement,
					$body, $this->_getLimit($body, $requestIdentifier, $parameters));
			
			if(is_null($replacement))
			{
				throw new PatternReplacementFailureException(preg_last_error());
			}
		}
		return $replacement;
	}
	
	abstract protected function _getPattern($body, $requestIdentifier, $parameters);
	abstract protected function _getReplacement($body, $requestIdentifier, $parameters);
	
	protected function _shouldFilter($body, $requestIdentifier, $parameters)
	{
		return true;
	}
	
	protected function _getLimit($body, $requestIdentifier, $parameters)
	{
		return 1;
	}
}
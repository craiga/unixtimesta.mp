<?php

class TimestampRequestHandler extends RequestHandler
{
	private $_template;
	private $_time;
	
	public function __construct(Template $template, $time = null)
	{
		if(is_null($time))
		{
			$time = time();
		}
		if(!is_int($time))
		{
			throw new InvalidArgumentException();
		}
		$this->_template = $template;
		$this->_time = $time;
	}
	
	protected function _handleRequest($requestIdentifier, $parameters, $requestMethod)
	{
		$response = false;
		
		
		// /yyyy/mm/dd format
		if($this->_requestIdentifierMatches("/^\/(\d{4})\/(\d{1,2})\/(\d{1,2})(?:\/(\d{1,2})(?:\/(\d{1,2})(?:\/(\d{1,2}))?)?)?(?:\?.*)?$/m", $requestIdentifier, $matches))
		{
			$year = (int)$matches[1];
			$month = (int)$matches[2];
			$day = (int)$matches[3];
			$hour = 0;
			$minute = 0;
			$second = 0;
			if(isset($matches[4]))
			{
				$hour = (int)$matches[4];
				if(isset($matches[5]))
				{
					$minute = (int)$matches[5];
					if(isset($matches[6]))
					{
						$second = (int)$matches[6];
					}
				}
			}
			if($year > 1969 && $hour < 25 && $minute < 61 && $second < 61 && checkdate($month, $day, $year))
			{
				$time = gmmktime($hour, $minute, $second, $month, $day, $year);
				$response = new TemporaryRedirectResponse(sprintf("/%d", $time));
			}
		}
		// timestamp format
		else if($this->_requestIdentifierMatches("/^\/(\d{1,17})(?:\?.*)?$/m", $requestIdentifier, $matches))
		{
			// set time in template
			$time = (int)$matches[1];
			$this->_template->setVar("timestamp", $time);
			// set warnings in template
			$warnings = $this->_generateWarnings($time);
			foreach($warnings as $warning)
			{
				$this->_template->gotoNext("warning");
				$this->_template->setVar("warning.message", $warning);
			}
			
			// // set relative times
			// $relativeHours = $this->_generateRelativeHours($time);
			// foreach($relativeHours as $relativeHour)
			// {
			// 	$this->_template->gotoNext("relativeHour");
			// 	$this->_template->setVar("relativeHour.time", $relativeHour['time']);
			// 	$this->_template->setVar("relativeHour.description", $relativeHour['description']);
			// }
			// $relativeDays = $this->_generateRelativeDays($time);
			// foreach($relativeDays as $relativeDay)
			// {
			// 	$this->_template->gotoNext("relativeDay");
			// 	$this->_template->setVar("relativeDay.time", $relativeDay['time']);
			// 	$this->_template->setVar("relativeDay.description", $relativeDay['description']);
			// }
			// $relativeMonths = $this->_generateRelativeMonths($time);
			// foreach($relativeMonths as $relativeMonth)
			// {
			// 	$this->_template->gotoNext("relativeMonth");
			// 	$this->_template->setVar("relativeMonth.time", $relativeMonth['time']);
			// 	$this->_template->setVar("relativeMonth.description", $relativeMonth['description']);
			// }
			
			// create response
			$response = new TemplateResponse($this->_template);
		}
		// check for something parseable by strtotime
		else if($this->_requestIdentifierMatches("/^\/([\w\d\+%]+)(?:\?.*)?$/m", $requestIdentifier, $matches))
		{
			
			$str = urldecode($matches[1]);
			$time = strtotime($str, $this->_time);
			if($time !== false && $time > -1)
			{
				$response = new TemporaryRedirectResponse(sprintf("/%d", $time));
			}
		}
		// home page
		else if($this->_requestIdentifierMatches("/^\/(?:\?.*)?$/m", $requestIdentifier, $matches))
		{
			// check for posted data
			if(isset($parameters["time"]))
			{
				$post = $parameters["time"];
				// check for posted timestamp
				if(preg_match("/^\d+$/", $post))
				{
					$time = (int)$post;
					$response = new TemporaryRedirectResponse(sprintf("/%d", $time));
				}
				// check for something parseable by strtotime
				else
				{
					$time = strtotime($post, $this->_time);
					if($time)
					{
						$response = new TemporaryRedirectResponse(sprintf("/%d", $time));
					}
				}
			}
			else
			{
				$response = new TemporaryRedirectResponse(sprintf("/%d", $this->_time));
			}
		}
		
		return $response;
	}
	
	const MAX_UNSIGNED_32BIT_INTEGER = 4294967295;
	const MAX_SIGNED_32BIT_INTEGER = 2147483647;
	
	private function _generateWarnings($time)
	{
		$warnings = array();
		if($time > self::MAX_UNSIGNED_32BIT_INTEGER)
		{
			$warnings[] = sprintf("%d is greater than the maximum value of a 32 bit unsigned integer (%d). This timestamp can't be used in many systems.", $time, self::MAX_SIGNED_32BIT_INTEGER);
		}
		elseif($time == self::MAX_UNSIGNED_32BIT_INTEGER)
		{
			$warnings[] = sprintf("%d is the maximum value of a 32 bit unsigned integer. This timestamp is the largest that can be used in many systems.", $time);
		}
		elseif($time > self::MAX_SIGNED_32BIT_INTEGER)
		{
			$warnings[] = sprintf("%d is greater than the maximum value of a 32 bit signed integer (%d). This timestamp can't be used in some systems.", $time, self::MAX_SIGNED_32BIT_INTEGER);
		}
		elseif($time == self::MAX_SIGNED_32BIT_INTEGER)
		{
			$warnings[] = sprintf("%d is the maximum value of a 32 bit signed integer. This timestamp is the largest that can be used in some systems.", $time);
		}
		return $warnings;
	}
	
	// private function _generateRelativeHours($time)
	// {
	// 	return $this->_generateRelativeTimes(array(
	// 		array(
	// 			'time' => '-4 hours',
	// 			'description' => '&#8592; 4 hours',
	// 		),
	// 		array(
	// 			'time' => '-3 hours',
	// 			'description' => '&#8592; 3 hours',
	// 		),
	// 		array(
	// 			'time' => '-2 hours',
	// 			'description' => '&#8592; 2 hours',
	// 		),
	// 		array(
	// 			'time' => '-1 hour',
	// 			'description' => '&#8592; 1 hour',
	// 		),
	// 		array(
	// 			'time' => date('G:00', $time),
	// 			'description' => 'this hour',
	// 		),
	// 		array(
	// 			'time' => '+1 hour',
	// 			'description' => '1 hour &#8594;',
	// 		),
	// 		array(
	// 			'time' => '+2 hours',
	// 			'description' => '2 hours &#8594;',
	// 		),
	// 		array(
	// 			'time' => '+3 hours',
	// 			'description' => '3 hours &#8594;',
	// 		),
	// 		array(
	// 			'time' => '+4 hours',
	// 			'description' => '4 hours &#8594;',
	// 		),
	// 	), $time);
	// }
	// 
	// private function _generateRelativeDays($time)
	// {
	// 	return $this->_generateRelativeTimes(array(
	// 		array(
	// 			'time' => '-4 days',
	// 			'description' => '&#8592; 4 days',
	// 		),
	// 		array(
	// 			'time' => '-3 days',
	// 			'description' => '&#8592; 3 days',
	// 		),
	// 		array(
	// 			'time' => '-2 days',
	// 			'description' => '&#8592; 2 days',
	// 		),
	// 		array(
	// 			'time' => '-1 day',
	// 			'description' => '&#8592; 1 day',
	// 		),
	// 		array(
	// 			'time' => 'midnight',
	// 			'description' => 'this day',
	// 		),
	// 		array(
	// 			'time' => '+1 day',
	// 			'description' => '1 day &#8594;',
	// 		),
	// 		array(
	// 			'time' => '+2 days',
	// 			'description' => '2 days &#8594;',
	// 		),
	// 		array(
	// 			'time' => '+3 days',
	// 			'description' => '3 days &#8594;',
	// 		),
	// 		array(
	// 			'time' => '+4 days',
	// 			'description' => '4 days &#8594;',
	// 		),
	// 	), $time);
	// }
	// 
	// private function _generateRelativeMonths($time)
	// {
	// 	return $this->_generateRelativeTimes(array(
	// 		array(
	// 			'time' => '-4 months',
	// 			'description' => '&#8592; 4 months',
	// 		),
	// 		array(
	// 			'time' => '-3 months',
	// 			'description' => '&#8592; 3 months',
	// 		),
	// 		array(
	// 			'time' => '-2 months',
	// 			'description' => '&#8592; 2 months',
	// 		),
	// 		array(
	// 			'time' => '-1 month',
	// 			'description' => '&#8592; 1 month',
	// 		),
	// 		// array(
	// 		// 	'time' => '1st day of this month',
	// 		// 	'description' => 'this month',
	// 		// ),
	// 		array(
	// 			'time' => '+1 month',
	// 			'description' => '1 month &#8594;',
	// 		),
	// 		array(
	// 			'time' => '+2 months',
	// 			'description' => '2 months &#8594;',
	// 		),
	// 		array(
	// 			'time' => '+3 months',
	// 			'description' => '3 months &#8594;',
	// 		),
	// 		array(
	// 			'time' => '+4 months',
	// 			'description' => '4 months &#8594;',
	// 		),
	// 	), $time);
	// }
	// 
	// private function _generateRelativeTimes($relativeTimeDefinitions, $time)
	// {
	// 	$relativeTimes = array();
	// 	foreach($relativeTimeDefinitions as $relativeTimeDefinition)
	// 	{
	// 		$relativeTime = strtotime($relativeTimeDefinition['time'], $time);
	// 		if($relativeTime !== false && $relativeTime >= 0)
	// 		{
	// 			$relativeTimes[] = array(
	// 				'time' => $relativeTime,
	// 				'description' => $relativeTimeDefinition['description'],
	// 			);
	// 		}
	// 	}
	// 	return $relativeTimes;
	// }
}

<?php

Mock::generate("Template", "MockTemplate");

class TimestampRequestHandlerTest extends UnitTestCase
{
	public function testTimestamp()
	{
		$template = new MockTemplate();
		$template->expectOnce("setVar", array("timestamp", 123456789));
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/123456789");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual($template, $response->getTemplate());
	}
	
	public function testOneTimestamp()
	{
		$template = new MockTemplate();
		$template->expectOnce("setVar", array("timestamp", 1));
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/1");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual($template, $response->getTemplate());
	}
	
	public function testZeroTimestamp()
	{
		$template = new MockTemplate();
		$template->expectOnce("setVar", array("timestamp", 0));
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/0");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual($template, $response->getTemplate());
	}
	
	public function testNegativeTimestamp()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/-1");
		
		$this->assertEqual(false, $response);
	}
	
	public function testYearSingleDigitMonthSingleDigitDay()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/1/1");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/1230768000", $response->getLocation());
	}
	
	public function testYearSingleDigitMonthTwoDigitDay()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/1/22");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/1232582400", $response->getLocation());
	}
	
	public function testYearSingleDigitMonthLeadingZeroDay()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/1/01");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/1230768000", $response->getLocation());
	}
	
	public function testYearTwoDigitMonthSingleDigitDay()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/12/1");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/1259625600", $response->getLocation());
	}
	
	public function testYearTwoDigitMonthTwoDigitDay()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/12/25");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/1261699200", $response->getLocation());
	}
	
	public function testYearTwoDigitMonthLeadingZeroDay()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/12/01");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/1259625600", $response->getLocation());
	}
	
	public function testYearLeadingZeroMonthSingleDigitDay()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/01/1");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/1230768000", $response->getLocation());
	}
	
	public function testYearLeadingZeroMonthTwoDigitDay()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/01/22");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/1232582400", $response->getLocation());
	}
	
	public function testYearLeadingZeroMonthLeadingZeroDay()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/01/01");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/1230768000", $response->getLocation());
	}
	
	public function testYearMonthZeroDay()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/1/0");
		
		$this->assertEqual(false, $response);
	}
	
	public function testYearJanMaxDay()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/1/31");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/1233360000", $response->getLocation());
	}
	
	public function testYearJanMaxDayPlusOne()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/1/32");
		
		$this->assertEqual(false, $response);
	}
	
	public function testYearFebMaxDayRegularYear()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/2/28");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/1235779200", $response->getLocation());
	}
	
	public function testYearFebMaxDayPlusOneRegularYear()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/2/29");
		
		$this->assertEqual(false, $response);
	}
	
	public function testYearFebMaxDayLeapYear()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2008/2/29");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/1204243200", $response->getLocation());
	}
	
	public function testYearFebMaxDayPlusOneLeapYear()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2008/2/30");
		
		$this->assertEqual(false, $response);
	}
	
	public function testYearMarMaxDay()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/3/31");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/1238457600", $response->getLocation());
	}
	
	public function testYearMarMaxDayPlusOne()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/3/32");
		
		$this->assertEqual(false, $response);
	}
	
	public function testYearAprMaxDay()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/4/30");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/1241049600", $response->getLocation());
	}
	
	public function testYearAprMaxDayPlusOne()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/4/31");
		
		$this->assertEqual(false, $response);
	}
	
	public function testYearMayMaxDay()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/5/31");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/1243728000", $response->getLocation());
	}
	
	public function testYearMayMaxDayPlusOne()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/5/32");
		
		$this->assertEqual(false, $response);
	}
	
	public function testYearJunMaxDay()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/6/30");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/1246320000", $response->getLocation());
	}
	
	public function testYearJunMaxDayPlusOne()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/6/31");
		
		$this->assertEqual(false, $response);
	}
	
	public function testYearJulMaxDay()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/7/31");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/1248998400", $response->getLocation());
	}
	
	public function testYearJulMaxDayPlusOne()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/7/32");
		
		$this->assertEqual(false, $response);
	}
	
	public function testYearAugMaxDay()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/8/31");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/1251676800", $response->getLocation());
	}
	
	public function testYearAugMaxDayPlusOne()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/8/32");
		
		$this->assertEqual(false, $response);
	}
	
	public function testYearSepMaxDay()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/9/30");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/1254268800", $response->getLocation());
	}
	
	public function testYearSepMaxDayPlusOne()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/9/31");
		
		$this->assertEqual(false, $response);
	}
	
	public function testYearOctMaxDay()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/10/31");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/1256947200", $response->getLocation());
	}
	
	public function testYearOctMaxDayPlusOne()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/10/32");
		
		$this->assertEqual(false, $response);
	}
	
	public function testYearNovMaxDay()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/11/30");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/1259539200", $response->getLocation());
	}
	
	public function testYearNovMaxDayPlusOne()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/11/32");
		
		$this->assertEqual(false, $response);
	}
	
	public function testYearDecMaxDay()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/12/31");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/1262217600", $response->getLocation());
	}
	
	public function testYearDecMaxDayPlusOne()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/12/32");
		
		$this->assertEqual(false, $response);
	}
	
	public function testYearZeroMonthDay()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/0/1");
		
		$this->assertEqual(false, $response);
	}
	
	public function testZeroYearMonthDay()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/0000/1/1");
		
		$this->assertEqual(false, $response);
	}
	
	public function testYearMonthDayFirstDay()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/1970/1/1");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/0", $response->getLocation());
	}
	
	public function testYearMonthDayBeforeFirstDay()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/1969/12/31");
		
		$this->assertEqual(false, $response);
	}
	
	public function testYearSingleDigitMonth()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/2");
		
		$this->assertEqual(false, $response);
	}
	
	public function testYearTwoDigitMonth()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/12");
		
		$this->assertEqual(false, $response);
	}
	
	public function testYearLeadingZeroMonth()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/02");
		
		$this->assertEqual(false, $response);
	}
	
	public function testYearMonthDaySingleDigitHour()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/1/1/5");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/1230786000", $response->getLocation());
	}
	
	public function testYearMonthDayTwoDigitHour()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/1/1/15");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/1230822000", $response->getLocation());
	}
	
	public function testYearMonthDayLeadingZeroHour()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/1/1/05");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/1230786000", $response->getLocation());
	}
	
	public function testYearMonthDaySingleDigitHourSingleDigitMinute()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/1/1/5/5");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/1230786300", $response->getLocation());
	}
	
	public function testYearMonthDayTwoDigitHourSingleDigitMinute()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/1/1/15/5");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/1230822300", $response->getLocation());
	}
	
	public function testYearMonthDayLeadingZeroHourSingleDigitMinute()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/1/1/05/5");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/1230786300", $response->getLocation());
	}
	
	public function testYearMonthDaySingleDigitHourTwoDigitMinute()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/1/1/5/15");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/1230786900", $response->getLocation());
	}
	
	public function testYearMonthDayTwoDigitHourTwoDigitMinute()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/1/1/15/15");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/1230822900", $response->getLocation());
	}
	
	public function testYearMonthDayLeadingZeroHourTwoDigitMinute()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/1/1/05/15");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/1230786900", $response->getLocation());
	}
	
	public function testYearMonthDaySingleDigitHourLeadingZeroMinute()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/1/1/5/05");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/1230786300", $response->getLocation());
	}
	
	public function testYearMonthDayTwoDigitHourLeadingZeroMinute()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/1/1/15/05");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/1230822300", $response->getLocation());
	}
	
	public function testYearMonthDayLeadingZeroHourLeadingZeroMinute()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/1/1/5/05");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/1230786300", $response->getLocation());
	}
	
	public function testYearMonthDayHourMinuteSingleDigitSecond()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/1/1/5/5/5");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/1230786305", $response->getLocation());
	}
	
	public function testYearMonthDayHourMinuteTwoDigitSecond()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/1/1/5/5/15");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/1230786315", $response->getLocation());
	}
	
	public function testYearMonthDayHourMinuteLeadingZeroSecond()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/1/1/5/5/05");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/1230786305", $response->getLocation());
	}
	
	public function testNegativeYear()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/-2009/1/1/5/5/5");
		
		$this->assertEqual(false, $response);
	}
	
	public function testNegativeMonth()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/-1/1/5/5/5");
		
		$this->assertEqual(false, $response);
	}
	
	public function testOverMaxMonth()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/13/1/5/5/5");
		
		$this->assertEqual(false, $response);
	}
	
	public function testNegativeDay()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/1/-1/5/5/5");
		
		$this->assertEqual(false, $response);
	}
	
	public function testNegativeHour()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/1/1/-5/5/5");
		
		$this->assertEqual(false, $response);
	}
	
	public function testOverMaxHour()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/1/1/25/5/5");
		
		$this->assertEqual(false, $response);
	}
	
	public function testNegativeMinute()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/1/1/5/-5/5");
		
		$this->assertEqual(false, $response);
	}
	
	public function testOverMaxMinute()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/1/1/5/61/5");
		
		$this->assertEqual(false, $response);
	}
	
	public function testNegativeSecond()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/1/1/5/5/-5");
		
		$this->assertEqual(false, $response);
	}
	
	public function testOverMaxSecond()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/2009/1/1/5/5/61");
		
		$this->assertEqual(false, $response);
	}
	
	public function testLastWednesday()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template, 123456789);
		$response = $handler->handleRequest("/Last+Wednesday");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/" . strtotime("Last Wednesday", 123456789), $response->getLocation());
	}
	
	public function testNow()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template, 123456789);
		$response = $handler->handleRequest("/now");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/123456789", $response->getLocation());
	}
	
	public function testString()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/foobar");
		
		$this->assertEqual(false, $response);
	}
	
	public function testHomePage()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template, 123456789);
		$response = $handler->handleRequest("/");
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/123456789", $response->getLocation());
	}
	
	public function testPostTimestamp()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/", array("time" => "123456789"));
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/123456789", $response->getLocation());
	}
	
	public function testPostZeroTimestamp()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template);
		$response = $handler->handleRequest("/", array("time" => "0"));
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/0", $response->getLocation());
	}
	
	public function testPostNegativeTimestamp()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template, 123456789);
		$response = $handler->handleRequest("/", array("time" => "-123456789"));
		
		$this->assertEqual(false, $response);
	}
	
	public function testPostDate()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template, 123456789);
		$response = $handler->handleRequest("/", array("time" => "8/31/2009"));
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/1251676800", $response->getLocation());
	}
	
	public function testPostNow()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template, 123456789);
		$response = $handler->handleRequest("/", array("time" => "now"));
		
		$this->assertNotEqual(false, $response);
		$this->assertEqual("/123456789", $response->getLocation());
	}
	
	public function testPostString()
	{
		$template = new MockTemplate();
		$template->expectCallCount("setVar", 0);
		
		$handler = new TimestampRequestHandler($template, 123456789);
		$response = $handler->handleRequest("/", array("time" => "foobar"));
		
		$this->assertEqual(false, $response);
	}
}
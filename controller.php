<?php

$classDirectories = array("classes");

function __autoload($className)
{
	global $classDirectories;
	$filename = null;
	foreach($classDirectories as $classDirectory)
	{
		$candidateFilename = sprintf("%s/%s.php", $classDirectory, $className);
		if(file_exists($candidateFilename))
		{
			$filename = $candidateFilename;
			break; // exit foreach loop
		}
	}
	
	if(is_null($filename))
	{
		throw new NoClassFileException($className);
	}
	
	require_once($filename);
}

// set up error reporting
ini_set("error_reporting", E_ALL ^ E_STRICT);
error_reporting(E_ALL | E_STRICT);

// set up error handling
function exception_error_handler($errno, $errstr, $errfile, $errline)
{
	throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
set_error_handler("exception_error_handler");

try
{
	try
	{
		// set up configuration
		if(file_exists("config.php"))
		{
			require_once("config.php");
		}
		
		// set up timezone
		date_default_timezone_set("UTC");
		
		// set up navigation
		$navigation = new Navigation();
		$navigation[] = new NavigationItem("Usage", "/usage");
		$navigation[] = new NavigationItem("Now", "/now");
		$navigation[] = new NavigationItem("Bookmarklet", "javascript:var%20s=null;if(window.getSelection)s=window.getSelection();else%20if(document.getSelection)s=document.getSelection();else%20if(document.selection)s=document.selection.createRange().text;url=%27http://unixtimesta.mp/%27;if(s)url+=encodeURIComponent(s);document.location.href=url;");
		
		// set up error responses
		$pageNotFoundResponse = new PageNotFoundTemplateResponse(new Template("templates/page-not-found.template"));
		
		// set up request handlers
		$requestHandlers = array();
		// javascript
		$requestHandlers[] = new FilesystemRequestHandler("/^\/date$/", "javascript/date-en-US.js");
		$requestHandlers[] = new FilesystemRequestHandler("/^\/timestamp$/", "javascript/timestamp.js");
		// robots.txt
		$requestHandlers[] = new FilesystemRequestHandler("/^\/robots.txt/", "robots.txt");
		// favicon.ico
		$requestHandlers[] = new ResponseRequestHandler("/^\/favicon\.ico/", $pageNotFoundResponse);
		// timestamp
		$requestHandlers[] = new TimestampRequestHandler(new Template("templates/timestamp.template"));
		// usage
		$requestHandlers[] = new TemplateRequestHandler("/^\/usage/", new Template("templates/usage.template"));
		// sitemap
		$requestHandlers[] = new SitemapRequestHandler(new Template("templates/sitemap.template"));
		$requestHandlers[] = new SitemapIndexRequestHandler(new Template("templates/sitemapindex.template"));
		// non-production request handlers
		if(!Config::Get("production", false))
		{
			// test
			$requestHandlers[] = new TestRequestHandler(new Template("templates/tests.template"));
			// phpinfo
			$requestHandlers[] = new PhpInfoRequestHandler();
		}
		// 404 (*must* be the last request handler)
		$requestHandlers[] = new CatchAllRequestHandler($pageNotFoundResponse);
		// tie request handlers together
		$requestHandler = new AggregateRequestHandler($requestHandlers);
		// get the response
		$response = $requestHandler->handleRequest();
	}
	catch(Exception $e)
	{
		// attempt to create pretty exception message
		$template = new Template("templates/exception.template");
		$template->setVar("class", get_class($e));
		$template->setVar("message", $e->getMessage());
		
		foreach($e->getTrace() as $stack)
		{
			if($stack["function"] != "exception_error_handler")
			{
				$file = "";
				if(isset($stack["file"]))
				{
					$file = str_replace(realpath("."), "~", $stack["file"]);
				}
				
				$line = "";
				if(isset($stack["line"]))
				{
					$line = $stack["line"];
				}
				
				$template->gotoNext("stack");
				$template->setVar("stack.file", $file);
				$template->setVar("stack.line", $line);
				$template->setVar("stack.function", $stack["function"]);
			}
		}
		
		$log = Logger::GetLog(Logger::INFO);
		foreach($log as $logEntry)
		{
			$template->gotoNext("log");
			$template->setVar("log.level", $logEntry["level"]);
			$template->setVar("log.message", htmlspecialchars($logEntry["message"]));
			$template->setVar("log.time", $logEntry["time"]);
		}
		
		$response = new TemplateResponse($template);
	}
	
	// set up response filters
	$responseFilters = array();
	// form
	$responseFilters[] = new TemplateInjectionResponseFilter(new Template("templates/form.template"), '/<!-- form here -->/');
	// navigation
	$responseFilters[] = new NavigationInjectionResponseFilter($navigation);
	// footer
	$responseFilters[] = new FooterTemplateInjectionResponseFilter(new Template("templates/footer.template"));
	// javascript
	$responseFilters[] = new JavaScriptInjectionResponseFilter("http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js");
	$responseFilters[] = new JavaScriptInjectionResponseFilter("/date");
	$responseFilters[] = new JavaScriptInjectionResponseFilter("/timestamp");
	// css
	$responseFilters[] = new StylesheetInjectionResponseFilter("http://craiga.id.au/main.css", "all");
	// search engine verification meta tags
	$searchEngineVerificationKeys = array("google-site-verification", "msvalidate.01", "y_key");
	foreach($searchEngineVerificationKeys as $searchEngineVerificationKey)
	{
		try
		{
			$responseFilters[] = new MetaTagInjectionResponseFilter($searchEngineVerificationKey, Config::Get($searchEngineVerificationKey));
		}
		catch(ConfigNotSetException $e)
		{
			// configuration not set; ignore
		}
	}
	// googalytics
	try
	{
		$googleAnalyticsTemplate = new Template("templates/google-analytics.template");
		$googleAnalyticsTemplate->setVar("google-analytics-account", Config::Get("google-analytics-account"));
		$responseFilters[] = new FooterTemplateInjectionResponseFilter($googleAnalyticsTemplate);
	}
	catch(ConfigNotSetException $e)
	{
		// configuration not set; ignore
	}
	// compatability for non-XHTML compliant browsers
	$responseFilters[] = new MSIECompatabilityResponseFilter(new HeaderTemplateInjectionResponseFilter(new Template("templates/compatability-message.template")));
	$responseFilters[] = new MSIECompatabilityResponseFilter(new XHTMLCompatabilityResponseFilter(), 8);
	$responseFilters[] = new LynxCompatabilityResponseFilter(new XHTMLCompatabilityResponseFilter());
	// non-production response filters
	if(!Config::Get("production", false))
	{
		// log messages
		$responseFilters[] = new LogInjectionResponseFilter(new Template("templates/log.template"));
	}
	// tie response filters together
	$responseFilter = new AggregateResponseFilter($responseFilters);
	// set filter in the response
	$response->setFilter($responseFilter);
	
	// finally, send the response
	$response->send();
}
catch(Exception $e)
{
	// send ugly, text-only error message
	if(!headers_sent())
	{
		header("Content-type: text/plain");
	}	
	printf("An unhandled %s occurred.\n\n%s\n\n", get_class($e), $e->getMessage());
	printf("%s (%d)\n", $e->getFile(), $e->getLine());
	printf($e->getTraceAsString());
}

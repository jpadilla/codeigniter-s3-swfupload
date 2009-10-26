<?php
/**
 * File: TarzanCore
 * 	Core functionality and default settings shared across classes.
 *
 * Version:
 * 	2009.08.13
 *
 * Copyright:
 * 	2006-2009 LifeNexus Digital, Inc., and contributors.
 *
 * License:
 * 	Simplified BSD License - http://opensource.org/licenses/bsd-license.php
 *
 * See Also:
 * 	Tarzan - http://tarzan-aws.com
 */


/*%******************************************************************************************%*/
// CORE DEPENDENCIES

// Include the Tarzan config file
if (file_exists(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.inc.php'))
{
	include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.inc.php';
}


/*%******************************************************************************************%*/
// CONSTANTS

/**
 * Constant: TARZAN_NAME
 * Name of the software.
 */
define('TARZAN_NAME', 'Tarzan');

/**
 * Constant: TARZAN_VERSION
 * Version of the software.
 */
define('TARZAN_VERSION', '2.0.5');

/**
 * Constant: TARZAN_BUILD
 * Build ID of the software.
 */
// define('TARZAN_BUILD', gmdate('YmdHis', strtotime(substr('$Date$', 7, 25)) ? strtotime(substr('$Date$', 7, 25)) : filemtime(__FILE__)));
define('TARZAN_BUILD', '20090813151546');

/**
 * Constant: TARZAN_URL
 * URL to learn more about the software.
 */
define('TARZAN_URL', 'http://tarzan-aws.com');

/**
 * Constant: TARZAN_USERAGENT
 * User agent string used to identify Tarzan
 * > Tarzan/2.0 (Amazon Web Services API; http://tarzan-aws.com) Build/20080927210040
 */
define('TARZAN_USERAGENT', TARZAN_NAME . '/' . TARZAN_VERSION . ' (Amazon Web Services API; ' . TARZAN_URL . ') Build/' . TARZAN_BUILD);

/**
 * Constant: DATE_AWS_RFC2616
 * Define the RFC 2616-compliant date format
 */
define('DATE_AWS_RFC2616', 'D, d M Y H:i:s \G\M\T');

/**
 * Constant: DATE_AWS_ISO8601
 * Define the ISO-8601-compliant date format
 */
define('DATE_AWS_ISO8601', 'Y-m-d\TH:i:s\Z');

/**
 * Constant: DATE_AWS_MYSQL
 * Define the MySQL-compliant date format
 */
define('DATE_AWS_MYSQL', 'Y-m-d H:i:s');

/**
 * Constant: HTTP_GET
 * HTTP method type: Get
 */
define('HTTP_GET', 'GET');

/**
 * Constant: HTTP_POST
 * HTTP method type: Post
 */
define('HTTP_POST', 'POST');

/**
 * Constant: HTTP_PUT
 * HTTP method type: Put
 */
define('HTTP_PUT', 'PUT');

/**
 * Constant: HTTP_DELETE
 * HTTP method type: Delete
 */
define('HTTP_DELETE', 'DELETE');

/**
 * Constant: HTTP_HEAD
 * HTTP method type: Head
 */
define('HTTP_HEAD', 'HEAD');


/*%******************************************************************************************%*/
// EXCEPTIONS

/**
 * Exception: TarzanCore_Exception
 * 	Default TarzanCore Exception.
 */
class TarzanCore_Exception extends Exception {}


/*%******************************************************************************************%*/
// CLASS

/**
 * Class: TarzanCore
 * 	Container for all shared methods. This is not intended to be instantiated directly, but is extended by the Amazon-specific classes.
 */
class TarzanCore
{
	/**
	 * Property: key
	 * The Amazon API Key.
	 */
	var $key;

	/**
	 * Property: secret_key
	 * The Amazon API Secret Key.
	 */
	var $secret_key;

	/**
	 * Property: account_id
	 * The Amazon Account ID, sans hyphens.
	 */
	var $account_id;

	/**
	 * Property: assoc_id
	 * The Amazon Associates ID.
	 */
	var $assoc_id;

	/**
	 * Property: util
	 * Handle for the utility functions.
	 */
	var $util;

	/**
	 * Property: service
	 * An identifier for the current AWS service.
	 */
	var $service = null;

	/**
	 * Property: api_version
	 * The supported API version.
	 */
	var $api_version = null;

	/**
	 * Property: utilities_class
	 * The default class to use for Utilities (defaults to <TarzanUtilities>).
	 */
	var $utilities_class = 'TarzanUtilities';

	/**
	 * Property: request_class
	 * The default class to use for HTTP Requests (defaults to <RequestCore>).
	 */
	var $request_class = 'TarzanHTTPRequest';

	/**
	 * Property: response_class
	 * The default class to use for HTTP Responses (defaults to <TarzanHTTPResponse>).
	 */
	var $response_class = 'TarzanHTTPResponse';

	/**
	 * Property: adjust_offset
	 * The number of seconds to adjust the request timestamp by (defaults to 0).
	 */
	var $adjust_offset = 0;

	/**
	 * Property: enable_ssl
	 * 	Whether SSL/HTTPS should be enabled by default.
	 */
	var $enable_ssl = true;

	/**
	 * Property: set_proxy
	 * 	Sets the proxy to use for connecting.
	 */
	var $set_proxy = null;

	/**
	 * Property: devpay_tokens
	 * 	Stores the Amazon DevPay tokens to use, if any.
	 */
	var $devpay_tokens;

	/**
	 * Property: set_hostname
	 * 	Stores the alternate hostname to use, if any.
	 */
	var $hostname = null;


	/*%******************************************************************************************%*/
	// AUTO-LOADER

	/**
	 * Method: autoloader()
	 * 	Automatically load classes that aren't included.
	 *
	 * Access:
	 * 	public static
	 *
	 * Parameters:
	 * 	class_name - _string_ (Required) The classname to load.
	 *
	 * Returns:
	 * 	void
	 */
	public static function autoloader($class)
	{
		$path = dirname(__FILE__) . DIRECTORY_SEPARATOR;

		if (stristr($class, 'amazon'))
		{
			$path .= str_replace('amazon', '', strtolower($class)) . '.class.php';
		}
		elseif (stristr($class, 'tarzan'))
		{
			$path .= str_replace('tarzan', '_', strtolower($class)) . '.class.php';
		}
		elseif (stristr($class, 'cache'))
		{
			$path .= '_' . strtolower($class) . '.class.php';
		}
		else
		{
			$path = '';
		}

		if (file_exists($path))
		{
			require_once($path);
		}
	}


	/*%******************************************************************************************%*/
	// CONSTRUCTOR

	/**
	 * Method: __construct()
	 * 	The constructor
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	key - _string_ (Optional) Your Amazon API Key. If blank, it will look for the <AWS_KEY> constant.
	 * 	secret_key - _string_ (Optional) Your Amazon API Secret Key. If blank, it will look for the <AWS_SECRET_KEY> constant.
	 * 	account_id - _string_ (Optional) Your Amazon account ID without the hyphens. Required for EC2. If blank, it will look for the <AWS_ACCOUNT_ID> constant.
	 * 	assoc_id - _string_ (Optional) Your Amazon Associates ID. Required for AAWS. If blank, it will look for the <AWS_ASSOC_ID> constant.
	 *
	 * Returns:
	 * 	boolean FALSE if no valid values are set, otherwise true.
	 */
	public function __construct($key = null, $secret_key = null, $account_id = null, $assoc_id = null)
	{
		// Instantiate the utilities class.
		$this->util = new $this->utilities_class();

		// Determine the current service.
		$this->service = get_class($this);

		// Set default values
		$this->key = null;
		$this->secret_key = null;
		$this->account_id = null;
		$this->assoc_id = null;

		// Set the Account ID
		if ($account_id)
		{
			$this->account_id = $account_id;
		}
		elseif (defined('AWS_ACCOUNT_ID'))
		{
			$this->account_id = AWS_ACCOUNT_ID;
		}

		// Set the Associates ID
		if ($assoc_id)
		{
			$this->assoc_id = $assoc_id;
		}
		elseif (defined('AWS_ASSOC_ID'))
		{
			$this->assoc_id = AWS_ASSOC_ID;
		}

		// If both a key and secret key are passed in, use those.
		if ($key && $secret_key)
		{
			$this->key = $key;
			$this->secret_key = $secret_key;
			return true;
		}
		// If neither are passed in, look for the constants instead.
		else if (defined('AWS_KEY') && defined('AWS_SECRET_KEY'))
		{
			$this->key = AWS_KEY;
			$this->secret_key = AWS_SECRET_KEY;
			return true;
		}

		// Otherwise set the values to blank and return false.
		else
		{
			throw new TarzanCore_Exception('No valid credentials were used to authenticate with AWS.');
		}
	}


	/*%******************************************************************************************%*/
	// SET CUSTOM SETTINGS

	/**
	 * Method: adjust_offset()
	 * 	Allows you to adjust the current time, for occasions when your server is out of sync with Amazon's servers.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	seconds - _integer_ (Required) The number of seconds to adjust the sent timestamp by.
	 *
	 * Returns:
	 * 	void
 	 *
	 * See Also:
	 * 	Example Usage - http://tarzan-aws.com/docs/examples/tarzan/adjust_offset.phps
	 */
	public function adjust_offset($seconds)
	{
		$this->adjust_offset = $seconds;
	}

	/**
	 * Method: set_proxy()
	 * 	Set the proxy settings to use for connecting.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	proxy - _string_ (Required) Accepts proxy credentials in the following format: proxy://user:pass@hostname:port
	 *
	 * Returns:
	 * 	void
 	 *
	 * See Also:
	 * 	Example Usage - http://tarzan-aws.com/docs/examples/tarzan/set_proxy.phps
	 */
	public function set_proxy($proxy)
	{
		$this->set_proxy = $proxy;
	}

	/**
	 * Method: set_hostname()
	 * 	Set the hostname to use for connecting.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	hostname - _string_ (Required) The alternate hostname to use in place of the default one. Useful for API-compatible applications living on different hostnames.
	 *
	 * Returns:
	 * 	void
	 */
	public function set_hostname($hostname)
	{
		$this->hostname = $hostname;
	}

	/**
	 * Method: disable_ssl()
	 * 	Disables SSL/HTTPS connections for hosts that don't support them.
	 *
	 * Access:
	 * 	public
	 *
	 * Returns:
	 * 	void
	 */
	public function disable_ssl()
	{
		$this->enable_ssl = false;
	}


	/*%******************************************************************************************%*/
	// SET CUSTOM CLASSES

	/**
	 * Method: set_utilities_class()
	 * 	Set a custom class for this functionality. Perfect for extending/overriding existing classes with new functionality.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	class - _string_ (Optional) The name of the new class to use for this functionality. Defaults to the default class.
	 *
	 * Returns:
	 * 	void
 	 *
	 * See Also:
	 * 	Example Usage - http://tarzan-aws.com/docs/examples/tarzan/set_utilities_class.phps
	 */
	function set_utilities_class($class = 'TarzanUtilities')
	{
		$this->utilities_class = $class;
		$this->util = new $this->utilities_class();
	}

	/**
	 * Method: set_request_class()
	 * 	Set a custom class for this functionality. Perfect for extending/overriding existing classes with new functionality.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	class - _string_ (Optional) The name of the new class to use for this functionality. Defaults to the default class.
	 *
	 * Returns:
	 * 	void
 	 *
	 * See Also:
	 * 	Example Usage - http://tarzan-aws.com/docs/examples/tarzan/set_request_class.phps
	 */
	function set_request_class($class = 'TarzanHTTPRequest')
	{
		$this->request_class = $class;
	}

	/**
	 * Method: set_response_class()
	 * 	Set a custom class for this functionality. Perfect for extending/overriding existing classes with new functionality.
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	class - _string_ (Optional) The name of the new class to use for this functionality. Defaults to the default class.
	 *
	 * Returns:
	 * 	void
 	 *
	 * See Also:
	 * 	Example Usage - http://tarzan-aws.com/docs/examples/tarzan/set_response_class.phps
	 */
	function set_response_class($class = 'TarzanHTTPResponse')
	{
		$this->response_class = $class;
	}


	/*%******************************************************************************************%*/
	// AUTHENTICATION

	/**
	 * Method: authenticate()
	 * 	Default, shared method for authenticating a connection to AWS. Overridden on a class-by-class basis as necessary. This should not be used directly unless you're writing custom methods for this class.
	 *
	 * Access:
	 * 	public
 	 *
	 * Parameters:
	 * 	action - _string_ (Required) Indicates the action to perform.
	 * 	opt - _array_ (Optional) Associative array of parameters for authenticating. See the individual methods for allowed keys.
	 * 	domain - _string_ (Optional) The URL of the queue to perform the action on.
	 * 	message - _string_ (Optional) This parameter is only used by the send_message() method.
	 *
	 * Returns:
	 * 	<TarzanHTTPResponse> object
	 */
	public function authenticate($action, $opt = null, $domain = null, $message = null)
	{
		$return_curl_handle = false;
		$key_prepend = 'AWSAccessKeyId=' . $this->key . '&';

		// Manage the key-value pairs that are used in the query.
		$query['Action'] = $action;
		$query['SignatureMethod'] = 'HmacSHA256';
		$query['SignatureVersion'] = 2;
		$query['Timestamp'] = gmdate(DATE_AWS_ISO8601, time() + $this->adjust_offset);
		$query['Version'] = $this->api_version;

		// Merge in any options that were passed in
		if (is_array($opt))
		{
			$query = array_merge($query, $opt);
		}

		$return_curl_handle = isset($query['returnCurlHandle']) ? $query['returnCurlHandle'] : false;
		unset($query['returnCurlHandle']);

		// Do a case-insensitive, natural order sort on the array keys.
		uksort($query, 'strcasecmp');

		// Create the string that needs to be hashed.
		$canonical_query_string = $key_prepend . $this->util->to_signable_string($query);

		// Set the proper verb.
		$verb = HTTP_GET;
		if ($message) $verb = HTTP_POST;

		// Remove the default scheme from the domain.
		$domain = str_replace(array('http://', 'https://'), '', $domain);

		// Parse our request.
		$parsed_url = parse_url('http://' . $domain);

		// Set the proper host header.
		$host_header = strtolower($parsed_url['host']);

		// Set the proper request URI.
		$request_uri = isset($parsed_url['path']) ? $parsed_url['path'] : '/';

		// Prepare the string to sign
		$stringToSign = "$verb\n$host_header\n$request_uri\n$canonical_query_string";

		// Hash the AWS secret key and generate a signature for the request.
		$query['Signature'] = $this->util->hex_to_base64(hash_hmac('sha256', $stringToSign, $this->secret_key));

		// Generate the querystring from $query
		$querystring = $key_prepend . $this->util->to_query_string($query);

		// Gather information to pass along to other classes.
		$helpers = array(
			'utilities' => $this->utilities_class,
			'request' => $this->request_class,
			'response' => $this->response_class,
		);

		// Compose the request.
		$request_url = 'https://' . $domain;
		$request_url .= !isset($parsed_url['path']) ? '/' : '';
		$request_url .= '?' . $querystring;
		$request = new $this->request_class($request_url, $this->set_proxy, $helpers);

		// Set DevPay tokens if we have them.
		if ($this->devpay_tokens)
		{
			$request->addHeader('x-amz-security-token', $this->devpay_tokens);
		}

		// Tweak some things if we have a message (i.e. AmazonSQS::send_message()).
		if ($message)
		{
			$request->addHeader('Content-Type', 'text/plain');
			$request->setMethod(HTTP_POST);
			$request->setBody($message);
		}

		// If we have a "true" value for returnCurlHandle, do that instead of completing the request.
		if ($return_curl_handle)
		{
			return $request->prepRequest();
		}

		// Send!
		$request->sendRequest();

		// Prepare the response.
		$headers = $request->getResponseHeader();
		$headers['x-tarzan-requesturl'] = $request_url;
		$headers['x-tarzan-stringtosign'] = $stringToSign;
		if ($message) $headers['x-tarzan-body'] = $message;
		$data = new $this->response_class($headers, $request->getResponseBody(), $request->getResponseCode());

		// Return!
		return $data;
	}


	/*%******************************************************************************************%*/
	// CACHING LAYER

	/**
	 * Method: cache_response()
	 * 	Caches a TarzanHTTPResponse object using the preferred caching method.
	 *
	 * Access:
	 * 	public
 	 *
	 * Parameters:
	 * 	method - _string_ (Required) The method of the current object that you want to execute and cache the response for. If the method is not in the $this scope, pass in an array where the correct scope is in the [0] position and the method name is in the [1] position.
	 * 	location - _string_ (Required) The location to store the cache object in. This may vary by cache method. See below.
	 * 	expires - _integer_ (Required) The number of seconds until a cache object is considered stale.
	 * 	params - _array_ (Optional) An indexed array of parameters to pass to the aforementioned method, where array[0] represents the first parameter, array[1] is the second, etc.
	 *
	 * Example values for $location:
	 * 	File - Local file system paths such as ./cache (relative) or /tmp/cache/tarzan (absolute). Location must be server-writable.
	 * 	APC - Pass in 'apc' to use this lightweight cache. You must have the APC extension installed. <http://php.net/apc>
	 * 	Memcached - Pass in an indexed array of associative arrays. Each associative array should have a 'host' and a 'port' value representing a Memcached server to connect to.
	 * 	PDO - A URL-style string (e.g. pdo.mysql://user:pass@localhost/tarzan_cache) or a standard DSN-style string (e.g. pdo.sqlite:/sqlite/tarzan_cache.db). MUST be prefixed with 'pdo.'. See <CachePDO> and <http://php.net/pdo> for more details.
	 *
	 * Returns:
	 * 	<TarzanHTTPResponse> object
 	 *
	 * See Also:
	 * 	Example Usage - http://tarzan-aws.com/docs/examples/tarzan/cache_response.phps
	 */
	public function cache_response($method, $location, $expires, $params = null)
	{
		$_this = $this;
		if (is_array($method))
		{
			$_this = $method[0];
			$method = $method[1];
		}

		// If we have an array, we're probably passing in Memcached servers and ports.
		if (is_array($location))
		{
			$CacheMethod = 'CacheMC';
		}
		else
		{
			// I would expect locations like '/tmp/cache', 'pdo.mysql://user:pass@hostname:port', 'pdo.sqlite:memory:', and 'apc'.
			$type = strtolower(substr($location, 0, 3));
			switch ($type)
			{
				case 'apc':
					$CacheMethod = 'CacheAPC';
					break;

				case 'pdo':
					$CacheMethod = 'CachePDO';
					$location = substr($location, 4);
					break;

				default:
					$CacheMethod = 'CacheFile';
					break;
			}
		}

		// Once we've determined the preferred caching method, instantiate a new cache.
		if (isset($_this->key))
		{
			$cache_uuid = $method . '-' . $_this->key . '-' . sha1($method . serialize($params));
		}
		else
		{
			$cache_uuid = $method . '-' . 'nokey' . '-' . sha1($method . serialize($params));
		}

		$cache = new $CacheMethod($cache_uuid, $location, $expires);

		// If the data exists...
		if ($data = $cache->read())
		{
			// It exists, but is it expired?
			if ($cache->is_expired())
			{
				// If so, fetch new data from Amazon.
				if ($data = call_user_func_array(array($_this, $method), $params))
				{
					if (is_array($data))
					{
						$copy = array();

						for ($i = 0, $len = sizeof($data); $i < $len; $i++)
						{
							// We need to convert the SimpleXML data back to real XML before the cache methods serialize it. <http://bugs.php.net/28152>
							$copy[$i] = clone($data[$i]);

							if (get_class($copy[$i]->body) == 'SimpleXMLElement')
							{
								$copy[$i]->body = $copy[$i]->body->asXML();
							}
						}

						// Cache the data
						$cache->update($copy);

						// Free the unused memory.
						$copy = null;
						unset($copy);
					}
					else
					{
						// We need to convert the SimpleXML data back to real XML before the cache methods serialize it. <http://bugs.php.net/28152>
						$copy = clone($data);

						if (get_class($copy->body) == 'SimpleXMLElement')
						{
							$copy->body = $copy->body->asXML();
						}

						// Cache the data
						$cache->update($copy);

						// Free the unused memory.
						$copy = null;
						unset($copy);
					}
				}

				// We did not get back good data from Amazon...
				else
				{
					// ...so we'll reset the freshness of the cache and use it again (if supported by the caching method).
					$cache->reset();
				}
			}

			// It exists and is still fresh. Let's use it.
			else
			{
				if (is_array($data))
				{
					for ($i = 0, $len = sizeof($data); $i < $len; $i++)
					{
						// We need to convert the SimpleXML data back to real XML before the cache methods serialize it. <http://bugs.php.net/28152>
						if (substr(trim($data[$i]->body), 0, 5) == '<?xml')
						{
							$data[$i]->body = new SimpleXMLElement($data[$i]->body);
						}
					}
				}
				else
				{
					// Since we're going to use this, let's convert the XML back into a SimpleXML object.
					if (substr(trim($data->body), 0, 5) == '<?xml')
					{
						$data->body = new SimpleXMLElement($data->body);
					}
				}
			}
		}

		// The data does not already exist in the cache.
		else
		{
			// Fetch it.
			if ($data = call_user_func_array(array($_this, $method), $params))
			{
				if (is_array($data))
				{
					$copy = array();

					for ($i = 0, $len = sizeof($data); $i < $len; $i++)
					{
						// We need to convert the SimpleXML data back to real XML before the cache methods serialize it. <http://bugs.php.net/28152>
						$copy[$i] = clone($data[$i]);

						if (get_class($copy[$i]->body) == 'SimpleXMLElement')
						{
							$copy[$i]->body = $copy[$i]->body->asXML();
						}
					}

					// Cache the data
					$cache->create($copy);

					// Free the unused memory.
					$copy = null;
					unset($copy);
				}
				else
				{
					// We need to convert the SimpleXML data back to real XML before the cache methods serialize it. <http://bugs.php.net/28152>
					$copy = clone($data);
					if (get_class($copy->body) == 'SimpleXMLElement')
					{
						$copy->body = $copy->body->asXML();
					}

					// Cache the data
					$cache->create($copy);

					// Free the unused memory.
					$copy = null;
					unset($copy);
				}
			}
		}

		// We're done. Return the data. Huzzah!
		return $data;
	}
}


// Register the autoloader.
spl_autoload_register(array('TarzanCore', 'autoloader'));

?>
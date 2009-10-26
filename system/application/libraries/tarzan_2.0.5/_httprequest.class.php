<?php
/**
 * File: TarzanHTTPRequest
 * 	Handles all linear and parallel HTTP requests using cURL.
 *
 * Version:
 * 	2009.02.28
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
// EXCEPTIONS

/**
 * Exception: TarzanHTTPRequest_Exception
 * 	Default TarzanHTTPRequest Exception.
 */
class TarzanHTTPRequest_Exception extends Exception {}


/*%******************************************************************************************%*/
// CLASS

/**
 * Class: TarzanHTTPRequest
 * 	Container for all request-related methods. The naming convention for methods mimics those of PEAR's HTTP_Request class.
 */
class TarzanHTTPRequest
{
	/**
	 * Property: request_url
	 * 	The URL being requested.
	 */
	var $request_url;

	/**
	 * Property: request_headers
	 * 	The headers being sent in the request.
	 */
	var $request_headers;

	/**
	 * Property: request_body
	 * 	The body being sent in the request.
	 */
	var $request_body;

	/**
	 * Property: response
	 * 	The response returned by the request.
	 */
	var $response;

	/**
	 * Property: response_headers
	 * 	The headers returned by the request.
	 */
	var $response_headers;

	/**
	 * Property: response_body
	 * 	The body returned by the request.
	 */
	var $response_body;

	/**
	 * Property: response_code
	 * 	The HTTP status code returned by the request.
	 */
	var $response_code;

	/**
	 * Property: response_info
	 * 	Additional response data.
	 */
	var $response_info;

	/**
	 * Property: curl_handle
	 * 	The handle for the cURL object.
	 */
	var $curl_handle;

	/**
	 * Property: method
	 * 	The method by which the request is being made.
	 */
	var $method;

	/**
	 * Property: proxy
	 * 	Stores the proxy settings to use for the request.
	 */
	var $proxy = null;

	/**
	 * Property: username
	 * 	The username to use for the request.
	 */
	var $username = null;

	/**
	 * Property: password
	 * 	The password to use for the request.
	 */
	var $password = null;

	/**
	 * Property: utilities_class
	 * The default class to use for Utilities (defaults to <TarzanUtilities>).
	 */
	var $utilities_class = 'TarzanUtilities';

	/**
	 * Property: request_class
	 * The default class to use for HTTP Requests (defaults to <TarzanHTTPRequest>).
	 */
	var $request_class = 'TarzanHTTPRequest';

	/**
	 * Property: response_class
	 * The default class to use for HTTP Responses (defaults to <TarzanHTTPResponse>).
	 */
	var $response_class = 'TarzanHTTPResponse';


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
	 * 	url - _string_ (Required) The URL to request or service endpoint to query.
	 * 	proxy - _string_ (Optional) The faux-url to use for proxy settings. Takes the following format: proxy://user:pass@hostname:port
	 * 	helpers - _array_ (Optional) An associative array of classnames to use for utilities, request, and response functionality. Gets passed in automatically by the calling class.
	 * 
	 * Returns:
	 * 	void
 	 * 
	 * See Also:
	 * 	Example Usage - http://tarzan-aws.com/docs/examples/httprequest/__construct.phps
	 */
	public function __construct($url, $proxy = null, $helpers = null)
	{
		// Set some default values.
		$this->request_url = $url;
		$this->method = HTTP_GET;
		$this->request_headers = array();
		$this->request_body = '';

		// Set a new Request class if one was set.
		if (isset($helpers['utilities']) && !empty($helpers['utilities']))
		{
			$this->utilities_class = $helpers['utilities'];
		}

		// Set a new Request class if one was set.
		if (isset($helpers['request']) && !empty($helpers['request']))
		{
			$this->request_class = $helpers['request'];
		}

		// Set a new Request class if one was set.
		if (isset($helpers['response']) && !empty($helpers['response']))
		{
			$this->response_class = $helpers['response'];
		}

		if ($proxy)
		{
			$proxy = parse_url($proxy);
			$proxy['user'] = isset($proxy['user']) ? $proxy['user'] : null;
			$proxy['pass'] = isset($proxy['pass']) ? $proxy['pass'] : null;
			$proxy['port'] = isset($proxy['port']) ? $proxy['port'] : null;
			$this->proxy = $proxy;
		}
	}


	/*%******************************************************************************************%*/
	// REQUEST METHODS

	/**
	 * Method: setCredentials()
	 * 	Sets the credentials to use for authentication.
	 * 
	 * Access:
	 * 	public
	 * 
	 * Parameters:
	 * 	user - _string_ (Required) The username to authenticate with.
	 * 	pass - _string_ (Required) The password to authenticate with.
	 * 
	 * Returns:
	 * 	void
 	 * 
	 * See Also:
	 * 	Example Usage - http://tarzan-aws.com/docs/examples/httprequest/set_credentials.phps
	 */
	public function setCredentials($user, $pass)
	{
		$this->username = $user;
		$this->password = $pass;
	}

	/**
	 * Method: addHeader()
	 * 	Adds a custom HTTP header to the cURL request.
	 * 
	 * Access:
	 * 	public
	 * 
	 * Parameters:
	 * 	key - _string_ (Required) The custom HTTP header to set.
	 * 	value - _mixed_ (Required) The value to assign to the custom HTTP header.
	 * 
	 * Returns:
	 * 	void
 	 * 
	 * See Also:
	 * 	Example Usage - http://tarzan-aws.com/docs/examples/httprequest/add_header.phps
	 */
	public function addHeader($key, $value)
	{
		$this->request_headers[$key] = $value;
	}

	/**
	 * Method: removeHeader()
	 * 	Removes an HTTP header from the cURL request.
	 * 
	 * Access:
	 * 	public
	 * 
	 * Parameters:
	 * 	key - _string_ (Required) The custom HTTP header to set.
	 * 
	 * Returns:
	 * 	void
 	 * 
	 * See Also:
	 * 	Example Usage - http://tarzan-aws.com/docs/examples/httprequest/remove_header.phps
	 */
	public function removeHeader($key)
	{
		if (isset($this->request_headers[$key]))
		{
			unset($this->request_headers[$key]);
		}
	}

	/**
	 * Method: setMethod()
	 * 	Set the method type for the request.
	 * 
	 * Access:
	 * 	public
	 * 
	 * Parameters:
	 * 	method - _string_ (Required) One of the following constants: <HTTP_GET>, <HTTP_POST>, <HTTP_PUT>, <HTTP_HEAD>, <HTTP_DELETE>.
	 * 
	 * Returns:
	 * 	void
 	 * 
	 * See Also:
	 * 	Example Usage - http://tarzan-aws.com/docs/examples/httprequest/set_method.phps
	 */
	public function setMethod($method)
	{
		$this->method = strtoupper($method);
	}

	/**
	 * Method: setBody()
	 * 	Set the body to send in the request.
	 * 
	 * Access:
	 * 	public
	 * 
	 * Parameters:
	 * 	body - _string_ (Required) The textual content to send along in the body of the request.
	 * 
	 * Returns:
	 * 	void
 	 * 
	 * See Also:
	 * 	Example Usage - http://tarzan-aws.com/docs/examples/httprequest/set_body.phps
	 */
	public function setBody($body)
	{
		$this->request_body = $body;
	}


	/*%******************************************************************************************%*/
	// PREPARE, SEND, AND PROCESS REQUEST

	/**
	 * Method: prepRequest()
	 * 	Prepares and adds the details of the cURL request. This can be passed along to a curl_multi_exec() function.
	 * 
	 * Access:
	 * 	public
	 * 
	 * Returns:
	 * 	The handle for the cURL object.
	 */
	public function prepRequest()
	{
		$this->addHeader('Expect', '100-continue');
		$this->addHeader('Connection', 'close');

		$curl_handle = curl_init();

		// Set default options.
 		curl_setopt($curl_handle, CURLOPT_URL, $this->request_url);
 		curl_setopt($curl_handle, CURLOPT_FILETIME, true);
 		curl_setopt($curl_handle, CURLOPT_FRESH_CONNECT, true);
 		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
 		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, true);
 		curl_setopt($curl_handle, CURLOPT_VERBOSE, false);
 		curl_setopt($curl_handle, CURLOPT_CLOSEPOLICY, CURLCLOSEPOLICY_LEAST_RECENTLY_USED);
		curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl_handle, CURLOPT_MAXREDIRS, 5);
		curl_setopt($curl_handle, CURLOPT_HEADER, true);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl_handle, CURLOPT_TIMEOUT, 5184000);
		curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 120);
		curl_setopt($curl_handle, CURLOPT_NOSIGNAL, true);
		curl_setopt($curl_handle, CURLOPT_REFERER, $this->request_url);
		curl_setopt($curl_handle, CURLOPT_USERAGENT, TARZAN_USERAGENT);

		// Enable a proxy connection if requested.
		if ($this->proxy)
		{
			curl_setopt($curl_handle, CURLOPT_HTTPPROXYTUNNEL, true);
		
			$host = $this->proxy['host'];
			$host .= ($this->proxy['port']) ? ':' . $this->proxy['port'] : '';
			curl_setopt($curl_handle, CURLOPT_PROXY, $host);
		
			if (isset($this->proxy['user']) && isset($this->proxy['pass']))
			{
				curl_setopt($curl_handle, CURLOPT_PROXYUSERPWD, $this->proxy['user'] . ':' . $this->proxy['pass']);
			}
		}

		// Set credentials for HTTP Basic/Digest Authentication.
		if ($this->username && $this->password)
		{
			curl_setopt($curl_handle, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
			curl_setopt($curl_handle, CURLOPT_USERPWD, $this->username . ':' . $this->password);
		}

		// Handle the encoding if we can.
		if (extension_loaded('zlib'))
		{
			curl_setopt($curl_handle, CURLOPT_ENCODING, '');
		}

		// Process custom headers
		if (isset($this->request_headers) && count($this->request_headers))
		{
			$temp_headers = array();

			foreach ($this->request_headers as $k => $v)
			{
				$temp_headers[] = $k . ': ' . $v;
			}

			curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $temp_headers);
		}

		switch ($this->method)
		{
			case HTTP_PUT:
				curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, 'PUT');
				curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $this->request_body);
				break;

			case HTTP_POST:
				curl_setopt($curl_handle, CURLOPT_POST, true);
				curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $this->request_body);
				break;

			case HTTP_HEAD:
				curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, HTTP_HEAD);
				curl_setopt($curl_handle, CURLOPT_NOBODY, 1);
				break;

			default:
				curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, $this->method);
				break;
		}

		return $curl_handle;
	}

	/**
	 * Method: processResponse()
	 * 	Take the post-processed cURL data and break it down into useful header/body/info chunks. Uses the data stored in the <curl_handle> and <response> properties unless replacement data is passed in via parameters.
	 * 
	 * Access:
	 * 	public
	 * 
	 * Parameters:
	 * 	curl_handle - _string_ (Optional) The reference to the already executed cURL request.
	 * 	response - _string_ (Optional) The actual response content itself that needs to be parsed.
	 * 
	 * Returns:
	 * 	<TarzanHTTPResponse> object
	 */
	public function processResponse($curl_handle = null, $response = null)
	{
		// Accept a custom one if it's passed.
		if ($curl_handle && $response)
		{
			$this->curl_handle = $curl_handle;
			$this->response = $response;
		}

		// As long as this came back as a valid resource...
		if (is_resource($this->curl_handle))
		{
			// Determine what's what.
			$header_size = curl_getinfo($this->curl_handle, CURLINFO_HEADER_SIZE);
			$this->response_headers = substr($this->response, 0, $header_size);
			$this->response_body = substr($this->response, $header_size);
			$this->response_code = curl_getinfo($this->curl_handle, CURLINFO_HTTP_CODE);
			$this->response_info = curl_getinfo($this->curl_handle);

			// Parse out the headers
			$this->response_headers = explode("\r\n\r\n", trim($this->response_headers));
			$this->response_headers = array_pop($this->response_headers);
			$this->response_headers = explode("\r\n", $this->response_headers);
			array_shift($this->response_headers);

			// Loop through and split up the headers.
			$header_assoc = array();
			foreach ($this->response_headers as $header)
			{
				$kv = explode(': ', $header);
				$header_assoc[strtolower($kv[0])] = $kv[1];
			}

			// Reset the headers to the appropriate property.
			$this->response_headers = $header_assoc;
			$this->response_headers['_info'] = $this->response_info;
			$this->response_headers['_info']['method'] = $this->method;

			if ($curl_handle && $response)
			{
				return new $this->response_class($this->response_headers, $this->response_body, $this->response_code);
			}
		}

		// Return false
		return false;
	}

	/**
	 * Method: sendRequest()
	 * 	Sends the request, calling necessary utility functions to update built-in properties.
	 * 
	 * Access:
	 * 	public
	 * 
	 * Returns:
	 * 	_string_ The resulting unparsed data from the request.
	 * 
	 * See Also:
	 * 	Related - <sendMultiRequest()>
	 * 	Example Usage - http://tarzan-aws.com/docs/examples/httprequest/send_request.phps
	 */
	public function sendRequest()
	{
		$curl_handle = $this->prepRequest();
		$this->response = curl_exec($curl_handle);
		$this->processResponse($curl_handle, $this->response);

		curl_close($curl_handle);
		return $this->response;
	}

	/**
	 * Method: sendMultiRequest()
	 * 	Sends the request using curl_multi_exec(), enabling parallel requests.
	 * 
	 * Access:
	 * 	public
	 * 
	 * Parameters:
	 * 	handles - _array_ (Required) An indexed array of cURL handles to process simultaneously.
	 * 
	 * Returns:
	 * 	_array_ Post-processed cURL responses.
	 * 
	 * See Also:
	 * 	Related - <sendRequest()>
	 * 	Example Usage - http://tarzan-aws.com/docs/examples/httprequest/send_multi_request.phps
	 */
	public function sendMultiRequest($handles)
	{
		// Initialize MultiCURL
		$multi_handle = curl_multi_init();

		// Loop through each of the CURL handles and add them to the MultiCURL request.
		foreach ($handles as $handle)
		{
			curl_multi_add_handle($multi_handle, $handle);
		}

		$count = 0;

		// Execute
		do
		{
			$status = curl_multi_exec($multi_handle, $active);
		}
		while ($status == CURLM_CALL_MULTI_PERFORM  || $active);

		// Define this.
		$handles_post = array();

		// Retrieve each handle response
		foreach ($handles as $handle)
		{
			if (curl_errno($handle) == CURLE_OK)
			{
				$http = new $this->request_class(null);
				$handles_post[] = $http->processResponse($handle, curl_multi_getcontent($handle));
			}
			else
			{
				throw new TarzanHTTPRequest_Exception(curl_error($handle));
			}

			// Explicitly close each cURL handle.
			curl_multi_remove_handle($multi_handle, $handle);
			curl_close($handle);
		}

		// Explicitly close the MultiCurl handle.
		curl_multi_close($multi_handle);

		return $handles_post;
	}


	/*%******************************************************************************************%*/
	// RESPONSE METHODS

	/**
	 * Method: getResponseHeader()
	 * 	Get the HTTP response headers from the request.
	 * 
	 * Access:
	 * 	public
	 * 
	 * Parameters:
	 * 	header - _string_ (Optional) A specific header value to return. Defaults to all headers.
	 * 
	 * Returns:
	 * 	_string_|_array_ All or selected header values.
	 * 
	 * See Also:
	 * 	Related - <getResponseBody()>, <getResponseCode()>
	 * 	Example Usage - http://tarzan-aws.com/docs/examples/httprequest/get_response_header.phps
	 */
	public function getResponseHeader($header = null)
	{
		if ($header)
		{
			return $this->response_headers[strtolower($header)];
		}
		return $this->response_headers;
	}

	/**
	 * Method: getResponseBody()
	 * 	Get the HTTP response body from the request.
	 * 
	 * Access:
	 * 	public
	 * 
	 * Returns:
	 * 	_string_ The response body.
	 * 
	 * See Also:
	 * 	Related - <getResponseHeader()>, <getResponseCode()>
	 * 	Example Usage - http://tarzan-aws.com/docs/examples/httprequest/get_response_body.phps
	 */
	public function getResponseBody()
	{
		return $this->response_body;
	}

	/**
	 * Method: getResponseCode()
	 * 	Get the HTTP response code from the request.
	 * 
	 * Access:
	 * 	public
	 * 
	 * Returns:
	 * 	_string_ The HTTP response code.
	 * 
	 * See Also:
	 * 	Related - <getResponseHeader()>, <getResponseBody()>
	 * 	Example Usage - http://tarzan-aws.com/docs/examples/httprequest/get_response_code.phps
	 */
	public function getResponseCode()
	{
		return $this->response_code;
	}
}

?>
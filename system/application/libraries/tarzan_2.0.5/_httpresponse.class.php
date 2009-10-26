<?php
/**
 * File: TarzanHTTPResponse
 * 	Converts the HTTP responses into organized data chunks.
 *
 * Version:
 * 	2008.11.27
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
// CLASS

/**
 * Class: TarzanHTTPResponse
 * 	Container for all response-related methods.
 */
class TarzanHTTPResponse
{
	/**
	 * Property: header
	 * Stores the HTTP header information.
	 */
	var $header;

	/**
	 * Property: body
	 * Stores the SimpleXML response.
	 */
	var $body;

	/**
	 * Property: status
	 * Stores the HTTP response code.
	 */
	var $status;

	/**
	 * Method: __construct()
	 * 	The constructor
	 * 
	 * Access:
	 * 	public
	 * 
	 * Parameters:
	 * 	header - _array_ (Required) Associative array of HTTP headers (typically returned by <TarzanHTTPRequest::getResponseHeader()>).
	 * 	body - _string_ (Required) XML-formatted response from AWS.
	 * 	status - _integer_ (Optional) HTTP response status code from the request.
	 * 
	 * Returns:
	 * 	_object_ Contains an _array_ 'header' property (HTTP headers as an associative array), a _SimpleXMLElement_ 'body' property, and an _integer_ 'status' code.
 	 * 
	 * See Also:
	 * 	Example Usage - http://tarzan-aws.com/docs/examples/httpresponse/httpresponse.phps
	 */
	public function __construct($header, $body, $status = null)
	{
		$this->header = $header;
		$this->body = $body;
		$this->status = $status;

		if (isset($body))
		{
			// If the response is XML data, parse it.
			if (substr(ltrim($body), 0, 5) == '<?xml')
			{
				$this->body = new SimpleXMLElement($body);
			}
		}

		return $this;
	}

	/**
	 * Method: isOK()
	 * 	Did we receive the status code we expected?
	 * 
	 * Access:
	 * 	public
	 * 
	 * Parameters:
	 * 	codes - _integer|array_ (Optional) The status code(s) to expect. Pass an _integer_ for a single acceptable value, or an _array_ of integers for multiple acceptable values. Defaults to _array_ 200|204.
	 * 
	 * Returns:
	 * 	_boolean_ Whether we received the expected status code or not.
 	 * 
	 * See Also:
	 * 	Example Usage - http://tarzan-aws.com/docs/examples/httpresponse/httpresponse.phps
	 */
	public function isOK($codes = array(200, 201, 204))
	{
		if (is_array($codes))
		{
			return in_array($this->status, $codes);
		}
		else
		{
			return ($this->status == $codes);
		}
	}
}
?>
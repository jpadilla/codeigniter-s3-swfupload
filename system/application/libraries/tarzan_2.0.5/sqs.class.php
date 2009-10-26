<?php
/**
 * File: Amazon SQS
 * 	Amazon Simple Queue Service (http://aws.amazon.com/sqs)
 *
 * Version:
* 	2009.04.29
 * 
 * Copyright:
 * 	2006-2009 LifeNexus Digital, Inc., and contributors.
 * 
 * License:
 * 	Simplified BSD License - http://opensource.org/licenses/bsd-license.php
 * 
 * See Also:
 * 	Tarzan - http://tarzan-aws.com
 * 	Amazon SQS - http://aws.amazon.com/sqs
 */


/*%******************************************************************************************%*/
// CONSTANTS

/**
 * Constant: SQS_DEFAULT_URL
 * 	Specify the default queue URL.
 */
define('SQS_DEFAULT_URL', 'queue.amazonaws.com');


/*%******************************************************************************************%*/
// EXCEPTIONS

/**
 * Exception: SQS_Exception
 * 	Default SQS Exception.
 */
class SQS_Exception extends Exception {}


/*%******************************************************************************************%*/
// MAIN CLASS

/**
 * Class: AmazonSQS
 * 	Container for all Amazon SQS-related methods. Inherits additional methods from TarzanCore.
 * 
 * Extends:
 * 	TarzanCore
 * 
 * Example Usage:
 * (start code)
 * require_once('tarzan.class.php');
 * 
 * // Instantiate a new AmazonSQS object using the settings from the config.inc.php file.
 * $sqs = new AmazonSQS();
 * 
 * // Instantiate a new AmazonSQS object using these specific settings.
 * $sqs = new AmazonSQS($key, $secret_key);
 * (end)
 */
class AmazonSQS extends TarzanCore
{
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
	 * 
	 * Returns:
	 * 	_boolean_ false if no valid values are set, otherwise true.
 	 * 
	 * See Also:
	 * 	Example Usage - http://tarzan-aws.com/docs/examples/sqs/__construct.phps
	 */
	public function __construct($key = null, $secret_key = null)
	{
		$this->api_version = '2008-01-01';
		$this->hostname = SQS_DEFAULT_URL;

		if (!$key && !defined('AWS_KEY'))
		{
			throw new SQS_Exception('No account key was passed into the constructor, nor was it set in the AWS_KEY constant.');
		}

		if (!$secret_key && !defined('AWS_SECRET_KEY'))
		{
			throw new SQS_Exception('No account secret was passed into the constructor, nor was it set in the AWS_SECRET_KEY constant.');
		}

		return parent::__construct($key, $secret_key);
	}


	/*%******************************************************************************************%*/
	// QUEUES

	/**
	 * Method: create_queue()
	 * 	Creates a new queue to store messages in. You must provide a queue name that is unique within the scope of the queues you own. The queue is assigned a queue URL; you must use this URL when performing actions on the queue. When you create a queue, if a queue with the same name already exists, <create_queue()> returns the queue URL with an error indicating that the queue already exists.
	 * 
	 * Access:
	 * 	public
	 * 
	 * Parameters:
	 * 	queue_name - _string_ (Required) The name of the queue to use for this action. The queue name must be unique within the scope of all your queues.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 * 
	 * Returns:
	 * 	<TarzanHTTPResponse> object
 	 * 
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSSimpleQueueService/2008-01-01/SQSDeveloperGuide/Query_QueryCreateQueue.html
	 * 	Example Usage - http://tarzan-aws.com/docs/examples/sqs/create_queue.phps
	 * 	Related - <delete_queue()>, <list_queues()>, <get_queue_attributes()>, <set_queue_attributes()>
	 */
	public function create_queue($queue_name, $returnCurlHandle = null)
	{
		$opt = array();
		$opt['QueueName'] = $queue_name;
		$opt['returnCurlHandle'] = $returnCurlHandle;
		return $this->authenticate('CreateQueue', $opt, $this->hostname);
	}

	/**
	 * Method: delete_queue()
	 * 	Deletes the queue specified by the queue URL. This will delete the queue even if it's not empty.
	 * 
	 * Access:
	 * 	public
	 * 
	 * Parameters:
	 * 	queue_url - _string_ (Required) The URL of the queue to perform the action on.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 * 
	 * Returns:
	 * 	<TarzanHTTPResponse> object
 	 * 
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSSimpleQueueService/2008-01-01/SQSDeveloperGuide/Query_QueryDeleteQueue.html
	 * 	Example Usage - http://tarzan-aws.com/docs/examples/sqs/delete_queue.phps
	 * 	Related - <create_queue()>, <list_queues()>, <get_queue_attributes()>, <set_queue_attributes()>
	 */
	public function delete_queue($queue_url, $returnCurlHandle = null)
	{
		$opt = array();
		$opt['returnCurlHandle'] = $returnCurlHandle;
		return $this->authenticate('DeleteQueue', $opt, $queue_url);
	}

	/**
	 * Method: list_queues()
	 * 	Returns a list of your queues. A maximum 1000 queue URLs are returned. If you specify a value for the optional <queue_name_prefix> parameter, only queues with a name beginning with the specified value are returned.
	 * 
	 * Access:
	 * 	public
	 * 
	 * Parameters:
	 * 	queue_name_prefix - _string_ (Optional) String to use for filtering the list results. Only those queues whose name begins with the specified string are returned.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 * 
	 * Returns:
	 * 	<TarzanHTTPResponse> object
 	 * 
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSSimpleQueueService/2008-01-01/SQSDeveloperGuide/Query_QueryListQueues.html
	 * 	Example Usage - http://tarzan-aws.com/docs/examples/sqs/list_queues.phps
	 * 	Related - <create_queue()>, <delete_queue()>, <get_queue_attributes()>, <set_queue_attributes()>
	 */
	public function list_queues($queue_name_prefix = null, $returnCurlHandle = null)
	{
		$opt = array();
		$opt['returnCurlHandle'] = $returnCurlHandle;
		if ($queue_name_prefix)
		{
			$opt['QueueNamePrefix'] = $queue_name_prefix;
		}
		return $this->authenticate('ListQueues', $opt, $this->hostname);
	}

	/**
	 * Method: get_queue_attributes()
	 * 	Gets one or all attributes of a queue.
	 * 
	 * Access:
	 * 	public
	 * 
	 * Parameters:
	 * 	queue_url - _string_ (Required) The URL of the queue to perform the action on.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 * 
	 * Returns:
	 * 	<TarzanHTTPResponse> object
 	 * 
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSSimpleQueueService/2008-01-01/SQSDeveloperGuide/Query_QueryGetQueueAttributes.html
	 * 	Example Usage - http://tarzan-aws.com/docs/examples/sqs/get_queue_attributes.phps
	 * 	Related - <create_queue()>, <delete_queue()>, <list_queues()>, <set_queue_attributes()>
	 */
	public function get_queue_attributes($queue_url, $returnCurlHandle = null)
	{
		$opt = array();
		$opt['AttributeName'] = 'All';
		$opt['returnCurlHandle'] = $returnCurlHandle;
		return $this->authenticate('GetQueueAttributes', $opt, $queue_url);
	}

	/**
	 * Method: set_queue_attributes()
	 * 	Sets an attribute of a queue. Currently, you can set only the <VisibilityTimeout> attribute for a queue. See Visibility Timeout for more information.
	 * 
	 * Access:
	 * 	public
	 * 
	 * Parameters:
	 * 	queue_url - _string_ (Required) The URL of the queue to perform the action on.
	 * 	opt - _array_ (Required) Associative array of parameters which can have the following keys:
	 * 
	 * Keys for the $opt parameter:
	 * 	VisibilityTimeout - _integer_ (Optional) Must be an integer from 0 to 7200 (2 hours).
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 * 
	 * Returns:
	 * 	<TarzanHTTPResponse> object
 	 * 
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSSimpleQueueService/2008-01-01/SQSDeveloperGuide/Query_QueryGetQueueAttributes.html
	 * 	Example Usage - http://tarzan-aws.com/docs/examples/sqs/set_queue_attributes.phps
	 * 	Related - <create_queue()>, <delete_queue()>, <list_queues()>, <get_queue_attributes()>
	 */
	public function set_queue_attributes($queue_url, $opt = null)
	{
		if (!$opt) $opt = array();

		if (isset($opt['VisibilityTimeout']))
		{
			$opt['Attribute.Name'] = 'VisibilityTimeout';
			$opt['Attribute.Value'] = $opt['VisibilityTimeout'];
			unset($opt['VisibilityTimeout']);
		}
		return $this->authenticate('SetQueueAttributes', $opt, $queue_url);
	}


	/*%******************************************************************************************%*/
	// MESSAGES

	/**
	 * Method: send_message()
	 * 	Delivers a message to the specified queue.
	 * 
	 * Access:
	 * 	public
	 * 
	 * Parameters:
	 * 	queue_url - _string_ (Required) The URL of the queue to perform the action on.
	 * 	message - _string_ (Required) Message size cannot exceed 8 KB. Allowed Unicode characters (according to http://www.w3.org/TR/REC-xml/#charsets): #x9 | #xA | #xD | [#x20-#xD7FF] | [#xE000-#xFFFD] | [#x10000-#x10FFFF].
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 * 
	 * Returns:
	 * 	<TarzanHTTPResponse> object
 	 * 
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSSimpleQueueService/2008-01-01/SQSDeveloperGuide/Query_QuerySendMessage.html
	 * 	Example Usage - http://tarzan-aws.com/docs/examples/sqs/send_message.phps
	 * 	Related - <receive_message()>, <delete_message()>
	 */
	public function send_message($queue_url, $message, $returnCurlHandle = null)
	{
		$opt = array();
		$opt['returnCurlHandle'] = $returnCurlHandle;
		return $this->authenticate('SendMessage', $opt, $queue_url, $message);
	}

	/**
	 * Method: receive_message()
	 * 	Retrieves one or more messages from the specified queue, including the message body and message ID of each message. Messages returned by this action stay in the queue until you delete them. However, once a message is returned to a <receive_message()> request, it is not returned on subsequent <receive_message()> requests for the duration of the <VisibilityTimeout>. If you do not specify a <VisibilityTimeout> in the request, the overall visibility timeout for the queue is used for the returned messages. A default visibility timeout of 30 seconds is set when you create the queue. You can also set the visibility timeout for the queue by using <set_queue_attributes()>. See Visibility Timeout for more information.
	 * 
	 * Access:
	 * 	public
	 * 
	 * Parameters:
	 * 	queue_url - _string_ (Required) The URL of the queue to perform the action on.
	 * 	opt - _array_ (Required) Associative array of parameters which can have the following keys:
	 * 
	 * Keys for the $opt parameter:
	 * 	VisibilityTimeout - _integer_ (Optional) Must be an integer from 0 to 7200 (2 hours).
	 * 	MaxNumberOfMessages - _integer_ (Optional) Maximum number of messages to return, from 1 to 10. Not necessarily all the messages in the queue are returned. If there are fewer messages in the queue than <MaxNumberOfMessages>, the maximum number of messages returned is the current number of messages in the queue. Defaults to 1 message.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 * 
	 * Returns:
	 * 	<TarzanHTTPResponse> object
 	 * 
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSSimpleQueueService/2008-01-01/SQSDeveloperGuide/Query_QueryReceiveMessage.html
	 * 	Example Usage - http://tarzan-aws.com/docs/examples/sqs/receive_message.phps
	 * 	Related - <send_message()>, <delete_message()>
	 */
	public function receive_message($queue_url, $opt = null)
	{
		if (!$opt) $opt = array();
		return $this->authenticate('ReceiveMessage', $opt, $queue_url);
	}

	/**
	 * Method: delete_message()
	 * 	Unconditionally removes the specified message from the specified queue. Even if the message is locked by another reader due to the visibility timeout setting, it is still deleted from the queue.
	 * 
	 * Access:
	 * 	public
	 * 
	 * Parameters:
	 * 	queue_url - _string_ (Required) The URL of the queue to perform the action on.
	 * 	receipt_handle - _string_ (Required) The receipt handle of the message to delete, returned by <receive_message()>.
	 * 	returnCurlHandle - _boolean_ (Optional) A private toggle that will return the CURL handle for the request rather than actually completing the request. This is useful for MultiCURL requests.
	 * 
	 * Returns:
	 * 	<TarzanHTTPResponse> object
 	 * 
	 * See Also:
	 * 	AWS Method - http://docs.amazonwebservices.com/AWSSimpleQueueService/2008-01-01/SQSDeveloperGuide/Query_QueryDeleteMessage.html
	 * 	Example Usage - http://tarzan-aws.com/docs/examples/sqs/delete_message.phps
	 * 	Related - <send_message()>, <receive_message()>
	 */
	public function delete_message($queue_url, $receipt_handle, $returnCurlHandle = null)
	{
		$opt = array();
		$opt['ReceiptHandle'] = $receipt_handle;
		$opt['returnCurlHandle'] = $returnCurlHandle;
		return $this->authenticate('DeleteMessage', $opt, $queue_url);
	}


	/*%******************************************************************************************%*/
	// HELPER/UTILITY METHODS

	/**
	 * Method: get_queue_size()
	 * 	Retrieves the approximate number of messages in the queue.
	 * 
	 * Access:
	 * 	public
	 * 
	 * Parameters:
	 * 	queue_url - _string_ (Required) The URL of the queue to perform the action on.
	 * 
	 * Returns:
	 * 	_integer_ The Approximate number of messages in the queue.
 	 * 
	 * See Also:
	 * 	Related - <get_queue_attributes()>
	 * 	Example Usage - http://tarzan-aws.com/docs/examples/sqs/get_queue_size.phps
	 */
	public function get_queue_size($queue_url)
	{
		$opt = array();
		$opt['AttributeName'] = 'ApproximateNumberOfMessages';
		$response = $this->authenticate('GetQueueAttributes', $opt, $queue_url);

		if ($response->isOK() === false)
		{
			throw new SQS_Exception("Could not get queue size for $queue_url: " . $response->body->Error->Code);
		}

		return (integer) $response->body->GetQueueAttributesResult->Attribute->Value;
	}
}
?>
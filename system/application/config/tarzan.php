<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Constant: AWS_KEY
 * 	Amazon Web Services Key. <http://aws-portal.amazon.com/gp/aws/developer/account/index.html?ie=UTF8&action=access-key>
 */
$config['AWS_ACCESS_KEY_ID'] = '';

/**
 * Constant: AWS_SECRET_KEY
 * 	Amazon Web Services Secret Key. <http://aws-portal.amazon.com/gp/aws/developer/account/index.html?ie=UTF8&action=access-key>
 */
$config['AWS_SECRET_ACCESS_KEY'] = '';

/**
 * Constant: AWS_ACCOUNT_ID
 * 	Amazon Account ID without dashes. Used for identification with Amazon EC2. <http://aws-portal.amazon.com/gp/aws/developer/account/index.html?ie=UTF8&action=edit-aws-profile>
 */
$config['AWS_ACCOUNT_ID'] = '';

/**
 * Constant: AWS_ASSOC_ID
 * 	Amazon Associates ID. Used for crediting referrals via Amazon AAWS. <http://affiliate-program.amazon.com/gp/associates/join/>
 */
$config['AWS_ASSOC_ID'] = '';

/**
 * Constant: AWS_CANONICAL_ID
 * 	Your CanonicalUser ID. Used for setting access control settings in AmazonS3. Must be fetched from the server. Call print_r($s3->get_canonical_user_id()); to view.
 */
$config['AWS_CANONICAL_ID'] = '';

/**
 * Constant: AWS_CANONICAL_NAME
 * 	Your CanonicalUser DisplayName. Used for setting access control settings in AmazonS3. Must be fetched from the server. Call print_r($s3->get_canonical_user_id()); to view.
 */
$config['AWS_CANNONICAL_NAME'] = '';


/*Amazon S3 Bucket name*/
$config['BUCKET'] = '';

/* Where to go after successful upload , including http:// --not relative-- */
$config['SUCCESS_REDIRECT'] = '';

/* where do I find swfupload , including http:// --not relative-- */
$config['SWFRoot'] = '';

?>
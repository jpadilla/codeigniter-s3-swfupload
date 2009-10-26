<?php
/**
 * File: CacheAPC
 * 	APC-based caching class.
 *
 * Version:
 * 	2008.11.30
 * 
 * Copyright:
 * 	2006-2009 LifeNexus Digital, Inc., and contributors.
 * 
 * License:
 * 	Simplified BSD License - http://opensource.org/licenses/bsd-license.php
 * 
 * See Also:
 * 	Tarzan - http://tarzan-aws.com
 * 	APC - http://php.net/apc
 */


/*%******************************************************************************************%*/
// CLASS

/**
 * Class: CacheAPC
 * 	Container for all APC-based cache methods. Inherits additional methods from CacheCore.
 */
class CacheAPC extends CacheCore
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
	 * 	name - _string_ (Required) A name to uniquely identify the cache object.
	 * 	location - _string_ (Required) The location to store the cache object in. This may vary by cache method.
	 * 	expires - _integer_ (Required) The number of seconds until a cache object is considered stale.
	 * 
	 * Returns:
	 * 	_object_ Reference to the cache object.
	 * 
	 * See Also:
	 * 	Example Usage - http://tarzan-aws.com/docs/examples/cachecore/cache.phps
	 */
	public function __construct($name, $location, $expires)
	{
		parent::__construct($name, null, $expires);
		$this->id = $this->name;
	}

	/**
	 * Method: create()
	 * 	Creates a new cache.
	 * 
	 * Access:
	 * 	public
	 * 
	 * Parameters:
	 * 	data - _mixed_ (Required) The data to cache.
	 * 
	 * Returns:
	 * 	_boolean_ Whether the operation was successful.
	 * 
	 * See Also:
	 * 	Example Usage - http://tarzan-aws.com/docs/examples/cachecore/cache.phps
	 */
	public function create($data)
	{
		return apc_add($this->id, serialize($data), $this->expires);
	}

	/**
	 * Method: read()
	 * 	Reads a cache.
	 * 
	 * Access:
	 * 	public
	 * 
	 * Returns:
	 * 	_mixed_ Either the content of the cache object, or _boolean_ false.
	 * 
	 * See Also:
	 * 	Example Usage - http://tarzan-aws.com/docs/examples/cachecore/cache.phps
	 */
	public function read()
	{
		return unserialize(apc_fetch($this->id));
	}

	/**
	 * Method: update()
	 * 	Updates an existing cache.
	 * 
	 * Access:
	 * 	public
	 * 
	 * Parameters:
	 * 	data - _mixed_ (Required) The data to cache.
	 * 
	 * Returns:
	 * 	_boolean_ Whether the operation was successful.
	 * 
	 * See Also:
	 * 	Example Usage - http://tarzan-aws.com/docs/examples/cachecore/cache.phps
	 */
	public function update($data)
	{
		return apc_store($this->id, serialize($data), $this->expires);
	}

	/**
	 * Method: delete()
	 * 	Deletes a cache.
	 * 
	 * Access:
	 * 	public
	 * 
	 * Returns:
	 * 	_boolean_ Whether the operation was successful.
	 */
	public function delete()
	{
		return apc_delete($this->id);
	}

	/**
	 * Method: is_expired()
	 * 	Defined here, but always returns false. APC manages it's own expirations.
	 * 
	 * Access:
	 * 	public
	 * 
	 * Returns:
	 * 	_boolean_ Whether the cache is expired or not.
	 * 
	 * See Also:
	 * 	Example Usage - http://tarzan-aws.com/docs/examples/cachecore/cache.phps
	 */
	public function is_expired()
	{
		return false;
	}
}
?>
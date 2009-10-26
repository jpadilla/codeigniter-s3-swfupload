<?php
/**
 * File: CacheCore
 * 	Core functionality and default settings shared across caching classes.
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
 */


/*%******************************************************************************************%*/
// CLASS

/**
 * Class: CacheCore
 * 	Container for all shared caching methods. This is not intended to be instantiated directly, but is extended by the cache-specific classes.
 */
class CacheCore
{
	/**
	 * Property: name
	 * A name to uniquely identify the cache object by.
	 */
	var $name;

	/**
	 * Property: location
	 * Where to store the cache.
	 */
	var $location;

	/**
	 * Property: expires
	 * The number of seconds before a cache object is considered stale.
	 */
	var $expires;

	/**
	 * Property: id
	 * Used internally to uniquely identify the location + name of the cache object.
	 */
	var $id;

	/**
	 * Property: timestamp
	 * Stores the time when the cache object was created.
	 */
	var $timestamp;


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
		$this->name = $name;
		$this->location = $location;
		$this->expires = $expires;

		return $this;
	}

	/**
	 * Method: create()
	 * 	Creates a new cache. Placeholder method should be defined by the extending class.
	 * 
	 * Access:
	 * 	public
	 * 
	 * Parameters:
	 * 	data - _mixed_ (Required) The data to cache.
	 * 
	 * Returns:
	 * 	void
	 * 
	 * See Also:
	 * 	Example Usage - http://tarzan-aws.com/docs/examples/cachecore/cache.phps
	 */
	public function create($data) { return; }

	/**
	 * Method: read()
	 * 	Reads a cache. Placeholder method should be defined by the extending class.
	 * 
	 * Access:
	 * 	public
	 * 
	 * Returns:
	 * 	void
	 * 
	 * See Also:
	 * 	Example Usage - http://tarzan-aws.com/docs/examples/cachecore/cache.phps
	 */
	public function read() { return; }

	/**
	 * Method: update()
	 * 	Updates an existing cache. Placeholder method should be defined by the extending class.
	 * 
	 * Access:
	 * 	public
	 * 
	 * Parameters:
	 * 	data - _mixed_ (Required) The data to cache.
	 * 
	 * Returns:
	 * 	void
	 * 
	 * See Also:
	 * 	Example Usage - http://tarzan-aws.com/docs/examples/cachecore/cache.phps
	 */
	public function update($data) { return; }

	/**
	 * Method: delete()
	 * 	Deletes a cache. Placeholder method should be defined by the extending class.
	 * 
	 * Access:
	 * 	public
	 * 
	 * Returns:
	 * 	void
	 */
	public function delete() { return; }

	/**
	 * Method: timestamp()
	 * 	Retrieves the timestamp of the cache. Placeholder method should be defined by the extending class.
	 * 
	 * Access:
	 * 	public
	 * 
	 * Returns:
	 * 	void
	 */
	public function timestamp() { return; }

	/**
	 * Method: reset()
	 * 	Resets the freshness of the cache. Placeholder method should be defined by the extending class.
	 * 
	 * Access:
	 * 	public
	 * 
	 * Returns:
	 * 	void
	 * 
	 * See Also:
	 * 	Example Usage - http://tarzan-aws.com/docs/examples/cachecore/cache.phps
	 */
	public function reset() { return; }

	/**
	 * Method: is_expired()
	 * 	Checks whether the cache object is expired or not. Placeholder method should be defined by the extending class.
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
	public function is_expired() { return; }

	/**
	 * Method: response_manager()
	 * 	Provides a simple, straightforward cache-logic mechanism. Useful for non-complex response caches.
	 * 
	 * Access:
	 * 	public
	 * 
	 * Parameters:
	 * 	callback - _string_ (Required) The name of the function to fire when we need to fetch new data to cache.
	 * 	params - _array_ (Optional) Parameters to pass into the callback function, as an array.
	 * 
	 * Returns:
	 * 	_array_ The cached data being requested.
	 * 
	 * See Also:
	 * 	Example Usage - http://tarzan-aws.com/docs/examples/cachecore/response_manager.phps
	 */
	public function response_manager($callback, $params = null)
	{
		if ($data = $this->read())
		{
			if ($this->is_expired())
			{
				if ($data = call_user_func_array($callback, $params))
				{
					$this->update($data);
				}
				else
				{
					$this->reset();
				}
			}
		}
		else
		{
			if ($data = call_user_func_array($callback, $params))
			{
				$this->create($data);
			}
		}

		return $data;
	}
}
?>

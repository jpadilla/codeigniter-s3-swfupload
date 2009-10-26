<?php
/**
 * File: CacheFile
 * 	File-based caching class.
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
 * Class: CacheFile
 * 	Container for all file-based cache methods. Inherits additional methods from CacheCore.
 */
class CacheFile extends CacheCore
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
		parent::__construct($name, $location, $expires);
		$this->id = $this->location . '/' . $this->name . '.cache';
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
		if (file_exists($this->id))
		{
			return false;
		}
		elseif (file_exists($this->location) && is_writeable($this->location))
		{
			return (bool) file_put_contents($this->id, serialize($data));
		}

		return false;
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
		if (file_exists($this->id) && is_readable($this->id))
		{
			return unserialize(file_get_contents($this->id));
		}

		return false;
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
		if (file_exists($this->id) && is_writeable($this->id))
		{
			return (bool) file_put_contents($this->id, serialize($data));
		}

		return false;
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
		if (file_exists($this->id))
		{
			return unlink($this->id);
		}

		return false;
	}

	/**
	 * Method: timestamp()
	 * 	Retrieves the timestamp of the cache.
	 * 
	 * Access:
	 * 	public
	 * 
	 * Returns:
	 * 	_mixed_ Either the Unix timestamp of the cache creation, or _boolean_ false.
	 */
	public function timestamp()
	{
		if (file_exists($this->id))
		{
			$this->timestamp = filemtime($this->id);
			return $this->timestamp;
		}

		return false;
	}

	/**
	 * Method: reset()
	 * 	Resets the freshness of the cache.
	 * 
	 * Access:
	 * 	public
	 * 
	 * Returns:
	 * 	_boolean_ Whether the operation was successful.
	 * 
	 * See Also:
	 * 	Example Usage - http://tarzan-aws.com/docs/examples/cachecore/cache.phps
	 */
	public function reset()
	{
		if (file_exists($this->id))
		{
			return touch($this->id);
		}

		return false;
	}

	/**
	 * Method: is_expired()
	 * 	Checks whether the cache object is expired or not.
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
		if ($this->timestamp() + $this->expires < time())
		{
			return true;
		}

		return false;
	}
}
?>
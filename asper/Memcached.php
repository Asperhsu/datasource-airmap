<?php
namespace Asper;

class Memcached extends \Memcached {
	
	private $prefix;

	public function __construct($prefix)
	{
		$this->prefix = $prefix;
		parent::__construct();
	}

	public function add($key, $var, $expire = null)
	{
		parent::add($this->prefix . $key, $var, $flag, $expire);
	}

	public function get($key)
	{
		return parent::get($this->prefix . $key);
	}

	public function set($key, $value, $expire = null)
	{
		return parent::set($this->prefix . $key, $value, $expire);
	}
}
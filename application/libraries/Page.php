<?php

use Philo\Blade\Blade as CoreBlade;

class Page extends CoreBlade
{
	
	public function __construct()
	{
		$this->views = __DIR__."/../views";
		$this->cache = __DIR__."/../cache";

		parent::__construct($this->views, $this->cache);
	}

	public function tampil($template, array $values = [])
	{
		return parent::view()->make($template, $values)->render();
	}

	public function sebar($key, $value)
	{
		$this->view()->share($key, $value);
	}
	
}

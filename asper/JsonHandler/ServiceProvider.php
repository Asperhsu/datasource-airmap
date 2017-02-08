<?php
namespace Asper\JsonHandler;

class ServiceProvider {
	protected $services = [];
	protected $keywords = [];

	public function __construct(){
	}

	public function register($services){
		if( !is_array($services) ){
			return $this->registerKeyword($services);
		}
		foreach($services as $service){			
			$this->registerKeyword($service);
		}
		return $this;
	}

	protected function checkRegister($className){
		if( !class_exists($className) ){ 
			return false; 
		}
		if( !is_subclass_of($className, Handleable::class) ){ 
			return false; 
		}
		return true;
	}

	protected function registerKeyword($className){
		if( !$this->checkRegister($className) ){
			return false;
		}
		$class = new $className();
		$keywords = $class->register();
		if( !is_array($keywords) ){
			$keywords = [$keywords];
		}
		foreach($keywords as $keyword){
			if( !isset($this->keywords) ){
				$this->keywords[$keyword] = [$class];
				continue;
			}
			$this->keywords[$keyword][] = $class;
		}
		return $this;
	}

	public function trigger($keyword, Array $params=[]){
		if( !isset($this->keywords[$keyword]) ){
			return false;
		}

		$classes = $this->keywords[$keyword];

		foreach($classes as $class){
			$class->trigger($params);
		}
		return true;
	}
}
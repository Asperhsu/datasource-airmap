<?php
namespace Asper\JsonHandler;

interface Handleable {
	
	public function register();
	public function trigger(Array $params = []);

}
<?php

	namespace Core;

	class Router
	{
		protected $routes=[];
		protected $params=[];

		public function add($route, $params=[])
		{
			$route=preg_replace('/\//','\\/',$route);

			$route=preg_replace('/\{([a-z-]+)\}/', '(?P<\1>[a-z-]+)', $route);

			$route=preg_replace('/\{([a-z-]+):([^\}]+)\}/', '(?P<\1>\2)', $route);

			$route='/^'.$route.'$/';

			$this->routes[$route]=$params;
		}

		public function match($url)
		{
			foreach($this->routes as $route=>$params)
			{
				if(preg_match($route, $url, $matches))
				{
					if(!empty($params) && !empty($matches))
					{
						$this->params=$params;

						foreach($matches as $key=>$value)
						{
							$this->params[$key]=$value;
						}
					}
					elseif(!empty($params))
					{ 
						$this->params=$params;
					}
					else
					{
						foreach($matches as $key=>$value)
						{
							$this->params[$key]=$value;
						}
					}
					return true;
				}
			}

			return false;
		}

		public function dispatch($url)
		{
			if($this->match($url))
			{
				$controller=$this->params['controller'];
				$controller=$this->convertToStudlyCaps($controller);
				$controller='App\Controllers\\'.$controller;

				if(class_exists($controller))
				{
					$controller_obj=new $controller($this->params);

					$action=$this->params['action'];
					$action=$this->convertToPascalCase($action);

					if(is_callable([$controller_obj, $action]))
					{
						$controller_obj->$action();
					}
					else
					{
						throw new \Exception("$action action not found in $controller class",404);
					}
				}
				else
				{
					throw new \Exception("$controller controller not found",404);
				}
			}
			else
			{
				throw new \Exception('Route not matched',404);
			}
		}

		protected function convertToStudlyCaps($url)
		{
			return str_replace('-','',ucwords($url));
		}

		protected function convertToPascalCase($url)
		{
			return lcfirst($this->convertToStudlyCaps($url));
		}

		public function getRoutes()
		{
			return $this->routes;
		}

		public function getParams()
		{
			return $this->params;
		}
	}


?>
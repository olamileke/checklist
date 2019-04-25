<?php

	require_once dirname(__DIR__).'/vendor/Twig/lib/Twig/Autoloader.php';
	Twig_Autoloader::register();


	spl_autoload_register(function($class) {

		$file=dirname(__DIR__).'/'.str_replace('\\', '/', $class).'.php';

		if(is_readable($file))
		{
			require $file;
		}
	});

	$url=$_SERVER['QUERY_STRING'];

	session_start();

	set_error_handler('\Core\Error::errorHandler');
	set_exception_handler('\Core\Error::exceptionHandler');

	$router=new Core\Router();

	$router->add('',['controller'=>'Home', 'action'=>'index']);

	$router->add('{controller}/{action}');

	$router->add('login',['controller'=>'Login', 'action'=>'index']);

	$router->add('{controller}/{action}/{token:[\da-z]+}');

	$router->add('project/{id:[\d]+}/{name:[a-z-]+}',['controller'=>'Project','action'=>'view']);

	$router->add('profile',['controller'=>'Profile','action'=>'index']);

	$router->add('logout', ['controller'=>'login','action'=>'logout']);

	$router->add('settings', ['controller'=>'settings', 'action'=>'index']);

	$router->add('admin', ['controller' => 'admin', 'action'=>'index']);

	$router->dispatch($url);


?>
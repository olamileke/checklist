<?php

	namespace Core;

	use \App\Auth;

	abstract class Controller
	{
		protected $route_params=[];

		public function __construct($params)
		{
			$this->route_params=$params;
		}

		public function __call($action, $args)
		{
			$method=$action.'Action';

			if(method_exists($this, $method))
			{
				$this->before();
				call_user_func_array([$this, $method], $args);
			}
		}

		public function redirect($url)
		{
			header("Location:http://".$_SERVER['HTTP_HOST'].'/'.$url);
		}

		public function checkLogin()
		{
			$user=Auth::getUser();

			if($user)
			{
				return true;
			}
			else
			{
				$this->redirect('login');
				Auth::getRequestedPage();
			}
		}	

		public function checkAdmin()
		{
			$user=Auth::getUser();

			if(!$user->is_admin)
			{
				$this->redirect('');
			}
		}
	}


?>
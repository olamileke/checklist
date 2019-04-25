<?php

	namespace Core;

	use \App\Flash;

	use \App\Auth;

	use \App\Date;

	class View
	{
		public static function renderTemplate($template, $args=[])
		{
			echo static::returnTemplate($template, $args);
		}

		public static function returnTemplate($template, $args=[])
		{
			$twig=null;

			if($twig === null)
			{
				$loader=new \Twig_Loader_FileSystem('../App/Views');
				$twig=new \Twig_Environment($loader);
				$twig->addGlobal('server', $_SERVER['HTTP_HOST']);
				$twig->addGlobal('flash_notifs', Flash::getMessages());
				$twig->addGlobal('day', Date::getDay());
				$twig->addGlobal('hour', Date::getHour());

				$user=Auth::getUser();

				if($user)
				{
					$letter=strtoupper(substr($user->lname,0,1));
					$twig->addGlobal('letter',$letter);
				}
			}

			return $twig->render($template,$args);
		}
	}
	





?>
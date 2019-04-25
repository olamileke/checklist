<?php

	namespace App\Controllers;

	use \Core\View;

	use \App\Auth;

	use \App\Flash;

	use \App\Models\Users;

	class Login extends \Core\Controller
	{
		public function indexAction()
		{
			View::renderTemplate('Login/index.html');
		}

		public function createAction()
		{
			$user=Users::authenticate($_POST['email'], $_POST['password']);

			$rememberme=isset($_POST['rememberme']);


			if($user)
			{
				Auth::login($user, $rememberme);

				$this->redirect(Auth::rememberRequestedPage());
			}
			else
			{
				Flash::addMessages('Username or Password incorrect', Flash::DANGER);

				View::renderTemplate('Login/index.html', ['user'=>$_POST]);
			}
		}

		public function logoutAction()
		{
			Auth::logout();

			$this->redirect('');
		}
	}






?>
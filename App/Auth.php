<?php

	namespace App;

	use App\Models\Users;

	use App\Models\rememberedLogin;

	class Auth
	{
		public static function login($user, $rememberme)
		{
			$_SESSION['user_id']=$user->id;

			if($rememberme)
			{
				if($user->rememberLogin())
				{
					setcookie('rememberme', $user->token, $user->time_expiry, '/');
				}
			}
		}

		public static function logout()
		{
			$_SESSION=[];

			if(ini_get("session.use_cookies")) 
			{
				$params=session_get_cookie_params();
				setcookie(session_name(), '', time() - 42000,
					 $params['path'],$params['domain'],$params['secure'],$params['httponly']);
			}

			session_destroy();

			static::rememberLogout();

			static::activateTokenLogout();
		}


		public static function getRequestedPage()
		{
			$_SESSION['return_to']=$_SERVER['REQUEST_URI'];
		}


		public static function rememberRequestedPage()
		{
			if(isset($_SESSION['return_to']))
			{
				$url=$_SESSION['return_to'];

				unset($_SESSION['return_to']);
				return $url;
			}
			else
			{
				return '';
			}
		}

		public static function getUser()
		{
			if(isset($_SESSION['user_id']))
			{
				$user=Users::findUserByID($_SESSION['user_id']);

				if($user)
				{
					return $user;
				}
			}
			else
			{
				$user=static::rememberLogin();

				if($user)
				{
					return $user;
				}
				else
				{
					return static::activateTokenLogin();
				}
			}
		}


		// FUNCTIONS TO IMPLEMENT THE REMEMBER ME FUNCTIONALITY

		public static function rememberLogin()
		{
			if(isset($_COOKIE['rememberme']))
			{
				$cookie=$_COOKIE['rememberme'];

				$rememberedLogin=rememberedLogin::confirmRememberToken($cookie);

				if($rememberedLogin)
				{
					$user=$rememberedLogin->getUser();

					if($user)
					{
						static::login($user, false);
						return $user;
					}
				}			
			}
		}


		public static function rememberLogout()
		{
			if(isset($_COOKIE['rememberme']))
			{
				$cookie=$_COOKIE['rememberme'];

				$rememberedLogin=rememberedLogin::confirmRememberToken($cookie);

				if($rememberedLogin)
				{
					$rememberedLogin->deleteCookie();
				}
			}
		}


		// LOGGING THE USER IN IMMEDIATELY WHEN THE USER ACTIVATES THEIR ACCOUNT

		public static function activateTokenLogin()
		{
			if(isset($_COOKIE['activate_token']))
			{
				$token=$_COOKIE['activate_token'];

				$user=Users::findUserByActivateToken($token);

				if($user)
				{
					static::login($user, false);

					return $user;
				}
			}
		}


		public static function activateTokenLogout()
		{
			if(isset($_COOKIE['activate_token']))
			{
				Users::deleteActivateToken();
			}
		}

	}







?>
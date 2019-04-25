<?php

	namespace App\Controllers;

	use \App\Models\Users;

	use \Core\View;

	use \App\Auth;

	use \App\Token;

	use \App\Mail;

	use \App\Flash;

	class Account extends \Core\Controller
	{
		public function activateAction()
		{
			$token=$this->route_params['token'];

			if(Users::checkToken($token))
			{				
				$user=Auth::getUser();

				if($user)
				{
					if(is_null($user->image))
					{
						$user->image='Images/Users/anon.png';
					}

					View::renderTemplate('Home/loggedin.html', ['user'=>$user, 'activate'=> true]);
				}
			}
		}

		public function forgotPasswordAction()
		{
			View::renderTemplate('Account/forgotpassword.html');
		}

		public function sendPasswordMailAction()
		{
			$email=$_POST['email'];

			$user=Users::findUserByEmail($email);

			if($user)
			{
				$token=new Token();

				$tokenz=$token->getToken();

				$hashed_token=$token->getHash();

				$user->setPasswordResetToken($hashed_token);
				
				$msgHtml=View::returnTemplate('Account/changepasswordmail.html', ['user'=>$user, 'token'=>$tokenz]);

				$msgText=View::returnTemplate('Account/changepasswordmail.txt');

				if(Mail::send($user->email, $user->fname, 'Password Reset', $msgHtml, $msgText))
				{
					Flash::addMessages('Check your email to complete the Process', 'success');

					View::renderTemplate('Account/forgotpassword.html');
				}
			}
			else
			{
				Flash::addMessages('Email Incorrect. Please check and try again.', 'danger');

				View::renderTemplate('Account/forgotpassword.html', ['email'=>$email]);
			}
		}

		public function passwordResetAction()
		{
			$tokenz=$this->route_params['token'];

			$token=new Token($tokenz);

			$user=Users::confirm_reset_token($token->getHash());

			if($user)
			{
				View::renderTemplate('Account/changepassword.html', ['id'=>$user->id]);
			}
		}

		public function changePassword()
		{

			$id=(int)$_POST['id'];

			$password=$_POST['password'];

			if(Users::changePassword($id, $password))
			{
				Flash::addMessages('Password changed successfully', 'success');
			}
			else
			{
				Flash::addMessages('Problem changing Password', 'danger');
			}

			View::renderTemplate('Account/changepassword.html', ['user'=>23]);

		}
	}






?>
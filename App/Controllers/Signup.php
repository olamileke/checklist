<?php

	namespace App\Controllers;

	use App\Flash;

	use App\Mail;

	use \App\Token;

	use \App\Models\Users;

	use \App\Models\Setting;

	use \Core\View;

	class Signup extends \Core\Controller
	{
		public function createAction()
		{
			$user=new Users($_POST);

			if($user->save())
			{
				$details=Users::returnLastUser();

				$tokenz=$user->getToken();

				$time_expiry=time() + (30 * 24 * 60 * 60);

				setcookie('activate_token', $tokenz, $time_expiry, '/');

				Setting::create($user->getLastID());

				$img='Images/Signup/hello.jpg';

				$msghtml=View::returnTemplate('Signup/activationhtml.html', ['name'=> $details['lname'],'img'=>$img, 'token_val'=>$tokenz]);

				$msgplain=View::returnTemplate('Signup/activationtext.txt', ['name'=> $details['lname']]);

				if(Mail::send($details['email'], $details['lname'], 'Welcome!',$msghtml, $msgplain, $img, 'emailimg'))
				{
					Flash::addMessages('Signup completed successfully', 'success');

					View::renderTemplate('Home/index1.html');
				}
			}
			else
			{
				Flash::addMessages('Signup Unsuccessful. Please try again', 'danger');

				View::renderTemplate('Home/index1.html', ['user'=>$_POST]);
			}
		}
	}



?>
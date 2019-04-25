<?php

	namespace App\Controllers;

	use \Core\View;

	use \App\Auth;

	use \App\Models\UImages;

	use \App\Models\Setting;

	class Settings extends \App\Authenticated
	{
		public function indexAction()
		{
			$user=Auth::getUser();

			$user->image=UImages::getImage($user->id)[0];

			$user->settings=Setting::getSettings($user->id);

			View::renderTemplate('Settings/index.html', ['user'=>$user]);
		}


		public function changeAction()
		{
			if(!empty($_POST))
			{
				$num=(int)$_POST['num'];

				$user=Auth::getUser();

				switch($num)
				{
					case 1:
						Setting::changeField($user->id, 'values_in_percentage');
						break;

					case 2:
						Setting::changeField($user->id, 'get_weekly_roundup');
						break;

					case 3:
						Setting::changeField($user->id, 'get_reminder');
						break;
				}
			}
		}

		public function checkDailyReminderAction()
		{
			$user=Auth::getUser();

			echo Setting::getField($user->id, 'get_reminder');
		}

		public function checkPercentAction()
		{
			$user=Auth::getUser();

			$user->settings=Setting::getSettings($user->id);

			if($user->settings->values_in_percentage)
			{
				echo 'true';
			}
			else
			{
				echo 'false';
			}
		}
	}




?>
<?php

	namespace App\Controllers;

	use \Core\View;

	use \App\Auth;

	use \App\Models\Projects;

	use \App\Models\Images;

	use \App\Models\Tasks;

	use \App\Models\Users;

	use \App\Models\UImages;

	use \App\Models\Setting;

	class Home extends \Core\Controller
	{
		public function indexAction()
		{
			$user=Auth::getUser();

			Auth::rememberLogin();


			if(!$user)
			{
				View::renderTemplate('Home/index1.html');
				exit();
			}

			$user->settings=Setting::getSettings($user->id);

			$projects=Projects::getProjects($user->id, 0, 6);

			$count=0;

			foreach($projects as $project)
			{
				$project->image=Images::getImage($project->id)[0];
				$project->numtasks=Tasks::getCountTasks($project->id)[0];
				$project->numcompletedtasks=Tasks::getCountCompletedTasks($project->id)[0];
				$project->url=str_replace(' ','-',strtolower($project->name));
				$project->percent=round(($project->numcompletedtasks/$project->numtasks) * 100). '%';
				$count++;
			}


			$user->route=strtolower($user->fname).'-'.strtolower($user->lname);
			$user->image=UImages::getImage($user->id)[0];
			$user->tprojects=Projects::getNumAllProjects($user->id)[0];
			$user->projects=$count;

			if(is_null($user->image))
			{
				$user->image='Images/Users/anon.png';
			}

			View::renderTemplate('Home/loggedin.html', ['projects'=>$projects, 'user'=>$user]);	
		}
	}


?>
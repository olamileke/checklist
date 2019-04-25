<?php

	namespace App\Controllers;

	use Core\View;

	use App\Auth;

	use \App\Models\Projects;

	use \App\Models\UImages;

	use \App\Models\Tasks;

	class Profile extends \App\Authenticated
	{
		public function indexAction()
		{
			$user=Auth::getUser();

			$user->route=strtolower($user->fname).'-'.strtolower($user->lname);
			$user->image=UImages::getImage($user->id)[0];
			$user->numprojects=Projects::getNumUserProjects($user->id)[0];
			$user->numcprojects=Projects::getNumUserCProjects($user->id)[0];

			$user->projects=Projects::getAllProjects($user->id);

			foreach($user->projects as $project)
			{
				$user->numtasks=$user->numtasks + Tasks::getCountTasks($project->id)[0];
				$user->numctasks=$user->numctasks + Tasks::getCountCompletedTasks($project->id)[0];
			}

			if(!$user->image)
			{
				$user->image='Images/Users/anon.png';
				$user->hasImage='false';
			}
			else
			{
				$user->hasImage='true';
			}

			
			View::renderTemplate('Profile/view.html', ['user'=>$user]);
			
		}


		public function uploadImage()
		{
			if(isset($_FILES['image']))
			{
				$imagename=$_FILES['image']['name'];
				$imagesize=$_FILES['image']['size'];
				$imagetemp=$_FILES['image']['tmp_name'];
				$imageext=pathinfo($imagename, PATHINFO_EXTENSION);
				$imagepath='Images/Users/'.$imagename;

				$extarray=array('jpg','JPG','png','PNG','JPEG','jpeg');

				if(in_array($imageext,$extarray))
				{
					move_uploaded_file($imagetemp, $imagepath);
					echo $imagepath;
				}
			}
		}

		public function saveImage()
		{
			if(!empty($_POST))
			{
				$user=Auth::getUser();
				$imgpath=$_POST['imgpath'];

				UImages::changeImage($user->id, $imgpath, $_POST['hasImage']);	

				echo $imgpath;
			}
		}
	}



?>
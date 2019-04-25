<?php

	namespace App\Controllers;

	use App\Models\Tasks;

	use App\Models\Setting;

	use \App\Auth;

	use App\Models\Projects;

	class Task
	{
		public function updateTasks()
		{
			if(!empty($_POST))
			{
				$id=(int)$_POST['id'];

				$user=Auth::getUser();

				$user->settings=Setting::getSettings($user->id);

				$idArray=$_POST['id_array'];
				$statusArray=$_POST['status_array'];
				$count=0;

				for($i=0; $i < sizeOf($idArray); $i++)
				{
					Tasks::updateTasks($statusArray[$i], $idArray[$i]);

					if($statusArray[$i] == 1)
					{
						$count++;
					}
				}

				if(!$user->settings->values_in_percentage)
				{
					echo $count;
				}
				else
				{
					$project=Projects::getProject($id);

					$project->numtasks=Tasks::getCountTasks($project->id)[0];
					$project->numcompletedtasks=Tasks::getCountCompletedTasks($project->id)[0];
					$project->percent=round(($project->numcompletedtasks/$project->numtasks) * 100). '%';

					echo $project->percent;
				}
			}
		}
	}





?>
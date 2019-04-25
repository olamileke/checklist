<?php

	namespace App\Controllers;

	use \App\Auth;

	use \App\Mail;

	use \App\Models\Projects;

	use \App\Models\Images;

	use \App\Models\Tasks;

	use \App\Models\Setting;

	use \App\Models\UImages;

	use \Core\View;

	class Project extends \App\Authenticated
	{
		public function newAction()
		{

			// RENDERING THE NEW PROJECT VIEW

			$user=Auth::getUser();

			$user->route=strtolower($user->fname).'-'.strtolower($user->lname);
			$user->image=UImages::getImage($user->id)[0];

			if(is_null($user->image))
			{
				$user->image='Images/Users/anon.png';
			}

			View::renderTemplate('Project/new.html', ['user'=>$user]);	
		}

		public function newProjectAction()
		{
			// ADDING A NEW PROJECT

			if(!empty($_POST))
			{
				$project=new Projects($_POST);

				$user=Auth::getUser();

				if($project->save($user->id))
				{
					$id=$project->returnLastID()[0];

					foreach($project->tasks as $task)
					{
						Tasks::save($task, $id);
					}

					Images::save($project->imagepath, $id);
				}
			}
		}

		public function addImageAction()
		{
			// UPLOADING THE IMAGE WHEN IT IS SELECTED IN THE NEW PROJECT FORM

			if(isset($_FILES['image']))
			{
				$imagename=$_FILES['image']['name'];
				$imagetemp=$_FILES['image']['tmp_name'];
				$imagesize=$_FILES['image']['size'];
				$imageext=pathinfo($imagename, PATHINFO_EXTENSION);
				$imagepath='Images/Projects/'.$imagename;

				$arrayext=array('jpg','JPG','jpeg','JPEG','png','PNG');

				if(in_array($imageext, $arrayext))
				{
					move_uploaded_file($imagetemp, $imagepath);

					echo $imagename;
				}
			}
		}


		public function viewAction()
		{

			// RENDERING THE INDIVIDUAL PROJECT VIEW

			$user=Auth::getUser();

			$user->route=strtolower($user->fname).'-'.strtolower($user->lname);
			$user->image=UImages::getImage($user->id)[0];

			$user->settings=Setting::getSettings($user->id);


			if(is_null($user->image))
			{
				$user->image='Images/Users/anon.png';
			}


			$id=$this->route_params['id'];
			$name=str_replace('-', ' ',$this->route_params['name']);

			$project=Projects::getProject($id);


			if($user->id == $project->user_id)
			{
				$project->image=Images::getImage($project->id)[0];
				$project->numtasks=Tasks::getCountTasks($project->id)[0];
				$project->numcompletedtasks=Tasks::getCountCompletedTasks($project->id)[0];
				$project->percent=round(($project->numcompletedtasks/$project->numtasks) * 100). '%';
				$project->url=str_replace(' ','-',strtolower($project->name));
				$project->tasks=Tasks::getTasks($project->id);

				View::renderTemplate('Project/view.html', ['project'=>$project, 'user'=>$user]);
			}
			else
			{
				$this->redirect('');
			}
		}

		public function complete()
		{
			if(!empty($_POST))
			{
				$id=(int)$_POST['id'];

				Projects::complete($id);
			}
		}

		public function checkStatusAction()
		{
			if(!empty($_POST))
			{
				$id=(int)$_POST['id'];
				$statusarray=Projects::returnStatus($id);

				if($statusarray[0] == 1 || $statusarray[1] == 1)
				{
					echo 'true';
				}
				else
				{
					echo 'false';
				}
			}
		}

		public function fetchMoreAction()
		{
			if(!empty($_POST))
			{
				$start=(int)$_POST['start'];
				$num=(int)$_POST['num'];

				$user=Auth::getUser();

				$user->settings=Setting::getSettings($user->id);

				$projects=Projects::getProjects($user->id, $start, $num);

				$tasktemplate='';

				foreach($projects as $project)
				{
					$project->image=Images::getImage($project->id)[0];
					$project->numtasks=Tasks::getCountTasks($project->id)[0];
					$project->numcompletedtasks=Tasks::getCountCompletedTasks($project->id)[0];
					$project->percent=round(($project->numcompletedtasks/$project->numtasks) * 100). '%';
					$project->url=str_replace(' ','-',strtolower($project->name));
					$tasktemplate.=View::returnTemplate('Project/task.html', ['project'=>$project, 'settings'=>$user->settings]); 
				}

				echo $tasktemplate;
			}
		}


		public function timerAction()
		{
			if(!empty($_POST))
			{
				$id=(int)$_POST['id'];

				$timearray=Projects::getTime($id);

				echo $this->obtainTime($timearray);
			}
				
		}

		public function obtainTime($timearray)
		{
			$day=$timearray[0];

			$time=$timearray[1];

			$timearray=explode(' ',$time);

			if($timearray[1] == 'PM')
			{
				$hourarray=explode(':', $timearray[0]);

				$hour=$hourarray[0] + 12;
				$time=str_replace($hour - 12, $hour, $timearray[0]);
			}
			else
			{
				$time=$timearray[0];
			}

			echo $day.' '.$time;
		}

		public function checkMoreAction()
		{
			if(!empty($_POST))
			{
				$currentnum=(int)$_POST['currentnum'];

				$total=Projects::getNumAllProjects(Auth::getUser()->id)[0];

				if($total > $currentnum)
				{
					echo 'true';
				}
			}
		}

		public function checkDateAction()
		{
			if(!empty($_POST))
			{
				$date=$_POST['date'];

				$datearray1=explode(' ',$date);

				echo $datearray1[2];
			}

		}

		public function sendCompleteMailAction()
		{
			if(!empty($_POST))
			{
				$id=(int)$_POST['id'];

				$date=$_POST['date'];

				$time=$_POST['time'];

				$user=Auth::getUser();

				$project=Projects::getProject($id);

				$project->image=Images::getImage($project->id)[0];

				$project->numcompletedtasks=Tasks::getCountCompletedTasks($project->id)[0];

				$msgHtml=View::returnTemplate('Project/completemail.html', ['name'=>$user->lname, 'projectname'=>$project->name, 'no_of_tasks'=>$project->numcompletedtasks, 'date'=>$date, 'time'=>$time ]);

				$msgTxt=View::returnTemplate('Project/completemail.txt');

				Mail::send($user->email, $user->lname, 'Project Completed', $msgHtml, $msgTxt, $project->image, 'projectimg');

			}
		}

		public function sendExpiredMailAction()
		{
			if(!empty($_POST))
			{
				$id=(int)$_POST['id'];

				$this->setExpired($id);

				$date=$_POST['date'];

				$time=$_POST['time'];

				$user=Auth::getUser();

				$project=Projects::getProject($id);

				$project->image=Images::getImage($project->id)[0];

				$project->numcompletedtasks=Tasks::getCountCompletedTasks($project->id)[0];

				$project->numtasks=Tasks::getCountTasks($project->id)[0];

				$project->uncompletedtasks=$project->numtasks - $project->numcompletedtasks;

				$msgHtml= View::returnTemplate('Project/expiredmail.html', ['name'=>$user->lname, 'projectname'=>$project->name, 'no_c_tasks'=>$project->numcompletedtasks, 'no_u_tasks'=>$project->uncompletedtasks, 'date'=>$date, 'time'=>$time]);

				$msgTxt=View::returnTemplate('Project/expiredmail.txt');

				Mail::send($user->email, $user->lname, 'Project Expired', $msgHtml, $msgTxt, $project->image, 'projectimg');

			}
		}

		public function setExpired($id)
		{
			Projects::setExpired($id);
		}


		public function sendDayReminderAction($id)
		{			
			$user=Auth::getUser();

			$project=Projects::getProject($id);

			$project->image=Images::getImage($project->id)[0];

			$project->numcompletedtasks=Tasks::getCountCompletedTasks($project->id)[0];

			$project->numtasks=Tasks::getCountTasks($project->id)[0];

			$project->uncompletedtasks=$project->numtasks - $project->numcompletedtasks;

			$msgHtml= View::returnTemplate('Project/remindermail.html', ['name'=>$user->lname, 'projectname'=>$project->name, 'no_c_tasks'=>$project->numcompletedtasks, 'no_u_tasks'=>$project->uncompletedtasks]);

			$msgTxt=View::returnTemplate('Project/remindermail.txt');

			Mail::send($user->email, $user->lname, 'Reminder', $msgHtml, $msgTxt, $project->image, 'projectimg');
		
		}

		public function checkTimesAction()
		{
			$user=Auth::getUser();

			$timearrays=Projects::getAvailableProjects($user->id);

			$count=0;

			foreach($timearrays as $timearray)
			{
				$date=$this->getProperDateValue($timearray);
				$time=$this->getProperTimeValue($timearray);

				$time4rmepoch=$this->getTimeEpoch($date, $time);
				$oneday=24 * 60 * 60;

				if(($time4rmepoch - strtotime(date('Y-m-d h:i:s'))) == $oneday)
				{
					$this->sendDayReminderAction($timearray['id']);
				}
			}			
		}

		public function getTimeEpoch($date, $time)
		{
			return strtotime($date.' '.$time);
		}

		public function getProperDateValue($timearray)
		{
			$newarray=explode(',',$timearray[0]);

			$year=$newarray[1];

			$montharray=explode(' ', $newarray[0]);

			$day=$montharray[0];

			if($day < 10)
			{
				$day='0'.$day;
			}

			$month=$this->getMonthNumber($montharray[1]);

			return $year.'-'.$month.'-'.$day;

		}

		public function getMonthNumber($m)
		{
			$months=['January','February', 'March', 'April', 'May', 'June', 'July', 'August','September','October','November','December'];

			$count=0;

			foreach($months as $month)
			{
				$count++;
				if($month == $m)
				{
					if($count < 10)
					{
						$count='0'.$count;
					}

					return $count;
					break;
				}
			}
		}


		public function getProperTimeValue($timearray)
		{
			$newarray=explode(' ', $timearray[1]);

			return $newarray[0];
		}

		public function sendRoundUpAction()
		{
			$users=\App\Models\Users::getAllUsers();

			foreach($users as $user)
			{
				$user->settings=Setting::getSettings($user->id);
				
				$count=0;

				if($user->settings->get_weekly_roundup)
				{
					$user->projects=Projects::getAvailableProjectz($user->id);

					foreach($user->projects as $project)
					{
						$project->numcompletedtasks=Tasks::getCountCompletedTasks($project->id)[0];

						$project->numtasks=Tasks::getCountTasks($project->id)[0];

						$project->uncompletedtasks=$project->numtasks - $project->numcompletedtasks;

						$count++;
					}

					$msgHtml=View::returnTemplate('Project/roundup.html',['name'=>$user->lname, 'projects'=>$user->projects]);

					$msgTxt=View::returnTemplate('Project/roundup.txt');

					if($count > 0)
					{					
						Mail::send($user->email, $user->lname, 'Weekly Roundup', $msgHtml, $msgTxt);
					}
					
					$count=0;
				}

			}
		}

	}





?>
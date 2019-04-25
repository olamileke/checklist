<?php

	namespace App;

	class Authenticated extends \Core\Controller
	{
		protected function before()
		{
			$this->checkLogin();
		}
	}



?>
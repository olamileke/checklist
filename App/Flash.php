<?php

	namespace App;

	class Flash 
	{
		const SUCCESS='success';
		const WARNING='warning';
		const INFO='info';
		const DANGER='danger';

		public static function addMessages($message, $type)
		{
			if(!isset($_SESSION['flash_messages']))
			{
				$_SESSION['flash_messages']=[];

				$_SESSION['flash_messages'][]=['message'=>$message,'type'=>$type];
			}
		}

		public static function getMessages()
		{
			if(isset($_SESSION['flash_messages']))
			{
				$messages=$_SESSION['flash_messages'];

				unset($_SESSION['flash_messages']);

				return $messages;
			}
		}
	}


?>
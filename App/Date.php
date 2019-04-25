<?php

	namespace App;

	class Date
	{
		public static function getDay()
		{
			$date=date('Y-m-d');

			$day=strtolower(date("D", strtotime($date)));

			return $day;
		}

		public static function getHour()
		{
			return date('H');
		}
	}




?>
"use strict"

$(document).ready(function() {

	var $host=location.hostname;

	var $get_daily_reminder;

	function checkIfDailyReminder()
	{
		return $.ajax('http://'+$host+'/settings/checkdailyreminder', {
			type:'POST',
			dataType:'text',
			success:function(data) {
				$get_daily_reminder=data;
			}
		})
	}

	checkIfDailyReminder();

	function getTimes()
	{
		return $.ajax('http://'+$host+'/project/checktimes', {
			type:'POST',
			dataType:"text"
		})
	}

	setInterval(function() {

		if($get_daily_reminder == 1)
		{
			getTimes();
		}
	},1000)
})
"use strict"

$(document).ready(function() {

	var $host=location.hostname;

	var $checkbox=$('.checkbox');

	var $updatebtn=$('button.update');

	var $completebtn=$('button.complete');

	var $tasksid=$('.task_id');

	var $project_id=$('.id').text();

	var $checkInitArray=[];

	var $checkNewArray=[];

	var $checkIDArray=[];

	var $id=$('.id').text();

	var $timer=$('.timer');

	var $picinfo=$('.picinfo');

	var $infodiv=$('.info');


	// MANIPULATING THE DOM UPON PROJECT COMPLETION/EXPIRY

	function changeDOM()
	{
		$timer.remove();	
		$checkbox.off();
		$picinfo.css('marginTop','-=22px');
	}


	initArray($checkInitArray);

	$tasksid.each(function() {
		$checkIDArray.push($(this).text());
	})

	// AJAX CALL TO FETCH THE TIME

	var $i=1;

	$.ajax('http://'+$host+'/project/timer', {
		type:'POST',
		dataType:'text',
		data:{
			'id':$id
		},
		success:function(data) {

			var $date=new Date(data).getTime();
			var $now=new Date().getTime();

			if($date >= $now)
			{
				var $interval=setInterval(function() {

				var $now=new Date().getTime();

				if($date >= $now)
				{
					var $difference=$date - $now;

					var $days=Math.floor($difference/(1000 * 60 * 60 * 24));

					var $hours=Math.floor(($difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));

					var $minutes=Math.floor(($difference % (1000 * 60 * 60)) / (1000 * 60));

					var $seconds=Math.floor(($difference % (1000 * 60)) / 1000);

					$timer.text($days + 'd ' + $hours + 'h ' + $minutes +'m ' + $seconds + 's');
				}
				else
				{
					setExpired($id);
					clearInterval($interval);
					changeDOM();
					$completebtn.replaceWith("<p class='expired'>Expired</p>");
				}

			},1000)
		}
		}
	})


	// FUNCTION TO SET PROJECT TO EXPIRED

	function setExpired($id)
	{
		return $.ajax('http://'+$host+'/project/sendexpiredmail', {
			type:'POST',
			dataType:'text',
			data:{
				'id':$id,
				'date':getDate(),
				'time':getTime()
			}
		})
	}


	// FUNCTION TO SET THE REQUIRED VALUES IN THE ARRAY

	function initArray($array)
	{
			$checkbox.each(function() {

			var $this=$(this);

			var $parent=$this.parent();
			var $lcheck=$parent.find('.l_check');
			var $scheck=$parent.find('.s_check');

			if($lcheck.hasClass('visible') && $scheck.hasClass('visible'))
			{
				$array.push(1);
			}
			else
			{
				$array.push(0);
			}
		})
	}

	// COMPARING THE VALUES IN THE ARRAY

	function compare()
	{
		var $count=0;

		for(var $i=0; $i < $checkInitArray.length; $i++)
		{
			if($checkInitArray[$i] != $checkNewArray[$i])
			{
				$count++;
			}
		}

		if($count > 0)
		{
			return false;
		}

		return true
	}


	// TOGGLING THE CHECKBOXES'S STATE

	function checkBoxEvent($this)
	{
		$checkNewArray=[];

		var $parent=$this.parent();
		var $lcheck=$parent.find('.l_check');
		var $scheck=$parent.find('.s_check');

		if($lcheck.hasClass('visible') && $scheck.hasClass('visible'))
		{
			$lcheck.removeClass('visible').css('display','none');
			$scheck.removeClass('visible').css('display','none');
		}
		else
		{
			$lcheck.addClass('visible').css('display','block');
			$scheck.addClass('visible').css('display','block');
		}

		initArray($checkNewArray);

		if(!compare())
		{
			$updatebtn.show().addClass('active');	
		}
		else
		{
			$updatebtn.hide().removeClass('active');
		}
	}


	$.ajax('http://'+$host+'/project/checkstatus', {
		type:'POST',
		dataType:'text',
		data:{
			'id':$project_id
		},
		error:function(data) {
			alert(data);
		},
		success:function(data) {
			if(data !== 'true')
			{
				$checkbox.click(function() {
					checkBoxEvent($(this));
				})
			}
		}
	})


	
	var $total_tasks=$('.picinfo').find('p').text().split('/')[1];
	var $p=$('.picinfo').find('p');


	// UPDATING THE TASKS

	$updatebtn.click(function() {

		showLoader();

		var $this=$(this);

		$.ajax('http://'+$host+'/task/updatetasks',{
			type:'POST',
			dataType:'text',
			data:{
				'id':$id,
				'id_array':$checkIDArray,
				'status_array':$checkNewArray
			},
			success:function(data) {
				setTimeout(function() {
					
					removeLoader('Project Updated Successfully');

					if($is_percent == 'true')
					{
						$p.text(data);
					}
					else
					{
						$p.text(data+'/'+$total_tasks);
					}
					
					$checkInitArray=$checkNewArray;
					$this.hide().removeClass('active');
				},3200)
			}
		})
	})


	var $is_percent;

	function checkPercent()
	{
		return $.ajax('http://'+$host+'/settings/checkpercent', {
			type:'POST',
			dataType:'text',
			success:function(data) {
				$is_percent=data;
			}
		})
	}

	checkPercent();


	// MARKING TASKS AS COMPLETED

	function complete($id)
	{
		return $.ajax('http://'+$host+'/project/complete', {
			type:'POST',
			dataType:'text',
			data:{
				'id':$id
			},
			success:function(data) {
				setTimeout(function() {
					removeLoader('Project completed Successfully');
					sendcompletemail();
					changeDOM();
				},3000);
			}
		})
	}

	// FUNCTION TO SEND MAIL UPON PROJECT COMPLETION

	function sendcompletemail()
	{
		return $.ajax('http://'+$host+'/project/sendcompletemail', {
			type:'POST',
			dataType:'text',
			data:{
				'id':$id,
				'date':getDate(),
				'time':getTime()
			},
			error:function(data) {
				alert(data);
			},
			success:function(data) {
				
			}
		})
	}

	// Function to get the current date

	function getDate()
	{
		var $monthArray=['January', 'February', 'March', 'April', 'May', 'June',' July', 'August', 'September', 'October', 'November', 'December'];

		var $today=new Date();

		var $d=$today.getDate();

		var $m=$today.getMonth();

		var $y=$today.getFullYear();

		var $date= $d + ' ' + $monthArray[$m] + ' ' + $y;

		return $date;
	}

	// Function to get the current time

	function getTime()
	{
		var $today=new Date();

		var $h=$today.getHours();

		var $m=$today.getMinutes();

		var $s=$today.getSeconds();

		var $suffix;

		if($h > 12)
		{
			$h=$h - 12;
			$suffix='pm';
		}
		else
		{
			$suffix='am';
		}

		if($m.length == 1)
		{
			$m='0'+$m;
		}

		var $time=$h + ':' + $m + ' ' + $suffix;

		return $time;
	}


	$completebtn.click(function() {
			
		var $this=$(this);

		if($updatebtn.hasClass('active'))
		{
			removeLoader('Please save task changes');
		}
		else
		{
			var $count=0;

			for(var $i=0; $i < $checkInitArray.length; $i++)
			{
				if($checkInitArray[$i] == 0)
				{
					removeLoader('Please complete all tasks first');
					$count++;
					break;
				}
			}

			if($count == 0)
			{
				showLoader();
				complete($project_id);	

				setTimeout(function() {
					$this.replaceWith('<p class="status">Completed!</p>');
					changeDOM();
				},3500)			
			}
		}
	})
})
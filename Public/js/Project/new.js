"use strict"

$(document).ready(function() {

	var $host=location.hostname;

	var $select=$('select');

	var $taskcontainer=$('.taskcontainer');

	var $tasks=$taskcontainer.find('input');

	var $body=$('body');

	// ADDING MORE TASKS TO THE TASK CONTAINER

	$select.change(function() {
		
		var $num=parseInt($select.val());

		var $task="<div class='task'><input type='text' name='task'><p></p></div>";
		
		for(var $i=1; $i <= $num; $i++)
		{
			$taskcontainer.append($task);
		}

		setTimeout(function() {			
			$tasks=$taskcontainer.find('input');
		},2000)

	})

	// UPLOADING THE IMAGE WHEN IT IS SELECTED

	var $image=$('.image');

	var $imgdiv=$('.proj_img');

	var $imagepath;

	function uploadImage()
	{
		var $form_data=new FormData();
		
		$form_data.append('image', $image.prop('files')[0]);

		return $.ajax('http://'+$host+'/project/addimage', {
			type:'POST',
			dataType:'text',
			contentType:false,
			processData:false,
			data:$form_data,
			error:function(data) {
				alert(data);
			},
			success:function(data) {
				$imagepath='Images/Projects/'+data;
				$imgdiv.css({'background':'url(http://'+$host+'/'+$imagepath+')', 'background-size':'cover','border':'2px solid rgba(0, 0, 0, 0.07)'}).text("");
			}
		})
	}

	setInterval(function() {
		if($image.prop('files').length > 0)
		{		
			uploadImage();
		}

	},1000)


	// FORM VALIDATION

	var $form=$('form');

	var $elements=$('.elements');

	var $proj_name=$('.text1');

	var $inputs=$('.due input');

	var $datepicker=$('.datepicker');

	var $timepicker=$('.timepicker');

	function validate()
	{
		var $count=0;

		$elements.each(function() {
			var $this=$(this);

			if(!$this.val())
			{
				if($this.attr('type') == 'file')
				{
					$imgdiv.css('border','1px solid red');
				}
				else if($this.hasClass('text1'))
				{
					$this.css('borderBottom','1px solid red');
				}
				else
				{					
					$this.css('border','1px solid red');	
				}

				$count++;
			}
		})

		if($count > 0)
		{
			return false;
		}
		return true;
	}

	// VALIDATING THE PROJECT NAME FIELD

	$proj_name.keyup(function() {
		var $this=$(this);

		if($this.val())
		{
			$this.css('borderBottom','1px inset rgba(0,0,0,0.65)');
		}
		else
		{
			$this.css('borderBottom','1px solid red');
		}
	})

	// VALIDATING THE TASKS

	function validateTasks()
	{
		var $count=0;

		$tasks.each(function() {
			var $this=$(this);

			if($this.val())
			{
				$count++;
			}
		})

		if($count < 3)
		{
			return false;
		}

		return true;
	}


	// VALIDATING THE DATE AND TIME PICKERS

	function check($elems)
	{
		$elems.each(function() {
			var $this=$(this);

			if($this.val())
			{
				$this.css('border','1px solid rgba(0,0,0,0.4)');
			}
		})
	}

	setInterval(function() {
		check($inputs);
	},1000)



	function checkDate()
	{
		return $.ajax('http://'+$host+'/project/checkdate',{
			type:'POST',
			dataType:'text',
			data:{
				'date':$('.datepicker').val()
			},
			error:function(data){
				alert(data);
			},
			success:function(data) {
				alert(data);
			}
		})
	}

	// SUBMITTING THE PROJECT DATA TO THE 

	var $submitbtn=$form.find('input[type="submit"]');

	$form.submit(function(e) {

		e.preventDefault();

		if(validate() && validateTasks())
		{
			showLoader();

			var $tasksarray=[];

			$tasks.each(function() {

				var $this=$(this);

				if($this.val())
				{
					$tasksarray.push($this.val());
				}
			})

			setTimeout(function() {
					$.ajax('http://'+$host+'/project/newproject', {
						type:'POST',
						dataType:'text',
						data:{
							'projectname':$proj_name.val(),
							'tasks':$tasksarray,
							'imagepath':$imagepath,
							'date_due':$datepicker.val(),
							'time_due':$timepicker.val()
						},
						success:function(data) {
							setTimeout(function() {								
								removeLoader('Post successfully created');
								$submitbtn.remove();
							}, 500)
						}
				})
			}, 4000)
		}
		else
		{
			$notifbell.addClass('error');
			$notifmsg.fadeIn(2000).addClass('error').text('Please fill in all the fields');
		}
	})
})
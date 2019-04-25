"use strict"

$(document).ready(function() {

	var $host=location.hostname;

	var $imgBody=$('.imgBody');

	var $input=$imgBody.find('input');

	var $hasImage=$('.imgBody p').text();

	// SETTING THE PERCENTAGE FOR THE PROJECT AND THE TASKS

	var $proj_percent=$('.proj_percent');

	var $task_percent=$('.task_percent');

	var $numprojects=parseInt($('span:nth-child(1)').text());
	var $numcprojects=parseInt($('span:nth-child(2)').text());

	var $numtasks=parseInt($('span:nth-child(3)').text());
	var $numctasks=parseInt($('span:nth-child(4)').text());

	var $percent=($numcprojects/$numprojects) * 70;

	var $percenttask=($numctasks/$numtasks) * 70;

	$proj_percent.css('width', $percent+'%');

	$task_percent.css('width', $percenttask+'%');


	// UPLOADING THE PROFILE IMAGE

	var $imagepath;

	var $imgdiv=$('.img');

	var $button=$('.picture');

	function uploadImage($currentpic)
	{
		if($input.prop('files').length > 0)
		{
			var formData=new FormData();

			formData.append('image', $input.prop('files')[0]);

			return $.ajax('http://'+$host+'/profile/uploadimage', {
				type:'POST',
				processData:false,
				contentType:false,
				data:formData,
				success:function(data) {

					if(data != $currentpic)
					{
						$imagepath=data;
						$imgdiv.css({'background':"url(http://"+$host+"/"+$imagepath+")",'background-size':'cover'});
						$button.show();
					}		
				}
			})
		}
	}

	
	var $interval=setInterval(function() {
		uploadImage('');
	},1000);


	($hasImage == 'true') ? $hasImage=true : $hasImage=false;

	var $profilediv=$('.prof_img');

	$button.click(function() {

		var $this=$(this);

		showLoader();

		$.ajax('http://'+$host+'/profile/saveimage', {
			type:'POST',
			dataType:'text',
			data:{
				'imgpath':$imagepath,
				'hasImage':$hasImage
			},
			success:function(data) {
				setTimeout(function() {
					removeLoader('Picture Updated Successfully');
					$profilediv.css({'background':"url(http://"+$host+"/"+$imagepath+")",'background-size':'cover'});
					$this.hide();

					clearInterval($interval);

					 $interval=setInterval(function() {
						uploadImage($imagepath);
					},1000);
					
				},3000)
			}
		})
	})


})
"use strict"

$(document).ready(function() {

	var $host=location.hostname;


	// DETERMINING IF MORE TASKS EXIST TO BE DISPLAYED

	function checkIfMore($button)
	{
		return $.ajax('http://'+$host+'/project/checkmore', {
			type:'POST',
			dataType:'text',
			data:{
				'currentnum':$('.task').length
			},
 			success:function(data) {
 				if(data == 'true')
 				{
 					$button.show();
 				}
 			}
		})
	}


	// ANIMATING THE LOADER AND FETCHING MORE POSTS

	var $loader=$('div.loader');

	var $taskscontainer=$('.taskscontainer');

	var $morebtn=$('.taskscontainer + button');

	var $icons=$('.loader').find('div');

	function fetchMore()
	{
		return $.ajax('http://'+$host+'/project/fetchmore', {
			type:'POST',
			dataType:"text",
			data:{
				'start':$('.task').length,
				'num':6
			},
			success:function(data) {
				$taskscontainer.append(data);

				var $over=$('.over');
				percentageBar($over);
			}
		})
	}

	// SETTING THE PERCENTAGE BAR OF THE NEWLY INSERTED POST

	function percentageBar($elem)
	{
		setTimeout(function() {

			$elem.each(function() {
				var $this=$(this);
				var $params=$this.text().split('/');
				var $percentval=($params[0]/$params[1]) * 100;
				var $parent=$this.parent().parent();
				var $percent=$parent.find('.percent');

				if($params.length == 2)
				{
					$percent.css('width',$percentval+'%');
				}
				else
				{					
					$percent.css('width', $this.text());	
				}
			},1000)

		})
	}

	$morebtn.click(function() {

		var $icon1=$loader.find('div:nth-child(1)');
		var $icon2=$loader.find('div:nth-child(2)');
		var $icon3=$loader.find('div:nth-child(3)');

		var $iconarray=[];

		$iconarray.push($icon1);
		$iconarray.push($icon2);
		$iconarray.push($icon3);

		var $i=0;	

		setInterval(function() {

			$iconarray[$i].addClass('active');
			$iconarray[$i].siblings().removeClass('active');

			$i++;

			if($i > 2)
			{
				$i=0;
			}

		},600)

		var $this=$(this);
		$this.hide();
		$icons.show();

		setTimeout(function() {

			fetchMore();
			$icons.hide();

			setTimeout(function() {
				checkIfMore($this);
				
				var $tasks=$('.task');

				$tasks.each(function() {
					setTimer($(this));
				})
			},300)
		},3000)
	})

})
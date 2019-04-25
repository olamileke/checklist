
	var $host=location.hostname;

	var $tasks=$('.task');

	var $i=1;

	function setTimer($this)
	{
		var $id=$this.find('span').text();
		var $timer=$this.find('.timer');

		if($timer)
		{
			$.ajax('http://'+$host+'/project/timer', {
				type:'POST',
				dataType:'text',
				data:{
					'id':$id
				},
				success:function(data) {
					
					var $now=new Date().getTime();
					var $date=new Date(data).getTime();

					if($date > $now)
					{
						var $i=setInterval(function() {
							var $now=new Date().getTime();
							var $date=new Date(data).getTime();

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
								$timer.replaceWith('<p class="expired">Expired</p>');
								setExpired($id);
								clearInterval($i);
							}
						},1000)
					}
				}
			})
		}
	}


	$tasks.each(function() {

		setTimer($(this));
})


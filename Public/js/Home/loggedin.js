"use strict"

$(document).ready(function() {

	var $sidebar=$('#sidebar');

	var $host=location.hostname;


	// CLOSING THE ACCOUNT ACTIVATION DISPLAY

	var $msgcontainer=$('.msgcontainer');

	var $closebtn=$msgcontainer.find('span');

	$closebtn.click(function() {
		$msgcontainer.remove();
	})


	// SETTING THE ACTIVE LINK


	var $li=$sidebar.find('li');

	var $route=document.URL;

	var $routearray=document.URL.split($host);

	var $routes=["/","/project/new",'/settings',"/profile", "/admin"];

	var $links=["Home","New Project",'Settings',"Profile", "Admin"];

	for(var $i=0; $i < $routes.length ; $i++)
	{
		if($routearray[1] == $routes[$i])
		{
			$li.each(function() {
				var $this=$(this);

				if($this.text().trim() == $links[$i])
				{
					$this.addClass('active');

					var $p=$this.find('p');

					var $a=$this.find('a');

					var $width=$a.css('width');

					$p.css('width',$width);
				}
			})
		}
	}

	// ANIMATION ON MENU HOVER 

	$li.hover(function() {

		if(!$(this).hasClass('active'))
		{
			var $this=$(this);

			var $p=$this.find('p');

			var $a=$this.find('a');

			var $width=$a.css('width');

			$p.css('width',$width);
		}
	}, function() {
		if(!$(this).hasClass('active'))
		{
			var $this=$(this);

			var $p=$this.find('p');
			
			$p.css('width','0px');
		}
	})


	// SETTING THE COMPLETED PERCENTAGE BAR

	var $over=$('.over');

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

	percentageBar($over);

})
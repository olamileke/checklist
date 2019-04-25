"use strict"

$(document).ready(function() {

	var $container=$('.container');
	var $container2=$('.container2');
	var $mainInfo=$('.mainInfo');

	// TOGGLING THE MOTIVATIONAL QUOTES

	setTimeout(function() {
		$container.fadeOut(2000);
		$container2.fadeIn(2000);
	},7000)

	var $quotes=['The way to get started is to quit talking and begin doing', "Infinite striving to be the best is man's duty; It is its own reward. Everything else is in God's hands",
	"It's not that I am smart, It's just that I stay with problems longer", "Writing crystallizes thought and produces action", "Your talent determines what you can do, Your motivation determines how much you are willing to do. Your attitude determines how well you do it."
	, "The purpose of life is not to be happy- but to matter, to be productive, to be useful, to have it make some difference that you lived at all"];


	var $quoters=['Walt Disney', 'Mahatma Gandhi', 'Albert Einstein', 'Paul J. Meyer', 'Lou Holtz', 'Leo Rosten'];

	var $count=0;

	var $altcount=0;

	var $intervalduration;

	if($altcount == 0)
	{
		$intervalduration=19000;
	}
	else
	{
		$intervalduration=10000;
	}

	function changeQuote()
	{
		var $quote;
		var $quoter;

		if($count > 5)
		{
			$count=0;
		}

		if($altcount == 0)
		{
			$quote=$('.quote');
			$quoter=$('.quoter');
		}
		else
		{
			$quote=$('.newquote');
			$quoter=$('.newquoter');
		}

		$quote.fadeOut(1000);
		$quoter.fadeOut(1000);

		setTimeout(function() {
			$quote.remove();
			$quoter.remove();
			$mainInfo.append("<p class='newquote'>"+$quotes[$count]+"</p>");
			$mainInfo.append("<p class='newquoter'> - "+$quoters[$count]+"</p>");
		}, 1000)

		setTimeout(function() {
			$quote=$('.newquote');
			$quoter=$('.newquoter');

			$quote.fadeIn(2000);
			$quoter.fadeIn(2000);

			$count++;
			$altcount++;
		}, 2000)
	}

	setInterval(function() {
		changeQuote();
	}, $intervalduration);

	// SHOW/HIDE PASSWORD FUNCTIONALITY

	var $checktoggle=$('input[type="checkbox"]');
	var $passwordinput=$('input[type="password"]');

	$checktoggle.click(function() {

		if($passwordinput.attr('type') == 'password')
		{
			$passwordinput.attr('type','text');
		}
		else
		{
			$passwordinput.attr('type','password');
		}
	})

	var $alert=$('.alert');

	setTimeout(function() {
		$alert.fadeIn(2000);
	}, 3000)


})
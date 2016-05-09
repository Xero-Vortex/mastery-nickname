<?php

function outputHeadCssAndJs()
{
	?>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<meta name="description" content="Get a nickname based on your 5 most played League of Legends champions!">
	<link type="text/css" rel="stylesheet" href="css/bootstrap/bootstrap.css">
	<link type="text/css" rel="stylesheet" href="css/bootstrap/bootstrap-theme.css">
	<link type="text/css" rel="stylesheet" href="css/mastery_style.css">
	<meta property="og:type"          content="website" />
	<meta property="og:title"         content="Mastery Nickname for League of Legends" />
	<meta property="og:description"   content="A nickname based on your 5 most played League of Legends champions!" />

	<?php
}


function outputRegionDropdown()
{
	?>
	<select name="region">
		<option value="na">North America</value>
		<option value="br">Brazil</value>
		<option value="eune">Europe Nordic & East</value>
		<option value="euw">Europe West</value>
		<option value="krR">Republic of Korea</value>
		<option value="lan">Latin America North</value>
		<option value="las">Latin America South</value>
		<option value="oce">Oceania</value>
		<option value="tr">Turkey</value>
		<option value="ru">Russia</value>
		<option value="jp">Japan</value>
	</select>
	<?php
}


function outputHeader()
{
	?>
	<div id="main-header">
		<h1 id="main-title"><a href="http://xero.tech/mastery/">Mastery Nickname</a></h1>
		<h2 id="main-title-subtitle">For League of Legends</h2>
		<h3 id="main-subtitle">A nickname based on your 5 most played League of Legends champions!</h3>
	</div>
	<?php
}


function outputFooter()
{
	?>
	<div id="site-footer">
		<div class="footer-text">Mastery Nickname was created for the Riot Games API Challenge 2016 that ran from April 22, 2016 through May 09, 2016. It uses the Riot Games League of Legends API.<br /><br />
		Mastery Nickname isn't endorsed by Riot Games and doesn't reflect the views or opinions of Riot Games or anyone officially involved in producing or managing League of Legends. League of Legends and Riot Games are trademarks or registered trademarks of Riot Games, Inc. League of Legends &copy; Riot Games, Inc.</div>
	</div>
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-61968631-3', 'auto');
	  ga('send', 'pageview');

	</script>
	<?php
}


function outputMasteryTweetLink($message)
{
	echo '<span id="tweet-link" class="mastery-social-container"><a href="http://twitter.com/home/?status=' . urlencode($message) . '" target="_blank" title="Tweet this"><img src="./img/Twitter_logo_blue_16.png" />Tweet</a></span>';
}

function outputMasteryFacebookLink($url)
{
	echo '<span id="facebook-share-link" class="mastery-social-container"><a href="https://www.facebook.com/sharer/sharer.php?u=' . urlencode($url) . '&t=' . urlencode('Mastery Nickname Title') . '" target="_blank" title="Share this on Facebook"><img src="./img/fb-f-logo-15.png" id="fb-icon" />Share</a></span>';
}

?>

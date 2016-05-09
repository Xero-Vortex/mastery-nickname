<?php
require_once('inc/page_components.php');
require_once('inc/league_helper.php');
require_once('inc/mastery_helper.php');

$displayResults = false;

// check if we have values to process
if(!empty($_GET['name']) && !empty($_GET['region']))
{
	$errorEncountered = false;
	$errorMsg = '';
	$output = '';
	$masterySummary = array();

	$region = strtolower($_GET['region']);

	// get the summoner ID from the given summoner name and region
	$summonerID = getSummonerIdFromName($region, $_GET['name']);

	if($summonerID == null)
	{
		$errorEncountered = true;
		$errorMsg = 'Could not find summoner for that region.';
	}
	else
	{
		// get the mastery data for the given player
		$masteryInfo = getMasteryInfoForPlayer(getPlatformIDFromRegion($region), $summonerID, 5);

		// continue if we got mastery data
		if($masteryInfo != null)
		{
			/*
			Sample Mastery data:
			[0] => Array
	        (
	            [playerId] => 999999
	            [championId] => 412
	            [championLevel] => 5
	            [championPoints] => 48525
	            [lastPlayTime] => 1459911292000
	            [championPointsSinceLastLevel] => 26925
	            [championPointsUntilNextLevel] => 0
	            [chestGranted] => 1
	            [highestGrade] => S-
	        )
			*/

			$totalMasteryPoints = 0;

			// add up all the mastery points for the champions we got
			for($i = 0; $i < count($masteryInfo); $i++)
			{
				$totalMasteryPoints += $masteryInfo[$i]['championPoints'];
			}

			// get champion info to match with the mastery data
			$champInfo = getBasicChampionInfo($region);

			// collect the summary info we're going to use to
			for($i = 0; $i < count($masteryInfo); $i++)
			{
				$champID = $masteryInfo[$i]['championId'];
				$champName = $champInfo[$champID]['name'];
				$masterySummary[$champID] =
					[
						'name' => $champName
						, 'key' => $champInfo[$champID]['key']
						, 'championId' => $champID
						, 'championPoints' => $masteryInfo[$i]['championPoints']
						, 'pointPercent' => $masteryInfo[$i]['championPoints'] / $totalMasteryPoints
					];
			}

			// sort the champion mastery data from highest mastery percent to lowest (then champion ID lowest to highest)
			usort($masterySummary, 'masteryDataCompareHighToLow');

			// finally get the nickname from the mastery info
			$nickname = getChampionMasteryNickname($masterySummary);

			// build the image url
			$fullChampImgUrl = '';
			$champImgUrlParams = '';
			$champIndex = 1;

			// get all the parameters we need to create the composite image
			foreach($masterySummary as $key => $info)
			{
				// if this isn't the first parameter set, add the ampersand
				if($champImgUrlParams !== '')
				{
					$champImgUrlParams .= '&';
				}
				$champImgUrlParams .= 'c' . $champIndex . '=' . $info['championId'] . '&p' . $champIndex . '=' . intval(round($info['pointPercent'] * 100));

				$champIndex++;
			}

			$fullChampImgUrl = 'http://xero.tech/mastery/champion_mastery_image.php?' . $champImgUrlParams;

			// if we got a nickname, we'll display it!
			if(!empty($nickname) && $nickname != '')
			{
				$displayResults = true;
			}
		}
		// else we didn't get mastery data
		else
		{
			$errorEncountered = true;
			$errorMsg = 'Could not get mastery data for that summoner.';
		}
	}

	if($errorEncountered === true)
	{
		error_log('Error: ' . $errorMsg . '
		$_GET: ' . print_r($_GET, true));
	}
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Mastery Nickname for League of Legends</title>
	<?php outputHeadCssAndJs(); ?>
	<?php
	// special meta tags for Facebook if we generated a nickname and champion mastery image
	if($displayResults === true)
	{
		echo '<meta property="og:image"         content="' . $fullChampImgUrl . '" />';
		echo '<meta property="og:url"           content="http://xero.tech/mastery/?name=' . $_GET['name'] . '&region=' . $_GET['region'] . '" />';
	}
	else {
		echo '<meta property="og:url"           content="http://xero.tech/mastery/" />';
	}
	?>
</head>
<body>
	<div id="site-content">
		<?php outputHeader(); ?>
		<div id="main-content">
			<div id="main-content-wrapper" class="content-container">
				<div id="mastery-wrapper">
					<?php
					// if we hit an error, display it
					if($errorEncountered === true)
					{
					?>
						<div id="error-msg-wrapper">
							<div id="error-msg" class="bg-danger"><?php echo $errorMsg; ?></div>
						</div>
					<?php
					}
					?>
					<div id="mastery-form-wrapper">
						<?php
						if($displayResults == true)
						{
						?>
							<div id="mastery-nickname-label">
							<?php
								echo htmlspecialchars($_GET['name']);
							?>'s nickname is
							</div>
							<div id="mastery-nickname">
							<?php
								echo $nickname;
							?>
							</div>
							<div id="mastery-nickname-share-container">
							<?php
								outputMasteryTweetLink('My mastery nickname for League of Legends is ' . $nickname . '! Check it: xero.tech/mastery?name=' . $_GET['name'] . '&region=' . $_GET['region'] . ' #lolmasterynickname');
								outputMasteryFacebookLink('http://xero.tech/mastery?name=' . $_GET['name'] . '&region=' . $_GET['region']);
							?>
							</div>

							<div id="mastery-summary">
								<?php
								if($champImgUrlParams !== '')
								{
									echo '<img src="./champion_mastery_image.php?' . $champImgUrlParams . '" />';
								}

								$masteryChampsString = '';
								// output all the top most played champions for the given summoner name to the screen
								foreach($masterySummary as $key => $info)
								{
									/*
									$info contains these fields:
									'name' => $champName
									, 'key' => $champInfo[$champID]['key']
									, 'championId' => $champID
									, 'championPoints' => $masteryInfo[$i]['championPoints']
									, 'pointPercent' => $masteryInfo[$i]['championPoints'] / $totalMasteryPoints
									*/
									// if we've already added a champion name to the string, add a separator at the end
									if($masteryChampsString !== '')
									{
										$masteryChampsString .= '/';
									}

									$masteryChampsString .= $info['name'];
								}

								if($masteryChampsString !== '')
								{
									echo '<div id="mastery-champions">' . $masteryChampsString . '</div><br />';
								}
								?>
							</div>
						<?php
						} // end if $displayResults == true
						?>
						<form id="summoner-form" method="get">
							<div>
								<div id="mastery-summoner-name">Summoner Name:</div>
								<input type="text" name="name" placeholder="Summoner Name"></input><br />
								<div id="mastery-region">Region:</div>
								<?php outputRegionDropdown(); ?>
								<br />
								<input class="btn btn-default core-button" id="btnGetNickname" type="submit" value="Get Nickname!" />
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<?php
		outputFooter();
		?>
	</div>
</body>
</html>
<?php

?>

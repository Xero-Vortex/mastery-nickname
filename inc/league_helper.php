<?php
// this file should define the API keys we need
require_once('inc/config.php');
require_once('inc/general_helper.php');


// define some constants
// regions
define('REGION_NA', 'na');
define('REGION_BR', 'br');
define('REGION_EUNE', 'eune');
define('REGION_EUW', 'euw');
define('REGION_KR', 'kr');
define('REGION_LAN', 'lan');
define('REGION_LAS', 'las');
define('REGION_OCE', 'oce');
define('REGION_TR', 'tr');
define('REGION_RU', 'ru');
define('REGION_JPN', 'jp');

// platform IDs - similar to regions but not the same (some endpoints use these instead of regions)
define('PLATFORM_ID_NA1', 'NA1');
define('PLATFORM_ID_BR1', 'BR1');
define('PLATFORM_ID_EUN1', 'EUN1');
define('PLATFORM_ID_EUW1', 'EUW1');
define('PLATFORM_ID_KR', 'KR');
define('PLATFORM_ID_LA1', 'LA1');
define('PLATFORM_ID_LA2', 'LA2');
define('PLATFORM_ID_OC1', 'OC1');
define('PLATFORM_ID_TR1', 'TR1');
define('PLATFORM_ID_RU', 'RU');
define('PLATFORM_ID_JPN1', 'JPN1');

// global variable to store the static item data
$staticItemData = array();


function getLeagueAPIKey()
{
	if(defined('LEAGUEAPIKEY'))
	{
		return LEAGUEAPIKEY;
	}
	else
	{
		return null;
	}
}


function getRegions()
{
	// returning an array with all the regions, so we can check that we got a valid region input
	// we're using the platform IDs as values so we can match regions to platform IDs
	$rgn[REGION_NA] = PLATFORM_ID_NA1;
	$rgn[REGION_BR] = PLATFORM_ID_BR1;
	$rgn[REGION_EUNE] = PLATFORM_ID_EUN1;
	$rgn[REGION_EUW] = PLATFORM_ID_EUW1;
	$rgn[REGION_KR] = REGION_KR;
	$rgn[REGION_LAN] = PLATFORM_ID_LA1;
	$rgn[REGION_LAS] = PLATFORM_ID_LA2;
	$rgn[REGION_OCE] = PLATFORM_ID_OC1;
	$rgn[REGION_TR] = PLATFORM_ID_TR1;
	$rgn[REGION_RU] = REGION_RU;
	$rgn[REGION_JP] = PLATFORM_ID_JPN1;

	return $rgn;
}


// Platform IDs are similar to regions, but not the same. Platform ID is used for the Mastery endpoint
function getPlatformIDs()
{
	// returning an array with all the platform IDs, so we can check that we got a valid platform ID input
	// we're using the regions as values so we can match platform IDs to regions
	$rgn[PLATFORM_ID_NA1] = REGION_NA;
	$rgn[PLATFORM_ID_BR1] = REGION_BR;
	$rgn[PLATFORM_ID_EUN1] = REGION_EUNE;
	$rgn[PLATFORM_ID_EUW1] = REGION_EUW;
	$rgn[PLATFORM_ID_KR] = REGION_KR;
	$rgn[PLATFORM_ID_LA1] = REGION_LAN;
	$rgn[PLATFORM_ID_LA2] = REGION_LAS;
	$rgn[PLATFORM_ID_OC1] = REGION_OCE;
	$rgn[PLATFORM_ID_TR1] = REGION_TR;
	$rgn[PLATFORM_ID_RU] = REGION_RU;
	$rgn[PLATFORM_ID_JPN1] = REGION_JP;

	return $rgn;
}


function isValidRegion($region = '')
{
	// check if the given region is in the list of valid regions
	return array_key_exists($region, getRegions());
}


function isValidPlatformID($platformID = '')
{
	// check if the given platform ID is in the list of valid platform IDs
	return array_key_exists($platformID, getPlatformIDs());
}


function getPlatformIDFromRegion($region = '')
{
	$regions = getRegions();
	if(array_key_exists($region, $regions))
	{
		return $regions[$region];
	}
	else
	{
		return null;
	}
}


function getCleanSummonerName($name = '')
{
	if($name == '')
	{
		return null;
	}
	else
	{
		// remove all the things (to help prevent security issues)
		return htmlspecialchars(stripslashes(strtolower(str_replace(' ', '', trim($name)))));
	}
}


// returns: Array ( [namehere] => Array ( [id] => 999999 [name] => Name Here [profileIconId] => 783 [summonerLevel] => 30 [revisionDate] => 1461818714000 ) )
function getPlayerInfo($region = '', $name = '')
{
	if($region != '' && $name != '' && isValidRegion($region))
	{
		// make sure that name is clean
		$cleanName = getCleanSummonerName($name);

		$url = 'https://' . $region . '.api.pvp.net/api/lol/' . $region . '/v1.4/summoner/by-name/' . $cleanName . '?api_key=' . getLeagueAPIKey();

		//error_log('In getPlayerInfo. name: ' . $name . ', cleanName: ' . $cleanName . ', region: ' . $region . ', url: ' . $url);

		return getJSONObject($url);
	}

	// need a name and region but didn't get at least one, returning null
	return null;
}


// https://na.api.pvp.net/championmastery/location/NA1/player/999999/topchampions?count=10&api_key=
function getMasteryInfoForPlayer($platformID = '', $playerID, $count = 10)
{
	// make sure we got some inputs
	if($platformID != '' && isValidPlatformID($platformID) && isInteger($playerID) && isInteger($count))
	{
		// build our url to get the mastery info
		$url = 'https://global.api.pvp.net/championmastery/location/' . $platformID . '/player/' . $playerID . '/topchampions?count=' . $count . '&api_key=' . getLeagueAPIKey();

		$rawMasteryData = getJSONObject($url);

		return $rawMasteryData;
	}

	return null;
}


// https://global.api.pvp.net/api/lol/static-data/na/v1.2/champion?dataById=true&api_key=
/*
	Returns basic champion data, indexed by champion ID. The 'key' value should be used for image names
	Example:
	"96": {
		"id": 96,
		"title": "the Mouth of the Abyss",
		"name": "Kog'Maw",
		"key": "KogMaw"
	},
	"222": {
		"id": 222,
		"title": "the Loose Cannon",
		"name": "Jinx",
		"key": "Jinx"
	}
	...
*/
function getBasicChampionInfo($region = '')
{
	if($region != '' && isValidRegion($region))
	{
		// build our url to get static champion info
		$url = 'https://global.api.pvp.net/api/lol/static-data/' . $region . '/v1.2/champion?dataById=true&api_key=' . getLeagueAPIKey();

		$rawChampData = getJSONObject($url);

		return $rawChampData['data'];
	}

	// need a region but didn't get one, returning null
	return null;
}


function getSummonerIdFromName($region = '', $name = '')
{
	if($region != '' && $name != '')
	{
		// get player info from a summoner name
		$playerData = getPlayerInfo($region, $name);

		if($playerData === null)
		{
			return null;
		}
		else
		{
			// return the summoner ID
			$cleanName = getCleanSummonerName($name);

			// use this foreach because summoner names with special characters (like Śký) can be
			// difficult to handle; since we should only have 1 array here we return the first ID
			foreach($playerData as $key => $info)
			{
				return $info['id'];
			}

			// if we didn't return anything, something is wrong, so return null
			return null;
		}
	}

	// didn't get the required inputs
	return null;
}

?>

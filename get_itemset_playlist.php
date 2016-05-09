<?php
// this is basically a web service that expects to get an item set JSON POSTed to it
// and returns a JSON string that contains the HTML for the 

//error_log('In get_itemset_playlist. $_POST: ' . print_r($_POST, true));

// we want to make sure we got something in POST
if(!empty($_POST) && !empty($_POST['itemset']))
{
	require_once('inc/echonest_helper.php');
	require_once('inc/league_helper.php');
	
	$itemSet = $_POST['itemset'];	
	
	//error_log('In get_itemset_playlist. $itemSet: ' . print_r($itemSet, true));
	
	if($itemSet != null)
	{
		$itemSetStats = getItemSetTotalStats($itemSet, REGION_NA);
		$itemSetInfo = getItemSetInfoFromItemSetStats($itemSetStats);
		
		//error_log('In get_itemset_playlist. Got play style: ' . $itemSetInfo);
		
		// if we didn't get a play style, send back an error
		if($itemSetInfo['play-style'] == null)
		{
			echo '{"response": {"status": "Failure", "message": "Cannot process item set"}}';
			exit;
		}
		
		// let's grab the query parameters based on the play style
		$queryParameters = getEchoNestAPIQueryParamters($itemSetInfo);
		
		// get the item set title, to use for the name of the playlist
		$playlistTitle = '';
		
		if(!empty($itemSet['title']))
		{
			$playlistTitle = $itemSet['title'] . ' Playlist';
		}
		
		$trackIDs = getSpotifyTrackList($queryParameters);
		
		// check if we got a rate limit error back
		if($trackIDs == 'ERROR|RATELIMIT')
		{
			echo '{"response": {"status": "Failure", "message": "Item Playlists is under heavy load right now and cannot generate a playlist, try again in a minute"}}';
			exit;
		}
		
		// get the traits for the item set into a string
		$traits = '';
		if(array_key_exists('traits', $itemSetInfo))
		{
			foreach($itemSetInfo['traits'] as $trait)
			{
				if($traits !== '')
				{
					$traits .= ', ';
				}
				
				$traits .= $trait;
			}
		} 
		
		// get the image of a random item from the item set
		$imageBlock = rand(0, count($itemSet['blocks']) - 1);
		$imageItem = rand(0, count($itemSet['blocks'][$imageBlock]['items']) - 1);
		
		//error_log('In get_itemset_playlist. $imageBlock: ' . $imageBlock . ', $imageItem: ' . $imageItem);
		
		$imageItemID = $itemSet['blocks'][$imageBlock]['items'][$imageItem]['id'];
		
		$imageItemURL = getFullItemImageURL($imageItemID);
		
		$imageItemJSON = '';
		
		if($imageItemURL !== null)
		{
			$imageItemJSON = ', "display_image": "' . $imageItemURL . '"';
		}
		
		// now build the whole respone string
		$responseString = '{"response": {"status": "Success", "message": "Success", "play_style": "' . $itemSetInfo['play-style'] . '", "trackIDs": "' . $trackIDs . '", "traits": "' . $traits . '"' . $imageItemJSON . '}}';
		
		//error_log('In get_itemset_playlist. Success, returning: ' . $responseString . ', queryParameters: ' . $queryParameters);
		
		echo $responseString;
	}
	
}
// we didn't get a POST, so return an error response
else
{
	echo '{"response": {"status": "Failure", "message": "Did not receive item set data"}}';
}

?>
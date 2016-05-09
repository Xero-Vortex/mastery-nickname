<?php
function getJSONObject($url)
{
	//error_log('Getting URL contents for: [' . $url . ']');

	$jsonData = getUrlContents($url);

	return parseJSONText($jsonData);
}


function parseJSONText($jsonText)
{
	if($jsonText)
	{
		//error_log('Got JSON data: [' . print_r($jsonText, true) . ']');

		return json_decode($jsonText, true);
	}

	return null;
}

function getUrlContents($url)
{
	$curl = curl_init();
	$timeout = 5;
	curl_setopt ($curl, CURLOPT_URL,$url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	$data = curl_exec($curl);
	curl_close($curl);

	return $data;
}

function isInteger($input)
{
    return(ctype_digit(strval($input)));
}
?>

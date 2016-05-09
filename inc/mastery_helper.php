<?php

function masteryDataCompareHighToLow($champA, $champB)
{
    // the first item's points are lower than the second, so we need to return something > 0
    if(intval($champA['championPoints']) < intval($champB['championPoints']))
    {
        return 1;
    }
    // the first item's points are higher than the second, so we need to return something < 0
    else if(intval($champA['championPoints']) > intval($champB['championPoints']))
    {
        return -1;
    }
    // the mastery percents are the same, so we'll go in numerical order based on champion ID
    else
    {
        // sort by champion ID, lowest to highest
        // the first item's ID is higher than the second, return something > 0
        if(intval($champA['championId']) > intval($champB['championId']))
        {
            return 1;
        }
        // the first item's ID is lower than the second, return something < 0
        if(intval($champA['championId']) < intval($champB['championId']))
        {
            return -1;
        }
        // the IDs are the same??? This should never happen...
        else
        {
            return 0;
        }
    }
}


function getChampionMasteryNickname($masterySummary)
{
    $curPercent = 0;
    $nickname = '';
    $part = '';
    $nicknameParts = array();
    $nickPartNum = 0;

    // sort the data by highest champion mastery points to lowest
    usort($masterySummary, 'masteryDataCompareHighToLow');

    // build the nickname using the percentage of total mastery points to pull out some letters from each champion name
    // ex: if Thresh is 33% of the player's mastery points, we'd start with the letters 'Th'
    foreach($masterySummary as $key => $info)
    {
        /*
        'name' => $champName
        , 'championId' => $champID
        , 'championPoints' => $masteryInfo[$i]['championPoints']
        , 'pointPercent' => $masteryInfo[$i]['championPoints'] / $totalMasteryPoints
        */

        // get nickname part for the current champion and total mastery percent
        $part = getChampionMasteryNicknamePart($info['name'], $curPercent, $info['pointPercent']);

        $nicknameParts[$nickPartNum] = ['championName' => $info['name'], 'nicknamePart' => $part];

        $nickname .= $part;

        $curPercent += $info['pointPercent'];
        $nickPartNum++;
    }

    //error_log('In getChampionMasteryNickname. Got nickname: ' . $nickname);
    return $nickname;
}


function getChampionMasteryNicknamePart($champName, $curPercent, $masteryPercent)
{
    // get the starting character position from the current
    // percentage of mastery points we've already processed
    // Ex: if our current percent is 50% and we are looking at Thresh
    // we start at character 3 ('e')
    $strStart = round(strlen($champName) * $curPercent);

    // get the number of characters we're going to use from this champion's name
    // based on the percentage of total mastery points attributed to this champion
    $numCharacters = round(strlen($champName) * $masteryPercent);

    // if we can, add an extra character to each nickname part, because it looks better with
    if(intval($strStart) + intval($numCharacters) + 1 <= strlen($champName))
    {
        $numCharacters++;
    }

    // if we would pull 0 characters for this champion, let's at least take the last character of the name
    if($strStart >= strlen($champName))
    {
        $strStart = strlen($champName) - 1;
        $numCharacters = 1;
    }

    // now get the substring, starting at our start percent and grabbing the
    // proportionate percentage of characters from the name compared to the mastery percent
    return substr($champName, intval($strStart), intval($numCharacters));
}

?>

<?php
require_once('inc/league_helper.php');

// we are expecting to get champion IDs and mastery percentages for each champion, as GET parameters
// exmaple:
// ../champion_mastery_image.php?c1=412&p1=28&c2=111&p2=23&c3=106&p3=19&c4=28&p4=17&c5=421&p5=13

// loading screen images are 308 x 560
$imageWidth = 308;
$imageHeight = 560;

$masteryData = array();

// hardcoded to NA region, the champion name keys should be the same for all regions (right...?)
$champInfo = getBasicChampionInfo('na');

// hardcoded to at most 5 champions right now
// could add another GET parameter if we really wanted to be able to get more
for($i=1; $i<=5; $i++)
{
    // we need both a champion ID and mastery percent to add to the image
    if(!empty($_GET['c' . $i]) && !empty($_GET['p' . $i]))
    {
        $champID = intval($_GET['c' . $i]);
        $percent = intval($_GET['p' . $i]);

        // check that the given champion ID is actually a champion ID
        // and make sure the percent isn't higher than 100
        if(array_key_exists($champID, $champInfo) === true && $percent <= 100)
        {
            $masteryData[] = ['championId' => $champID, 'key' => $champInfo[$champID]['key'], 'masteryPercent' => $percent];
        }
    }
}

//error_log('In champion_mastery_image.php. $masteryData: ' . print_r($masteryData, true));

// now create the image for each champion

// keep track of the total percentage we've covered so far
// and cut our image off at 100% if needed (in case we got bad data)
$totalPercent = 0;

// store out total traversed height so we know where to start drawing our next champion
$totalHeight = 0;

// create our working image that we will add our champion images to
$workingImg = imagecreatetruecolor($imageWidth, $imageHeight);

foreach($masteryData as $item)
{
    // make sure we don't go over 100%
    if($totalPercent + $item['masteryPercent'] <= 100)
    {
        $percentToUse = $item['masteryPercent'];
    }
    // else the next masteryPercent would put us over 100%, so just use
    // whatever percent is left to get us to 100% total percent
    else
    {
        $percentToUse = 100 - $totalPercent;
    }

    // only add the image if we have a positive percent above 0
    if($percentToUse > 0)
    {
        // get the image for our current champion
        $curImg = imagecreatefromjpeg('./img/champions/' . $item['key'] . '_0.jpg');

        // calculate the number of pixels this champion will span vertically
        $curHeight = round($item['masteryPercent'] / 100 * $imageHeight);


        // copy part of the first image
        // $dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h
        imagecopy($workingImg, $curImg, 0, $totalHeight, 0, $totalHeight, $imageWidth, $totalHeight + $curHeight);

        $totalPercent += $item['masteryPercent'];
        $totalHeight += $curHeight;
    }
}

// output the image
header('Content-Type: image/png');
imagepng($workingImg);

?>

# Mastery Nickname for League of Legends

Check out the live site and see what your nickname is: [Mastery Nickname for League of Legends](http://xero.tech/mastery/)

Mastery Nickname for League of Legends is an application that was created for the Riot Games API Challenge 2016, which ran from April 22, 2016 through May 9, 2016. The application allows League of Legends players to enter a summoner name (a.k.a. a player name) and it generates a nickname and a procedural image (using loading screen art) for the given player based on that player's champion mastery data.

The nickname is generated using parts of the champion names for the top 5 champions that the player has the highest mastery points for. The procedural image is generated in a similar way: using parts of the loading screen art for the top 5 champions.

### Nickname Generation
The nickname is generated in a fairly simple way. First the application finds the percentage of mastery points for each champion when compared to the sum of all 5 champions' mastery points. Then the application steps through each of the 5 champion names and extracts the characters in the name that corresponds to the percent of mastery points that champion has.

For example, if a player has the highest mastery points with Thresh which is 28% of the total of the top 5 champions' mastery points, the application starts the nickname with 'Thr' (28% of the string 'Thresh' rounds to 2 characters and then the application always adds 1 character because it makes for better nicknames). If the player's next highest champion is Nautilus with 24%, the application starts 28% of the way into the string 'Nautilus' (the starting percent is cumulative for all the champions as we go) and takes 24% of the string from there (+1 character for better nicknames). In this case 'uti' would be extracted. The next name would start at the 52% position in the given champion's name, and the process continues on.

A composite image is also generated using champion loading screen art that visually shows the percentages of mastery points each of the top 5 champions. The image starts with the highest mastery champion at the top and shows the percentage the image that corresponds to the percentage of mastery points, in a similar way to how the nickname was generated.

# Technical Info
This application uses PHP on the server and html, javascript, and css on the client side. There is no need for database storage as everything is calculated when a summoner name is entered.

## Languages, Libraries, and Server
* PHP 5.6
* Bootstrap v3.3.4
* html/javascript/css
* Any PHP host should be fine to host the application. [OpenShift](https://www.openshift.com/) offers a free PHP (among other languages) hosting service and is a good place to start if you don't already have a hosting plan.

## Requirements
### Riot Games API Key
The Riot Games API key is used to get player mastery data and static game data. To get a Riot Games API key, follow the steps here: https://developer.riotgames.com/docs/getting-started

## Installation
To install this application, upload the code to a PHP server and enter your Riot Games API key in the /inc/config.php file.

## Application Structure
The application is a one-page site. The most notable files are:
* `index.php` - The entry point for the application and file that processes GET user input. This is the 'one page' of the site.
* `champion_mastery_image.php` - This file takes GET parameters and programmatically generates an image that combines champion loading screen art. See the description above for more info on how it works.
* `inc/config.php` - This file defines PHP constants for the Riot Games API key. You **must** add your own API key to this file before the application will work.
* `inc/league_helper.php` - This file handles all the League-related logic that is performed server-side. This includes getting data from the Riot Games API. If it's related to League data specifically, it's probably in this file.
* `inc/general_helper.php` - Similar to the other helpers, but for general functionality. This mostly contains functions to get a JSON object from a given URL that returns JSON data.
* `inc/page_components.php` - Contains some common html code for the site, such as the header, footer, and javascript and css include statements. While this isn't too useful right now since the application is a one-page site, if it grows these common sections won't need to be re-written.

## Application Flow Outline
The following is an overview of how the application executes when given the correct data:

1. User enters a League of Legends summoner (player) name and region
2. The application (server-side) gets the player's mastery data using the Riot Game API
3. The application (server-side) generates a nickname based on the mastery data using the player's top 5 champions (see above for a description of how the nickname is generated)
4. The application (server-side) generates an image by combining loading screen art for the top 5 champions
5. The user is then served with a page that displays the generated nickname and image, huzzah!

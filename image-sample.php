<?php
/**
 * Image example on using the HypixelPHP API.
 * Almost fully documented. Please refer to the PHP docs if you have any questions regarding php.
 * This is quite the same as the simple example, but instead of showing it in a HTML formatted page, it's displayed
 * inside of an png image.
 *
 * @author    Max Korlaar
 * @author    Plancke (for the Hypixel-PHP project)
 * @copyright Max Korlaar
 * @license   MIT
 */

// ini_set('display_errors', 1); // Uncomment this line to make sure errors are being displayed, if something's wrong.
// Handle everything before we send out the web page.
require_once 'includes/HypixelPHP.php'; // Point this to the location of your HypixelPHP file.
use HypixelPHP\HypixelPHP;

$options = [
    'api_key' => 'your-api-key', // Replace this with your API-key. This is the only value you /need/ to change.
    'cache_times' => [ // These are the times for how long stuff will be kept in cache before requesting new stuff.
        // The longer, the faster things will load, but information won't be up-to-date in the meantime.
        'overall' => 900, // It's in seconds, so 900 seconds = 15 minutes
        'uuid' => 864000, // For UUID related information. This rarely changes, so a long cachetime is OK.
    ],
    'timeout' => 1.5, // Also in seconds. Higher = More stable. Lower = faster if the api's down.
    'logging' => false, // If you want to log debug messages, enable this.
    'debug' => false // Will display debug info in the form of HTML comments in your page's source if true.
    // There are way more options. Take a look at the HypixelPHP source for more.
];
$hypixel = new HypixelPHP($options); // Initialize HypixelPHP

/* Do you know what the input is (so is it an username or is it an uuid)?
 * if it's an username, use ['name' => 'username here']
 * if it's an uuid, use ['uuid' => 'uuid here']
 * if you don't know, use ['unknown' => 'uuid or username here']
 */

$player = $hypixel->getPlayer(['unknown' => 'MaxKorlaar']); // Fetches a new Player, with the name MegaMaxsterful. (object).

if ($player === null || $player->getStats() === null) {
    // Oops! Why is there no information about this player? Is the API down?
    // This may also trigger if the player does exist, but it doesn't have main statistics for some reason.
    $errorInfo = $hypixel->getUrlError();
    // Use var_dump($errorInfo); to see what $errorInfo returns. It may return null, meaning that the player probably doesn't exist.

    // For now, display an error on the image that we generate:
    $error = true;

} else {
    $error = false;
    $username = $player->getName(); // There are way more things to get from a player. Again, check HypixelPHP.php.
    $rank = $player->getRank(false); // getRank($package, $preEula). false here means that it requests the player's actual rank, not the bought one.
    $displayName = $player->getFormattedName(); // Useful when working in HTML. Returns a html formatted string which displays the player's name and rank in color.

    // Why not fetch some statistics?
    $mainStatistics = $player->getStats();
    // Now use this to get statistics from a game, like Quake.
    $quakeStats = $mainStatistics->getGame('Quake'); // If you know the game's ID, you can use getGameFromID(); instead.
    $kills = $quakeStats->get('kills', false, 0); // get($what, $implicit, $default); $default is returned if the $what is not found.
    // TIP! Don't know what statistics are available? Use var_dump($quakeStats->getRecord()); !
}

// Get the image

$font = "../../resources/SourceSansPro-Light.otf";
$im = imagecreatetruecolor(250, 80); // Width, height in px
$trans_color = imagecolorallocatealpha($im, 250, 250, 240, 10); // The first R,G,B values do not actually matter, since the color will be transparent (127 = transparent)
imagefill($im, 0, 0, $trans_color); // Make the background transparent
imagesavealpha($im, true);
$black = imagecolorallocate($im, 0, 0, 0); // Get a color for the text

imagettftext($im, 25, 0, 0, 25, $black, $font, $username); // Put his username on the image at 0,25 (from top-left)
// With size 25 and rotation 0
imagettftext($im, 20, 0, 0, 50, $black, $font, $kills . ' Quakecraft kills!');

imagettftext($im, 13, 0, 0, 75, $black, $font, 'I use PHP!');

header('Content-type: image/png'); // Let the browser know it's an image. Headers must be sent before any output!
// Comment/remove the above line if you are debugging the file (var_dumping stuff or getting errors) to make sure you browser knows to display text to you!
imageinterlace($im);
imagepng($im); // Outputs the image to the requester
imagedestroy($im); // Destroys the image object, freeing up some memory

// Congratulations, you have successfully created an image containing up-to-date statistics!
// It took me quite long to figure out on how-to have transparent backgrounds though :P

?>
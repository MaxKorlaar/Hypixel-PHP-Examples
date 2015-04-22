<?php
    /**
     * Simple example on using the HypixelPHP API.
     * Almost fully documented. Please refer to the PHP docs if you have any questions regarding php.
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
        'api_key'    => 'your-api-key', // Replace this with your API-key. This is the only value you /need/ to change.
        'cache_time' => 600, // In seconds. 600 is recommended.
        'timeout'    => 1.5, // Also in seconds. Higher = More stable. Lower = faster if the api's down.
        'logging'    => false, // If you want to log debug messages, enable this.
        'debug'      => false, // Will display debug info in the form of HTML comments in your page's source if true.
        // There are way more options. Take a look at the HypixelPHP source for more.
    ];
    $hypixel = new HypixelPHP($options); // Initialize HypixelPHP

    /* Do you know what the input is (so is it an username or is it an uuid)?
     * if it's an username, use ['name' => 'username here']
     * if it's an uuid, use ['uuid' => 'uuid here']
     * if you don't know, use ['unknown' => 'uuid or username here']
     */

    $player = $hypixel->getPlayer(['unknown' => 'MegaMaxsterful']); // Fetches a new Player, with the name MegaMaxsterful. (object).

    if ($player === null || $player->getStats() === null) {
        // Oops! Why is there no information about this player? Is the API down?
        // This may also trigger if the player does exist, but it doesn't have main statistics for some reason.
        $errorInfo = $hypixel->getUrlError();
        // Use var_dump($errorInfo); to see what $errorInfo returns. It may return null, meaning that the player probably doesn't exist.
        $error = true; // We can use this later, so we display a different page layout.
    } else {
        $error       = false;
        $username    = $player->getName(); // There are way more things to get from a player. Again, check HypixelPHP.php.
        $rank        = $player->getRank(false); // getRank($package, $preEula). false here means that it requests the player's actual rank, not the bought one.
        $displayName = $player->getFormattedName(); // Useful when working in HTML. Returns a html formatted string which displays the player's name and rank in color.

        // Why not fetch some statistics?
        $mainStatistics = $player->getStats();
        // Now use this to get statistics from a game, like Quake.
        $quakeStats = $mainStatistics->getGame('Quake'); // If you know the game's ID, you can use getGameFromID(); instead.
        $kills      = $quakeStats->get('kills', false, 0); // get($what, $implicit, $default); $default is returned if the $what is not found.
        // TIP! Don't know what statistics are available? Use var_dump($quakeStats->getRecord()); !
    }

?>
<!DOCTYPE html>
<html>
<head>
    <title>Simple sample on how-to use HypixelPHP</title>
    <meta name="author" content="Max Korlaar">
</head>
<body>
<h2>Totally unnecessary title</h2>

<p>Note that this page is currently unstyled, and thus, looks like super poop. I r8 8/8 m8 legendary, they say...</p>

<div class="page">
    <?php
        // Print the statistics to the page, or did something went wrong?
        if ($error === false) {
            // Nothing went wrong. Print the values we just retrieved.
            echo '<h3>Statistics for ' . $displayName . '</h3>';
            echo '<p><b>Quake Kills:</b> ' . $kills;
            echo '<br/><b>Username:</b> ' . $username;
            echo '<br/><b>Rank:</b> ' . $rank;
            echo '</p>'; // Close the p-tag.
        } else {
            // Something went wrong :(
            // Blame @Plancke?
            echo '<h3>Oops... Something went wrong.</h3>';
        }
    ?>
</div>
</body>
</html>
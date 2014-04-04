<?php
// Takes twitter RSS feed, strips the "username: ", removes @replies &
// auto-hyperlinks links

$username=$_GET["username"]; // request any username with '?username='
if ( empty($username) ) {
	$username='williamlong';    // <-- change this to your username!
} else {
	// Make sure username request is alphanumeric
	$username=ereg_replace("[^A-Za-z0-9]", "", $username);
}
$feedURL='http://twitter.com/statuses/user_timeline/'.$username.'.rss';
// pattern to exclude. this excludes any @replies
$excludePattern='/'.$username.': @/'; 

if(!$xml=simplexml_load_file($feedURL)){
	    trigger_error('Error reading XML file',E_USER_ERROR);
}
echo '<?xml version="1.0" encoding="UTF-8"?>'; 
?>

<rss version="2.0">
	<channel>
		<title><?php echo $xml->channel->title; ?></title>
		<link><?php echo $xml->channel->link; ?></link>
		<description><?php echo $xml->channel->description; ?></description>
		<language><?php echo $xml->channel->language; ?></language>
		<ttl><?php echo $xml->channel->ttl; ?></ttl>
<?php foreach($xml->channel->item as $item) { 
		  if ( ! preg_match("$excludePattern", $item->title)) {  
				// remove "username: " clean up html & add hyperlinks
				$filteredTitle=htmlspecialchars("$item->title");
				$filteredTitle=str_replace("$username: ","",$filteredTitle);
				$filteredDesc=htmlspecialchars("$item->description");
				$filteredDesc=str_replace("$username: ","",$filteredDesc); 
				$filteredDesc=preg_replace('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?[^.\ ,:)])@','<a href="$1">$1</a>', $filteredDesc); ?>
     <item>
		<title><?php echo $filteredTitle; ?></title>
		<description><![CDATA[<?php echo $filteredDesc; ?>]]></description>
		<pubDate><?php echo $item->pubDate; ?></pubDate>
		<guid><?php echo $item->guid; ?></guid>
		<link><?php echo $item->link; ?></link>
	</item>
<?php } } ?>
	</channel>
</rss>

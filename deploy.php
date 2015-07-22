<?php
	/**
	 * GIT DEPLOYMENT SCRIPT
	 *
	 * Used for automatically deploying websites via github securely, more deets here:
	 *
	 *		https://gist.github.com/limzykenneth/baef1b190c68970d50e1
	 */
	// The header information which will be verified
	$agent=$_SERVER['HTTP_USER_AGENT'];
	$signature=$_SERVER['HTTP_X_HUB_SIGNATURE'];
	$body=@file_get_contents('php://input');
	// The commands
	$commands = array(
		'echo $PWD',
		'whoami',
		'git reset --hard HEAD 2>&1',
		'git pull origin master 2>&1',
		'git status',
		'git submodule sync',
		'git submodule update'
	);
	base64_encode($agent);
	base64_encode($signature);
	// Run the commands for output
	$output = ''; var_dump(strpos($agent,'GitHub-Hookshot')); var_dump(hash_equals($signature, verify_request()));
	var_dump($signature);
	var_dump(verify_request());
	if (strpos($agent,'GitHub-Hookshot') !== false){
		//if (hash_equals($signature, verify_request())){
			// Run the commands
			foreach($commands AS $command){
				// Run it
				$tmp = shell_exec($command);
				// Output
				$output .= "<span style=\"color: #6BE234;\">\$</span> <span style=\"color: #729FCF;\">{$command}\n</span>";
				$output .= htmlentities(trim($tmp)) . "\n";
		/*	}
		}else{
			header('HTTP/1.1 403 Forbidden');
			echo "Invalid request.";
		}*/
	}else{
		header('HTTP/1.1 403 Forbidden');
		echo "Invalid request.";
	}
	// Generate the hash verification with the request body and the key stored in your .htaccess file
	function verify_request(){
		$message = $GLOBALS['body'];
		$key     = $_ENV['GIT_TOKEN'];
	    	$hash    = hash_hmac("sha1", $message, $key);
	    	$hash = "sha1=".$hash;
	    	return $hash;
	}
	// Compares the hash given in the header and the one generated by verify_request()
	// "==" is not recommended as it is prone to timing attacks
	// This function is built into PHP 5.6++ so if you have it you can ommit the following function
	function hash_equals( $a, $b ) {
	    $a_length = strlen( $a );
	    if ( $a_length !== strlen( $b ) ) {
	        return false;
	    }
	    $result = 0; 
	    // Do not attempt to "optimize" this.
	    for ( $i = 0; $i < $a_length; $i++ ) {
	        $result |= ord( $a[ $i ] ) ^ ord( $b[ $i ] );
	    } 
	    return $result === 0;
	}
	echo "Deploy successful."
?>
<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<title>GIT DEPLOYMENT SCRIPT</title>
</head>
<body style="background-color: #000000; color: #FFFFFF; font-weight: bold; padding: 0 10px;">
<pre>
 .  ____  .    ____________________________
 |/      \|   |                            |
[| <span style="color: #FF0000;">&hearts;    &hearts;</span> |]  | Git Deployment Script v0.1 |
 |___==___|  /              &copy; oodavid 2012 | AND | https://gist.github.com/limzykenneth/baef1b190c68970d50e1 | 2021creative Team | Go Away bots..
              |____________________________|
 
<?php echo $output; ?>
</pre>
</body>
</html>
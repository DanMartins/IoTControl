/*%################################################################
/*% NetControl - IoTControl
/*%
/*%       Network - Interface.
/*%       DanMartins
/*%       IoTControl reasearch project
/*%       São Paulo, 2017.
/*%
/*%################################################################
*/
<?php
popen("/var/www/html/IoTControl.sh", "r");
echo "IoTControl\n";
$output = shell_exec('whoami');
echo "whoami\n";
echo "<pre>$output</pre>";
$output = shell_exec('pwd');
echo "pwd\n";
echo "<pre>$output</pre>";

?>

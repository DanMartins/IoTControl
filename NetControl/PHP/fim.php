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

if (file_exists("/tmp/inic-exec"))
{
 echo "Encerrando aplicativo em execução ...\n";
 $gerarq = fopen("/tmp/abortar-exec", "w");
 fclose($gerarq);
}

echo "Aguardando ...\n";
sleep(2);
echo "Iniciar desligamento de CPU ...\n";

popen("/var/www/html/fim.sh", "r");

echo "IoTControl\n";
$output = shell_exec('whoami');
echo "whoami\n";
echo "<pre>$output</pre>";
$output = shell_exec('pwd');
echo "pwd\n";
echo "<pre>$output</pre>";

echo "desligado.\n";

?>

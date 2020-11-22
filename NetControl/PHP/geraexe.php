/*%################################################################
/*% NetControl - IoTControl
/*%
/*%       Network - Interface.
/*%       DanMartins
/*%       IoTControl reasearch project
/*%       S�o Paulo, 2017.
/*%
/*%################################################################
*/
<?php

   print("<html>\n");
   print("<head>\n");
   print("\t<title>IoTControl Configuração de Controle</title>\n");
   print("</head>\n");
   print("<body>\n");

   // No warnings but errors ON
   error_reporting(E_ERROR | E_PARSE);

   print("\t<h4>IoTControl - Comando em processamento</h4>\n");

   print("Abortar\n");
   $iniciado = file_exists ("/tmp/inic-exec");
   if (file_exists("/tmp/inic-exec"))
   {
     $gerarq = fopen("/tmp/abortar-exec", "w") or die("Processo cancelado!");
     fclose($gerarq);
   }

   print("</body>\n");
   print("</html>\n");

// end of PHP_IoT_ODBC.php
?>
<html>
    <head>
    </head>
    <body>
    <?php
	ob_start(); // ensures anything dumped out will be caught

	// do stuff here
	$url = 'Location: '. $_SERVER['HTTP_REFERER'];

	// clear out the output buffer
	while (ob_get_status())
	{
		ob_end_clean();
	}

	// no redirect
	header( "$url" );
	?>
    </body>
</html>
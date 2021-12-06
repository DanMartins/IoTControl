<!-- %################################################################
######################################################
## {IoTControl - NetControl - Interface}            ##
######################################################
## { DanMartins/IoTControl is licensed under the    ##
##   GNU General Public License v3.0}               ##
######################################################
## Author: {DanMartins}                             ##
## Copyright: Copyright {2021}, {IoTControl}        ##
## Credits: [{https://domartins.wixsite.com/data}]  ##
## License: {GNU General Public License v3.0}       ##
## Version: {2021}.{04}.{22}                        ##
## Maintainer: {github.com/DanMartins/IoTControl}   ##
## Email: {github.com/DanMartins}                   ##
## Status: {Development}                            ##
/*%################################################################
-->
<?php
   print("<!DOCTYPE html>\n");
   print("<html>\n");
   print("<head>\n");
   print("\t<title>IoTControl</title>\n");
   print("<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n");

   $osrunning = php_uname();
   if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
    // Force ODBCINI to define unixODBC var to the browser user.
	putenv("ODBCINI=/etc/odbc.ini");

   }

   popen("/var/www/html/PI.sh", "r");

   // No warnings but errors ON
   error_reporting(E_ERROR | E_PARSE);

   $ses = Initialize();

   $query = "SELECT ajuste, valork, valorki FROM controle";

   $qry = odbc_exec($ses, $query);

   $result = odbc_fetch_array($qry);


   if (!$qry)
    H_Erro("odbc_exec(SELECT TOP)");

   //Done($ses);


   //
   // Initialize()
   //
   // Perform the minimum requirement of logging onto the c-tree Server
   //

   function Initialize() {

      // connect to server
      $test_dsn = "c-treeACE ODBC Driver";
      if(($ses = odbc_pconnect( $test_dsn,"admin","ADMIN")) == FALSE)
      {
        H_Erro("odbc_connect()");
      }else

      return ($ses);
   }


   //
   // Done()
   //
   // This function handles the housekeeping of closing, freeing,
   // disconnecting and logging out of the database
   //

   function Done ($ses) {
      // logout
      odbc_close($ses);
   }

   //
   // H_Erro()
   //
   // General error routine that retrieves and displays specific SQL Error
   // before exiting the tutorial.  If the error returned indicates an object
   // already exists, execution is returned to the calling function.
   //

   function H_Erro($msg) {

      $err = odbc_errormsg();

      print("$msg - SQL ERROR: [$err] <br>\n");
      print("*** Processamento abortado *** <br>\n");
      exit();
   }

   print("</head>\n");
   print("<body>\n");

   print("</body>\n");
   print("</html>\n");

// end of PHP_IoT_ODBC.php
?>
<html>
    <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
    /* CSS property for content section */
	.columnA, .columnB, .columnC, .columnD {
	    float: left;
	    width: 23%;
	    height: 98%;
	    padding: 1%;
	    text-align:justify;
    }
    </style>
    <style>
    /* CSS property for content section */
	.colA, .colB, .colC {
	    float: left;
	    width: 33.3%;
	    height: 25vh;
	    padding: 0%;
	    text-align:center;
    }
    </style>
    <style>
    /* CSS property for content section */
	.clA, .clB {
	    float: left;
	    width: 50%;
	    height: 25vh;
	    padding: 0%;
	    text-align:center;
    }
    </style>

        <h1>PI</h1>
        <!-- Content section of website layout -->

	<table>
	<form method="post" action="piexe.php">

            <div class = "columnA">
                <h2>Kp</h2>
	            <input type="number" min="-100" max="100" step="0.0001" style="width: 98%; font-size:1em;"
	                   name="valork" value="<?php echo $result['valork'] ?>">
                <p></p>
            </div>

            <div class = "columnB">
                <h2>Ki</h2>
                <input type="number" min="-100" max="100" step="0.0001" style="width: 98%; font-size:1em;"
	                   name="valorki" value="<?php echo $result['valorki'] ?>">
                <p></p>
            </div>


            <div class = "columnD">
                <h2>Control</h2>
                <input type="number" min="-400" max="400" step="10" value="<?php echo $result['ajuste'] ?>" name="ajuste"
	                   for="power" oninput="power.value=ajuste.value" style="width: 98%; font-size:1em; text-align:right;" >
                <p></p>
            </div>

	  <input type="range" min="-400" max="400"  step="10" value="<?php echo $result['ajuste'] ?>" name="power" list="powers"
	         style="height:25%; width:100%; float:center;"
	         for="ajuste" oninput="ajuste.value=power.value" >

		<datalist id="powers">
		  <option value="-400" label="-400">
		  <option value="-350">
		  <option value="-300">
		  <option value="-250">
		  <option value="-200" label="-200">
		  <option value="-150">
		  <option value="-100">
		  <option value="-50">
		  <option value="0" label="0">
		  <option value="50">
		  <option value="100">
		  <option value="150">
		  <option value="200" label="200">
		  <option value="250">
		  <option value="300">
		  <option value="350">
		  <option value="400" label="400">
		</datalist>

	  <br>
	  <input type="submit" style="height:16.6%; width:100%; font-size:1em;" value="Update">
	</form>

	<div class = "colA">
	   <form action="Aumenta.php" method="post">
	   <input type="submit" style="height:100%; width:100%; font-size:1em;" value="+100">
	  </form>
	</div>

	<div class = "colB">
      <form action="Zero.php" method="post">
      <input type="submit" style="height:100%; width:100%; font-size:1em;" value="Zero">
      </form>
	</div>

	<div class = "colC">
	  <form action="Diminui.php" method="post">
	  <input type="submit" style="height:100%; width:100%; font-size:1em;" value="-100">
	  </form>
	</div>

	<div class = "clA">
      <form action="ggraph.php" method="post">
      <input type="submit" style="height:100%; width:100%; font-size:1em;" value="Graph">
      </form>
	</div>

	<div class = "clB">
      <form action="index.html" method="post">
      <input type="submit" style="height:100%; width:100%; font-size:1em;" value="Return">
      </form>
    </div>

    </table>
    </head>
    <body>
    </body>
</html>
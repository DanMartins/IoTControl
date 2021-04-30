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

   popen("/var/www/html/FU.sh", "r");

   // No warnings but errors ON
   error_reporting(E_ERROR | E_PARSE);


   $ses = Initialize();

   $query = "SELECT ajuste FROM controle";

   $qry = odbc_exec($ses, $query);

   $result = odbc_fetch_array($qry);

   if (!$qry)
    H_Erro("odbc_exec(SELECT controle)");

   $query2 = "SELECT \"fuzN3\", \"fuzN2\", \"fuzN1\", \"fuzZ\", \"fuzP1\", \"fuzP2\", \"fuzP3\", \"defuzN3\", \"defuzN2\", \"defuzN1\", \"defuzZ\", \"defuzP1\", \"defuzP2\", \"defuzP3\"  FROM fuzzy";

   $qry2 = odbc_exec($ses, $query2);

   $result2 = odbc_fetch_array($qry2);


   if (!$qry)
    H_Erro("odbc_exec(SELECT fuzzy)");

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
	.columnA, .columnB, .columnC, .columnD, .columnE, .columnF, .columnG {
	    float: left;
	    width: 12%;
	    padding: 1%;
	    text-align:justify;
    }
    </style>
    <style>
    /* CSS property for content section */
	.colA, .colB, .colC {
	    float: left;
	    width: 33.3%;
	    height: 12vh;
	    padding: 0%;
	    text-align:center;
    }
    </style>
    <style>
    /* CSS property for content section */
	.clA, .clB {
	    float: left;
	    width: 50%;
	    height: 12vh;
	    padding: 0%;
	    text-align:center;
    }
    </style>
        <h1>Fuzzification</h1>
        <!-- Content section of website layout -->

	<table>
	<form method="post" action="fuexe.php">

            <div class = "columnA">
            <h2>N3</h2>
	            <input type="number" min="-1000" max="1000" step="0.0001" style="height:16.6%; width:100%; font-size:1em;"
	                   name="fuzN3" value="<?php echo $result2['fuzN3'] ?>">
                <p></p>
            </div>

            <div class = "columnB">
            <h2>N2</h2>
                <input type="number" min="-1000" max="1000" step="0.0001" style="height:16.6%; width:100%; font-size:1em;"
	                   name="fuzN2" value="<?php echo $result2['fuzN2'] ?>">
                <p></p>
            </div>

            <div class = "columnC">
            <h2>N1</h2>
                <input type="number" min="-1000" max="1000" step="0.0001" style="height:16.6%; width:100%; font-size:1em;"
	                   name="fuzN1" value="<?php echo $result2['fuzN1'] ?>">
                <p></p>
            </div>

            <div class = "columnD">
            <h2>Z</h2>
                <input type="number" min="-1000" max="1000" step="0.0001" style="height:16.6%; width:100%; font-size:1em;"
	                   name="fuzZ" value="<?php echo $result2['fuzZ'] ?>">
                <p></p>
            </div>

            <div class = "columnE">
            <h2>P1</h2>
                <input type="number" min="-1000" max="1000" step="0.0001" style="height:16.6%; width:100%; font-size:1em;"
	                   name="fuzP1" value="<?php echo $result2['fuzP1'] ?>">
                <p></p>
            </div>

            <div class = "columnF">
            <h2>P2</h2>
                <input type="number" min="-1000" max="1000" step="0.0001" style="height:16.6%; width:100%; font-size:1em;"
	                   name="fuzP2" value="<?php echo $result2['fuzP2'] ?>">
                <p></p>
            </div>

            <div class = "columnG">
            <h2>P3</h2>
                <input type="number" min="-1000" max="1000" step="0.0001" style="height:16.6%; width:100%; font-size:1em;"
	                   name="fuzP3" value="<?php echo $result2['fuzP3'] ?>">
                <p></p>
            </div>



          <h1>Defuzzification</h1>

            <div class = "columnA">
            <h2>N3</h2>
	            <input type="number" min="-1000" max="1000" step="0.0001" style="height:16.6%; width:100%; font-size:1em;"
	                   name="defuzN3" value="<?php echo $result2['defuzN3'] ?>">
                <p></p>
            </div>

            <div class = "columnB">
            <h2>N2</h2>
                <input type="number" min="-1000" max="1000" step="0.0001" style="height:16.6%; width:100%; font-size:1em;"
	                   name="defuzN2" value="<?php echo $result2['defuzN2'] ?>">
                <p></p>
            </div>

            <div class = "columnC">
            <h2>N1</h2>
                <input type="number" min="-1000" max="1000" step="0.0001" style="height:16.6%; width:100%; font-size:1em;"
	                   name="defuzN1" value="<?php echo $result2['defuzN1'] ?>">
                <p></p>
            </div>

            <div class = "columnD">
            <h2>Z</h2>
                <input type="number" min="-1000" max="1000" step="0.0001" style="height:16.6%; width:100%; font-size:1em;"
	                   name="defuzZ" value="<?php echo $result2['defuzZ'] ?>">
                <p></p>
            </div>

            <div class = "columnE">
            <h2>P1</h2>
                <input type="number" min="-1000" max="1000" step="0.0001" style="height:16.6%; width:100%; font-size:1em;"
	                   name="defuzP1" value="<?php echo $result2['defuzP1'] ?>">
                <p></p>
            </div>

            <div class = "columnF">
            <h2>P2</h2>
                <input type="number" min="-1000" max="1000" step="0.0001" style="height:16.6%; width:100%; font-size:1em;"
	                   name="defuzP2" value="<?php echo $result2['defuzP2'] ?>">
                <p></p>
            </div>

            <div class = "columnG">
            <h2>P3</h2>
                <input type="number" min="-1000" max="1000" step="0.0001" style="height:16.6%; width:100%; font-size:1em;"
	                   name="defuzP3" value="<?php echo $result2['defuzP3'] ?>">
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


            <input type="number" min="-400" max="400" step="10" value="<?php echo $result['ajuste'] ?>" name="ajuste"
	        for="power" oninput="power.value=ajuste.value" style="width:98%; font-size:1em; text-align:right;" >

            <input type="submit" style="width:100%; font-size:1em;" value="Update">
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
	  <form action="graphhtml.php" method="post">
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
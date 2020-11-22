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
include("phpgraphlib.php");
$osrunning = php_uname();
if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
// Force ODBCINI to define unixODBC var to the browser user.
putenv("ODBCINI=/etc/odbc.ini");

}
// No warnings but errors ON
error_reporting(E_ERROR | E_PARSE);

$datarray = array();
$ajustevel = array();
$test_dsn = "c-treeACE ODBC Driver";
if(($ses = odbc_pconnect( $test_dsn,"admin","ADMIN")) == FALSE)
{
  Handle_Error("odbc_connect()");
}


$qry = odbc_exec($ses, "SELECT grafpts FROM configura");

$info = odbc_fetch_array($qry);
$pontos = $info["grafpts"];

if (!$qry)
 Handle_Error("odbc_exec(SELECT)");

$query = "SELECT TOP " . strval($pontos);
$query = $query . "  velocidade, tempo, ajuste FROM dados ORDER BY tempo DESC";

$qry = odbc_exec($ses, $query);

$info = odbc_fetch_array($qry);
$veloc = $info["velocidade"];
$tem = substr($info["tempo"], 10, -2);
$datarray[$tem]= $veloc;
$ajustevel[$tem] = $info["ajuste"];

while ($info = odbc_fetch_array($qry)){
    $veloc = $info["velocidade"];
    $tem = substr($info["tempo"], 10, -2);
    $datarray[$tem]= $veloc;
    $ajustevel[$tem] = $info["ajuste"];
}

if (!$qry)
 Handle_Error("odbc_exec(SELECT TOP)");

odbc_close($ses);

$graph = new PHPGraphLib(1200,600);
$graph->addData(array_reverse($datarray), array_reverse($ajustevel));
//$graph->setTitle("Velocidade p/s");
$graph->setBars(false);
$graph->setLine(true);
$graph->setLineColor('red', 'blue');
//$graph->setDataPoints(true, false);
//$graph->setDataPointColor('maroon', 'white');
//$graph->setDataValues(true, false);
//$graph->setDataValueColor('maroon', 'white');
//$graph->setGoalLine(array_reverse($ajustevel));
//$graph->setGoalLineColor('red');
$graph->createGraph();


//
// Handle_Error()
//
// General error routine that retrieves and displays specific SQL Error
// before exiting the tutorial.  If the error returned indicates an object
// already exists, execution is returned to the calling function.
//

function Handle_Error($msg) {

$err = odbc_errormsg();

print("$msg - SQL ERROR: [$err] <br>\n");
print("*** Execution aborted *** <br>\n");
exit();
}

?>
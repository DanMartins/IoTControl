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
$pwmout = array();

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

$query = "WITH BottomX (velocidade, tempo, ajuste, erro) AS (SELECT TOP " . strval($pontos);
$query = $query . "  velocidade, tempo, ajuste, erro FROM dados ORDER BY tempo DESC) SELECT * FROM BottomX ORDER BY tempo ASC";

$qry = odbc_exec($ses, $query);

$info = odbc_fetch_array($qry);
$veloc = $info["velocidade"];
$tem = substr($info["tempo"], 17, -1);
$datarray[$tem]= $veloc;
$ajustevel[$tem] = $info["ajuste"];
$pwmout[$tem] = $info["erro"];

while ($info = odbc_fetch_array($qry)){
    $veloc = $info["velocidade"];
    $tem = substr($info["tempo"], 17, -1);
    $datarray[$tem]= $veloc;
    $ajustevel[$tem] = $info["ajuste"];
    $pwmout[$tem] = $info["erro"];
}

if (!$qry)
 Handle_Error("odbc_exec(SELECT TOP)");

odbc_close($ses);

$graph = new PHPGraphLib(1280,720);
$graph->addData($datarray, $pwmout, $ajustevel);
$graph->setTitle("RPM");
$graph->setTitleLocation('left');
$graph->setLegend(true);
$graph->setLegendTitle('Feedback', 'PWM', 'Control');

$graph->setBars(false);
$graph->setLine(true);
$graph->setLineColor('red', 'green', 'blue');


//$graph->setDataPoints(true, false);
//$graph->setDataPointColor('maroon', 'white');
//$graph->setDataValues(true, false);
//$graph->setDataValueColor('maroon', 'white');
//$graph->setGoalLine(25);
//$graph->setGoalLineColor('green');
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
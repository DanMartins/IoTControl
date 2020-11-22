/* %################################################################
/*% SQL - IoTControl
/*%
/*%       Tables - SQL Commands.
/*%       DanMartins
/*%       IoTControl reasearch project 
/*%       São Paulo, 2017. 
/*%
/*%################################################################
*/
create table "admin"."configura" (
	"pulsosvelcalc" integer,
	"tempovelcalc" float (8),
	"tempociclo" float (8),
	"grafpts" integer,
	"intvelfil" integer
);
set schema user;

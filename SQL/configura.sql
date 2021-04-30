/* %################################################################
/*% SQL - IoTControl
/*%
/*%       Tables - SQL Commands.
/*%       DanMartins
/*%       IoTControl reasearch project 
/*%       São Paulo, 2021. 
/*%
/*%################################################################
*/
create table "admin"."configura" (
	"pulsosvelcalc" integer,
	"tempovelcalc" float (8),
	"tempociclo" float (8),
	"grafpts" integer,
	"intvelfil" float (8),
	"pwmfreq" float (8),
	"motorzm" float (8),
	"pwmzm" float (8)
);
set schema user;

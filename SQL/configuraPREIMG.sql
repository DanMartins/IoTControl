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
	"intvelfil" float (8),
	"pwmfreq" float (8),
	"motorzm" float (8),
	"pwmzm" float (8)
)STORAGE_ATTRIBUTES 'preimg;';
insert into "admin"."configura" values('200','0.10','0.10','100','0.4','1000.0','200.0','10.0');
Commit;


set schema user;

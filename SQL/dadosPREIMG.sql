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
create table "admin"."dados" (
	"ajuste" float (8),
	"velocidade" float (8),
	"erro" float (8),
	"kp" float (8),
	"ki" float (8),
	"kd" float (8),
	"kmotor" float (8),
	"tempo" timestamp
) STORAGE_ATTRIBUTES 'preimg;';
create index "admin"."indadados" on "admin"."dados" ("ajuste");
create index "admin"."indtdados" on "admin"."dados" ("tempo");
create index "admin"."indvdados" on "admin"."dados" ("velocidade");


set schema user;

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
create table "admin"."controle" (
	"ajuste" integer,
	"valork" float (8),
	"valorki" float (8),
	"valorkd" float (8),
	"fatora" float (8),
	"fatorb" float (8),
	"fatorc" float (8)
)STORAGE_ATTRIBUTES 'preimg;';
insert into "admin"."controle" values('0','1.00','0.50','0.05','0.0004','-0.1254','32.343');
Commit;


set schema user;

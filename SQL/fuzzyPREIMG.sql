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
create table "admin"."fuzzy" (
	"fuzN3" float (8),
	"fuzN2" float (8),
	"fuzN1" float (8),
	"fuzZ" float (8),
	"fuzP1" float (8),
	"fuzP2" float (8),
	"fuzP3" float (8),
	"defuzN3" float (8),
	"defuzN2" float (8),
	"defuzN1" float (8),
	"defuzZ" float (8),
	"defuzP1" float (8),
	"defuzP2" float (8),
	"defuzP3" float (8)
)STORAGE_ATTRIBUTES 'preimg;';
insert into "admin"."fuzzy" values('-250.0','-50.0','-20.0','0.0','20.0','50.0','250.0','-10.0','-1.0','-0.1','0.0','0.1','1.0','10.0');

Commit;

set schema user;

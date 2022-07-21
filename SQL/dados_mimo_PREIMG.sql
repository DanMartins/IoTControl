create table "admin"."dados_mimo" (
	"ajuste_1" float (8),
	"ajuste_2" float (8),
	"feedback_1" float (8),
	"feedback_2" float (8),
	"out_1" float (8),
	"out_2" float (8),
	"tempo" timestamp
) STORAGE_ATTRIBUTES 'preimg;';
create index "admin"."indadados_mimo" on "admin"."dados_mimo" ("feedback_1");
create index "admin"."indtdados_mimo" on "admin"."dados_mimo" ("tempo");
create index "admin"."indvdados_mimo" on "admin"."dados_mimo" ("feedback_2");


set schema user;
commit;

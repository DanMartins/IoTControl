#comando para inspecionar registros (�ltimos 1000):
SELECT TOP 1000 velocidade, ajuste, tempo FROM dados ORDER BY tempo DESC;

#comando para inspecionar registros (�ltimos 1000 - com tempo em ordem crescente):
WITH BottomX (velocidade, erro, ajuste, tempo)
AS (SELECT TOP 1000 velocidade, erro, ajuste, tempo FROM dados ORDER BY tempo DESC)
SELECT * FROM BottomX ORDER BY tempo ASC;

WITH BottomX (velocidade, erro, ajuste, tempo) AS (SELECT TOP 1000 velocidade, erro, ajuste, tempo FROM dados ORDER BY tempo DESC) SELECT * FROM BottomX ORDER BY tempo ASC;

WITH BottomX (velocidade, erro, ajuste, tempo) AS (SELECT TOP 100 velocidade, erro, ajuste, tempo FROM dados ORDER BY tempo DESC) SELECT * FROM BottomX ORDER BY tempo ASC;

WITH BottomX (set_1, set_2, set_3, fback_1, fback_2, fback_3, out_1, out_2, out_3, tempo) AS (SELECT TOP 1000 set_1, set_2, set_3, fback_1, fback_2, fback_3, out_1, out_2, out_3, tempo FROM ball_in_tube_data ORDER BY tempo DESC) SELECT * FROM BottomX ORDER BY tempo ASC";


#comando para criar a tabela dados
#caso desejar drop e recriar (limpeza melhora performance):
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

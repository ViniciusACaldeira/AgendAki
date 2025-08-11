USE AGENDAKI;

ALTER TABLE permissao ADD COLUMN permissao_pai_id int null,
ADD FOREIGN KEY (permissao_pai_id) REFERENCES permissao(id);
 
INSERT INTO permissao ( nome ) 
VALUES ( "Administrador" ), ("Cadastro"), ("Consulta");

INSERT INTO permissao ( nome, permissao_pai_id )
VALUES ( "Funcionário", 2 ), ( "Funcionário", 3 ),
( "Serviço", 2 ), ( "Serviço", 3 ),
( "Agenda", 2 ), ( "Agenda", 3 ),
( "Agendamento", 2 ), ( "Agendamento", 3 );

INSERT INTO permissao ( nome, permissao_pai_id ) VALUES ( "Permissão", 2 ), ( "Permissão", 3 );
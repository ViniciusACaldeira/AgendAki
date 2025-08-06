use agendaki;

ALTER TABLE agenda_servico ADD COLUMN inicio TIME;
ALTER TABLE agenda_servico ADD COLUMN fim TIME;

ALTER TABLE agendamento DROP COLUMN horario;
ALTER TABLE agendamento ADD COLUMN inicio TIME;
ALTER TABLE agendamento ADD COLUMN fim TIME;

CREATE TABLE parametros
(
    nome varchar(255) primary key not null,
    valor varchar(255) not null
);

INSERT INTO parametros (nome, valor) VALUES ( 'agenda_permite_livre_digita_horario', 'true' );
INSERT INTO parametros (nome, valor) VALUES ( 'agenda_minutos_intervalo', '10' );

ALTER TABLE funcionario_servico CHANGE COLUMN tempo duracao time;
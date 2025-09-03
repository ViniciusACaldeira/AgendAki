use agendaki;

ALTER TABLE agenda ADD COLUMN quantidade_fila int not null default 0;
ALTER TABLE agenda ADD COLUMN tamanho time default "00:00";

INSERT INTO tipo_agenda (nome) VALUES ("SLOT_HIBRIDO");
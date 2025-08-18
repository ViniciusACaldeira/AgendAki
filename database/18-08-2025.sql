USE agendaki;

ALTER TABLE servico ADD COLUMN ativo bool default true;

ALTER TABLE historico_servico_valor MODIFY valor DECIMAL(10, 2); 
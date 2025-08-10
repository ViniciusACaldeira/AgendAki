use AGENDAKI;

alter table historico_servico_valor add column inicio time default '00:00';
alter table historico_servico_valor add column fim time  default '23:59';
alter table agendamento add column valor double default 0.0;

CREATE TABLE agendamento_pagamento(
	id int not null primary key auto_increment,
    agendamento_id int not null,
    data datetime not null, 
    valor double not null,
    foreign key (agendamento_id) references agendamento(id)
);
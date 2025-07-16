/*DROP DATABASE agendaki;*/
CREATE DATABASE agendaki;
USE agendaki;

CREATE TABLE usuario
(
	id int primary key auto_increment,
	nome varchar(255),
    email varchar(255) unique,
    telefone varchar(255) unique,
    senha varchar(7000)
);

CREATE TABLE permissao
(
	id int primary key auto_increment,
    nome varchar(255)
);

CREATE TABLE funcao
(
	id int primary key auto_increment,
    nome varchar(255)
);

CREATE TABLE cargo
(
	id int primary key auto_increment,
    nome varchar(255)
);

CREATE TABLE cargo_funcao
(
	id int primary key auto_increment,
    cargo_id int not null,
    funcao_id int not null,
    FOREIGN KEY (cargo_id) REFERENCES cargo(id),
    FOREIGN KEY (funcao_id) REFERENCES funcao(id)
);

CREATE TABLE funcionario
(
	id int primary key auto_increment,
    usuario_id int not null,
    foreign key (usuario_id) references usuario(id)
);

CREATE TABLE funcionario_cargo
(
	id int primary key auto_increment,
    funcionario_id int not null,
    cargo_id int not null,
    FOREIGN KEY (funcionario_id) REFERENCES funcionario(id),
    FOREIGN KEY (cargo_id) REFERENCES cargo(id)
);

CREATE TABLE funcao_permissao
(
	id int primary key auto_increment,
    funcao_id int not null,
    permissao_id int not null,
    FOREIGN KEY (funcao_id) REFERENCES funcao(id),
    FOREIGN KEY (permissao_id) REFERENCES permissao(id)
);

CREATE TABLE funcionario_permissao
(
	id int primary key auto_increment,
    funcionario_id int not null,
    permissao_id int not null,
    FOREIGN KEY (funcionario_id) REFERENCES funcionario(id),
    FOREIGN KEY (permissao_id) REFERENCES permissao(id)
);

CREATE TABLE servico
(
	id int primary key auto_increment,
    nome varchar(255),
    descricao varchar(255)
);

CREATE TABLE funcionario_servico
(
	id int primary key auto_increment,
    funcionario_id int not null,
	servico_id int not null,
    tempo time,
    FOREIGN KEY (funcionario_id) REFERENCES funcionario(id),
	FOREIGN KEY (servico_id) REFERENCES servico(id)
);

CREATE TABLE agenda
(
	id int primary key auto_increment,
    data date not null,
    inicio time not null,
    fim time not null,
    funcionario_id int not null,
    FOREIGN KEY (funcionario_id) REFERENCES funcionario(id)
);

CREATE TABLE agenda_servico
(
	id int primary key auto_increment,
    agenda_id int not null,
    servico_id int not null,
    FOREIGN KEY (agenda_id) REFERENCES agenda(id),
    FOREIGN KEY (servico_id) REFERENCES servico(id)
);

CREATE TABLE historico_servico_valor
(
	id int primary key auto_increment,
    data date,
    servico_id int not null,
    valor float not null,
    FOREIGN KEY (servico_id) REFERENCES servico(id)
);

CREATE TABLE agendamento
(
	id int primary key auto_increment,
    agenda_servico_id int not null,
    usuario_id int not null,
    horario time not null,
    FOREIGN KEY (agenda_servico_id) REFERENCES agenda_servico(id),
    FOREIGN KEY (usuario_id) REFERENCES usuario(id)
);
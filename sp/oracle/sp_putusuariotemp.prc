create or replace procedure SP_PutUsuarioTemp (
  p_operacao      in varchar2,
  p_cliente       in number,
  p_cpf           in varchar2,
  p_nome          in varchar2,
  p_nome_resumido in varchar2,
  p_sexo          in varchar2,
  p_email         in varchar2,
  p_vinculo       in number,
  p_unidade       in varchar2,
  p_sala          in varchar2,
  p_ramal         in varchar2,
  p_efetivar      in varchar2 default null,
  p_efetivado     in varchar2 default null
 ) is
begin
  if p_operacao = 'I' then
     insert into sg_autenticacao_temp
       (cliente,   cpf,   nome,   nome_resumido,   sexo,   email,   vinculo,   unidade,   sala,   ramal)
     values
       (p_cliente, p_cpf, p_nome, p_nome_resumido, p_sexo, p_email, p_vinculo, p_unidade, p_sala, p_ramal);
  elsif p_operacao = 'A' then
    update sg_autenticacao_temp
       set cliente       = p_cliente,
           cpf           = p_cpf,
           nome          = p_nome,
           nome_resumido = p_nome_resumido,
           sexo          = p_sexo,
           email         = p_email,
           vinculo       = p_vinculo,
           unidade       = p_unidade,
           sala          = p_sala,
           ramal         = p_ramal
     where cliente = p_cliente
       and cpf     = p_cpf;
  elsif p_operacao = 'E' then
    delete sg_autenticacao_temp where cliente = p_cliente and cpf = p_cpf;
  elsif p_operacao = 'T' then
    -- Guarda informação que o usuário deverá ser criado na base definitiva
    update sg_autenticacao_temp set efetivar = p_efetivar where cliente = p_cliente and cpf = p_cpf;
  end if;
end SP_PutUsuarioTemp;
/


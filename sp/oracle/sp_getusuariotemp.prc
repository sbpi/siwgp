create or replace procedure SP_GetUsuarioTemp(
  p_cliente   in number,
  p_cpf       in varchar2 default null,
  p_efetivado in varchar2 default null,
  p_result    out sys_refcursor
 ) is
begin
  open p_result for
    select a.cliente, a.cpf, a.nome, a.nome_resumido, a.sexo, a.email, a.vinculo, a.unidade,
           a.sala, a.ramal, a.efetivar, a.efetivado, a.efetivacao
      from sg_autenticacao_temp a
     where a.cliente    = p_cliente
       and (p_cpf       is null or (p_cpf       is not null and a.cpf = p_cpf))
       and (p_efetivado is null or (p_efetivado is not null and a.efetivado = p_efetivado));
end SP_GetUsuarioTemp;
/


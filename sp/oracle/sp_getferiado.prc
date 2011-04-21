create or replace procedure SP_GetFeriado
   (p_cliente             in  number,
    p_cidade              in  number    default null,
    p_chave               in  number    default null,
    p_data                in  date      default null,
    p_nome                in  varchar2  default null,
    p_tipo                in  varchar2  default null,
    p_result      out sys_refcursor) is
begin
   -- Recupera os feriados a partir dos parâmetros informados
   open p_result for
      select null sq_feriado, null nome, null tipo, null sq_cidade
        from dual
       where 1 = 0;
end SP_GetFeriado;
/


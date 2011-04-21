create or replace procedure SP_PutInformar
   (p_chave           in number default null,
    p_sq_cidade       in number default null,
    p_inicio_real     in date,
    p_fim_real        in date,
    p_limite_passagem in varchar2
   ) is
begin
   -- Altera registro
   update pj_projeto set
          inicio_real        = p_inicio_real,
          fim_real           = p_fim_real,
          sq_cidade          = p_sq_cidade,
          limite_passagem    = p_limite_passagem
    where sq_siw_solicitacao = p_chave;

end SP_PutInformar;
/


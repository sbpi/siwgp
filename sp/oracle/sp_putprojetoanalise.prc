create or replace procedure SP_PutProjetoAnalise
   (p_chave           in number default null,
    p_analise1        in varchar2 default null,
    p_analise2        in varchar2 default null,
    p_analise3        in varchar2 default null,
    p_analise4        in varchar2 default null
   ) is
begin
   -- Altera registro
   update pj_projeto set
          analise1        = p_analise1,
          analise2        = p_analise2,
          analise3        = p_analise3,
          analise4        = p_analise4
    where sq_siw_solicitacao = p_chave;

end SP_PutProjetoAnalise;
/


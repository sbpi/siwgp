create or replace procedure SP_PutConfPJ
   (p_operacao                 in varchar2,
    p_cliente                  in number,
    p_siw_solicitacao          in number   default null,
    p_exibe_relatorio          in varchar2 default null
   ) is
begin
   -- Altera registro
   update pj_projeto
      set exibe_relatorio = p_exibe_relatorio
   where sq_siw_solicitacao = p_siw_solicitacao;
end SP_PutConfPJ;
/


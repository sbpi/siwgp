create or replace procedure sp_putProgramaQualit
   (p_chave                    in  number,
    p_descricao                in  varchar2 default null,
    p_justificativa            in  varchar2 default null,
    p_publico_alvo             in  varchar2 default null,
    p_estrategia               in  varchar2 default null,
    p_observacao              in  varchar2 default null
   ) is
begin
   -- Altera os registro
   update siw_solicitacao set
      descricao     = trim(p_descricao),
      justificativa = trim(p_justificativa),
      observacao    = trim(p_observacao)
   where sq_siw_solicitacao = p_chave;

   update pe_programa set
      publico_alvo  = trim(p_publico_alvo),
      estrategia    = trim(p_estrategia)
   where sq_siw_solicitacao = p_chave;
end sp_putProgramaQualit;
/


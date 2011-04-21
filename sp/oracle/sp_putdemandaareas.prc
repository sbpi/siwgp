create or replace procedure SP_PutDemandaAreas
   (p_operacao            in  varchar2,
    p_chave               in number,
    p_chave_aux           in number    default null,
    p_papel               in varchar2  default null
   ) is
begin
   If p_operacao = 'I' Then -- Inclusão
      -- Insere registro na tabela de áreas envolvidas
      Insert Into gd_demanda_envolv ( sq_unidade,  sq_siw_solicitacao, papel )
      (select p_chave_aux, p_chave, trim(p_papel)
         from dual
        where 0 = (select count(*) from gd_demanda_envolv where sq_unidade = p_chave_aux and sq_siw_solicitacao = p_chave)
      );
   Elsif p_operacao = 'A' Then -- Alteração
      -- Atualiza a tabela de áreas envolvidas
      Update gd_demanda_envolv set
          papel            = trim(p_papel)
      where sq_siw_solicitacao = p_chave
        and sq_unidade         = p_chave_aux;
   Elsif p_operacao = 'E' Then -- Exclusão
      -- Remove o registro na tabela de áreas envolvidas
      delete gd_demanda_envolv
       where sq_siw_solicitacao = p_chave
         and sq_unidade         = p_chave_aux;
   End If;
end SP_PutDemandaAreas;
/


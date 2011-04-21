create or replace procedure SP_PutDemandaInter
   (p_operacao            in  varchar2,
    p_chave               in number,
    p_chave_aux           in number    default null,
    p_tipo_visao          in varchar2  default null,
    p_envia_email         in varchar2  default null
   ) is
begin
   If p_operacao = 'I' Then -- Inclusão
      -- Insere registro na tabela de interessados
      Insert Into gd_demanda_interes
         ( sq_pessoa,   sq_siw_solicitacao, tipo_visao,    envia_email )
      Values
         (p_chave_aux,  p_chave,            p_tipo_visao,  p_envia_email );
   Elsif p_operacao = 'A' Then -- Alteração
      -- Atualiza a tabela de solicitações
      Update gd_demanda_interes set
          tipo_visao       = p_tipo_visao,
          envia_email      = p_envia_email
      where sq_siw_solicitacao = p_chave
        and sq_pessoa          = p_chave_aux;
   Elsif p_operacao = 'E' Then -- Exclusão
      -- Remove o registro na tabela de demandas
      delete gd_demanda_interes
       where sq_siw_solicitacao = p_chave
         and sq_pessoa          = p_chave_aux;
   End If;
end SP_PutDemandaInter;
/


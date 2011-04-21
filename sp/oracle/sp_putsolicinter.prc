create or replace procedure SP_PutSolicInter
   (p_operacao            in  varchar2,
    p_chave               in number   default null,
    p_pessoa              in number   default null,
    p_sq_tipo_interessado in number   default null,
    p_envia_email         in varchar2 default null,
    p_tipo_visao          in number   default null
   ) is
   w_cont number(18);
begin
   If p_operacao <> 'I' Then
      -- Se for altera��o ou exclus�o, faz o tratamento para migra��o do formato antigo de interessados para o formato novo
      select count(a.sq_solicitacao_interessado) into w_cont from siw_solicitacao_interessado a where a.sq_siw_solicitacao = p_chave and a.sq_pessoa = p_pessoa;

      -- Se n�o existe na nova tabela � porqu� precisa migrar
      If w_cont = 0 Then
         If p_operacao = 'A' Then
            -- Insere registro na nova tabela de interessados
            insert into siw_solicitacao_interessado
               (sq_solicitacao_interessado,         sq_siw_solicitacao, sq_pessoa,   sq_tipo_interessado,   envia_email,   tipo_visao)
            values
               (sq_solicitacao_interessado.nextval, p_chave,            p_pessoa,    p_sq_tipo_interessado, p_envia_email, p_tipo_visao);
         End If;

         -- Remove das tabelas antigas
         delete gd_demanda_interes a where a.sq_siw_solicitacao = p_chave and a.sq_pessoa = p_pessoa;
         delete pj_projeto_interes a where a.sq_siw_solicitacao = p_chave and a.sq_pessoa = p_pessoa;
      End If;
   End If;

   If p_operacao = 'I' Then -- Inclus�o
      -- Insere registro na tabela de interessados
      insert into siw_solicitacao_interessado
         (sq_solicitacao_interessado,         sq_siw_solicitacao, sq_pessoa, sq_tipo_interessado,   envia_email,   tipo_visao)
      values
         (sq_solicitacao_interessado.nextval, p_chave,            p_pessoa,  p_sq_tipo_interessado, p_envia_email, p_tipo_visao);
   Elsif p_operacao = 'A' Then -- Altera��o
      -- Atualiza a tabela de interessados da solicita��o
      update siw_solicitacao_interessado set
          sq_tipo_interessado = p_sq_tipo_interessado,
          envia_email         = p_envia_email,
          tipo_visao          = p_tipo_visao
      where sq_siw_solicitacao = p_chave
        and sq_pessoa          = p_pessoa;
   Elsif p_operacao = 'E' Then -- Exclus�o
      -- Remove o registro na tabela de interessados da solicita��o
      delete siw_solicitacao_interessado
       where sq_siw_solicitacao = p_chave
         and sq_pessoa          = p_pessoa;
   End If;
end SP_PutSolicInter;
/


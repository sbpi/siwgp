create or replace procedure sp_putIndicador
   (p_operacao          in  varchar2,
    p_cliente           in  number   default null,
    p_chave             in  number   default null,
    p_nome              in  varchar2 default null,
    p_sigla             in  varchar2 default null,
    p_tipo_indicador    in  number   default null,
    p_unidade_medida    in  number   default null,
    p_descricao         in  varchar2 default null,
    p_forma_afericao    in  varchar2 default null,
    p_fonte_comprovacao in  varchar2 default null,
    p_ciclo_afericao    in  varchar2 default null,
    p_vincula_meta      in  varchar2 default null,
    p_exibe_mesa        in  varchar2 default null,
    p_ativo             in  varchar2 default null
   ) is
   w_chave number(18);
begin
   If p_operacao = 'I' Then
      -- Recupera a próxima chave do registro
      select sq_eoindicador.nextval into w_chave from dual;

      -- Insere registro
      insert into eo_indicador
        (sq_eoindicador,   cliente,   sq_tipo_indicador, sq_unidade_medida, nome,   sigla,   descricao,   forma_afericao,   fonte_comprovacao,
         ciclo_afericao,   ativo,     vincula_meta,      exibe_mesa)
      values
        (w_chave,          p_cliente, p_tipo_indicador,  p_unidade_medida,  p_nome, p_sigla, p_descricao, p_forma_afericao, p_fonte_comprovacao,
         p_ciclo_afericao, p_ativo,   p_vincula_meta,    p_exibe_mesa);
   Elsif p_operacao = 'A' Then
      update eo_indicador
         set sq_tipo_indicador = p_tipo_indicador,
             sq_unidade_medida = p_unidade_medida,
             nome              = p_nome,
             sigla             = p_sigla,
             descricao         = p_descricao,
             forma_afericao    = p_forma_afericao,
             fonte_comprovacao = p_fonte_comprovacao,
             ciclo_afericao    = p_ciclo_afericao,
             vincula_meta      = p_vincula_meta,
             exibe_mesa        = p_exibe_mesa,
             ativo             = p_ativo
       where sq_eoindicador = p_chave;
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete eo_indicador where sq_eoindicador = p_chave;
   End If;
end sp_putIndicador;
/


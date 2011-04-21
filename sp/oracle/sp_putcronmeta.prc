create or replace procedure SP_PutCronMeta
   (p_operacao            in  varchar2,
    p_usuario             in number    default null,
    p_chave               in number    default null,
    p_chave_aux           in number    default null,
    p_inicio              in date      default null,
    p_fim                 in date      default null,
    p_valor_previsto      in number    default null,
    p_valor_real          in number    default null
   ) is
   w_chave    number(18);
   w_pai      number(18);
begin
   If p_operacao = 'I' Then -- Inclusão
      -- Recupera a próxima chave
      select sq_meta_cronograma.nextval into w_chave from dual;

      -- Insere registro na tabela de cronograma da meta
      Insert Into siw_meta_cronograma
         ( sq_meta_cronograma,      sq_solic_meta,  inicio,        fim,
           valor_previsto,          valor_real)
      Values
         ( w_chave,                 p_chave,              p_inicio,    p_fim,
           p_valor_previsto,        p_valor_real);
   Elsif p_operacao = 'A' Then
      -- Alteração do cronograma quando o projeto está na fase de cadastramento
      Update siw_meta_cronograma set
          inicio                    = p_inicio,
          fim                       = p_fim,
          valor_previsto            = p_valor_previsto,
          valor_real                = p_valor_real
      where sq_meta_cronograma   = p_chave_aux;

   Elsif p_operacao = 'V' Then
      -- Alteração do cronograma quando o projeto está na fase de execução
      Update siw_meta_cronograma
         set valor_real            = p_valor_real,
             ultima_atualizacao    = sysdate,
             sq_pessoa_atualizacao = p_usuario
       where sq_meta_cronograma = p_chave_aux;
   Elsif p_operacao = 'E' Then -- Exclusão
      -- Remove o registro na tabela de cronograma da meta
      delete siw_meta_cronograma where sq_meta_cronograma = p_chave_aux;

   End If;

end SP_PutCronMeta;
/


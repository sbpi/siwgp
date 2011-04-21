create or replace procedure SP_PutCronograma
   (p_operacao            in  varchar2,
    p_chave               in number,
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
      select sq_rubrica_cronograma.nextval into w_chave from dual;

      -- Insere registro na tabela de etapas do projeto
      Insert Into pj_rubrica_cronograma
         ( sq_rubrica_cronograma,   sq_projeto_rubrica,  inicio,        fim,
           valor_previsto,          valor_real)
      Values
         ( w_chave,                 p_chave,              p_inicio,    p_fim,
           p_valor_previsto,        p_valor_real);
   Elsif p_operacao = 'A' Then
      -- Alteração do cronograma quando o projeto está na fase de cadastramento
      Update pj_rubrica_cronograma set
          inicio                    = p_inicio,
          fim                       = p_fim,
          valor_previsto            = p_valor_previsto,
          valor_real                = p_valor_real
      where sq_rubrica_cronograma   = p_chave_aux;

   Elsif p_operacao = 'V' Then
      -- Alteração do cronograma quando o projeto está na fase de execução
      Update pj_rubrica_cronograma set valor_real = p_valor_real where sq_rubrica_cronograma = p_chave_aux;

   Elsif p_operacao = 'E' Then -- Exclusão
      -- Remove o registro na tabela de cronograma da rubrica
      delete pj_rubrica_cronograma where sq_rubrica_cronograma = p_chave_aux;

   End If;

end SP_PutCronograma;
/


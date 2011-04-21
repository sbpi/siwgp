create or replace procedure SP_PutEtapaMensal
   (p_operacao            in varchar2,
    p_chave               in number,
    p_quantitativo        in number,
    p_referencia          in date
   ) is
begin
   if p_operacao = 'E' Then
      -- Apaga todos os registros para que seja feia a atualização
      delete pj_etapa_mensal where sq_projeto_etapa = p_chave;
   Else
      -- Insere registro na tabela de meses da etapa
      Insert Into pj_etapa_mensal
         ( sq_projeto_etapa, referencia,   execucao_fisica, execucao_financeira)
         Values
         ( p_chave,          last_day(p_referencia), p_quantitativo,  0);
   End If;
end SP_PutEtapaMensal;
/


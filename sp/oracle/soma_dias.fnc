create or replace function SOMA_DIAS
  (p_cliente   in number,
   data_inicio in date,
   dias        in number,
   contagem    in varchar2)
   return date is
/**********************************************************************************
* Nome      : soma_dias
* Finalidade: Retorna a data fim a partir da data inicio e o número de dias
* Autor     : Alexandre Vinhadelli Papadópolis
* Data      : 29/07/2008, 14:00
* Parâmetros:
*    data_inicio   : data inicial
*    dias          : número de dias a ser incrementado
*    contagem      : forma da contagem dos dias (C -> corridos, U -> úteis
* Retorno: data inicial acrescida do número de dias (corridos/úteis) informado
***********************************************************************************/
w_atual   date := to_date(to_char(data_inicio,'dd/mm/yyyy')||'000000','dd/mm/yyyyhh24miss');
w_dias    number(10,1) := 1;
begin
  If upper(contagem) = 'C' Then
     w_atual := w_atual + dias;
  Else
     If dias >= 0 Then
        -- Se for contagem progressiva
         While w_dias <= dias Loop
           -- Incrementa a data atual
           w_atual := w_atual + 1;

           -- Verifica se pode incrementar o contador de dias
           If to_char(w_atual,'d') not in (1,7) Then
              If verificaDataEspecial(w_atual,p_cliente) <> 'N' Then
                 w_dias := w_dias + 1;
              End If;
           End If;
         End Loop;
     Else
        w_dias := -1;
        -- Se for contagem regressiva
         While w_dias >= dias Loop
           -- Incrementa a data atual
           w_atual := w_atual - 1;

           -- Verifica se pode decrementar o contador de dias
           If to_char(w_atual,'d') not in (1,7) Then
              If verificaDataEspecial(w_atual,p_cliente) <> 'N' Then
                 w_dias := w_dias - 1;
              End If;
           End If;
         End Loop;
     End If;
  End If;

  Return w_atual;
end SOMA_DIAS;
/


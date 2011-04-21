create or replace function fValor
  (p_Valor    in Varchar2,             -- Valor a ser convertido
   p_Tipo     in Char,                 -- Tipo da conversão: N (texto->numero) ou T (numero->texto)
   p_Precisao in Number   Default null,-- Número desejado de casas decimais (padrão 2)
   p_Negativo in Varchar2 Default null,-- Exibição de números negativos: '-' ou '()' (padrão -)
   p_Moeda    in Varchar2 Default null -- Indicador de moeda: R$, US$ ... (padrao '')
  ) return varchar2 is
  Result      Varchar2(100);
  w_Tipo      Char(1)      := upper(p_Tipo);
  w_Decimal   Varchar2(20) := lpad('0', Nvl(p_Precisao,2), '0');
  w_Negativo  Varchar2(2) := null;
  w_Separador Varchar2(2);
  w_Moeda     Varchar2(1)  := '';
  w_frmt      Varchar2(40);
  w_nlsparam  Varchar2(60) := null;
begin
  If p_Moeda is not null Then w_Moeda := 'L';     End If;
  If p_Negativo = '()'   Then w_Negativo := 'PR'; End If;
  If w_Tipo = 'N' Then
     w_frmt      := '999999999999D'||w_decimal;
     w_nlsparam  := 'NLS_NUMERIC_CHARACTERS = ''.,''';
  Else
     w_frmt      := w_Moeda||'999G999G999G990D'||w_decimal||Nvl(w_Negativo,'');
     If p_Moeda is null Then
        w_nlsparam  := 'NLS_NUMERIC_CHARACTERS = '',.''';
     Else
        w_nlsparam  := 'NLS_NUMERIC_CHARACTERS = '',.'' NLS_CURRENCY = '''||p_Moeda||'''';
     End If;
  End If;
  Result      := to_char(p_Valor, w_frmt, w_nlsparam);
  return(Result);
end fValor;
/


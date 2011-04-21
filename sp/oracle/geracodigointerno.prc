create or replace procedure GeraCodigoInterno
   (p_solicitacao in  number,
    p_ano         in  number default null,
    p_codigo      out varchar2
   ) is
   Result       varchar2(60);
   w_ano        number(4) := coalesce(p_ano,to_char(sysdate,'yyyy'));
   w_sequencial number(18) := 0;
   w_existe     number(4);
   w_menu       number(18);
   w_reg        siw_menu%rowtype;
begin
  -- Verifica é possível gerar um código para a solicitação
  select count(*) into w_existe
    from siw_solicitacao     a
         inner join siw_menu b on (a.sq_menu = b.sq_menu)
   where a.sq_siw_solicitacao = p_solicitacao
     and 0                    < coalesce(b.numeracao_automatica,0);

  If w_existe = 0 Then
     -- Se não for possível, retorna nulo
     p_codigo := null;
     return;
  Else
     -- Se for possível gerar o número, recupera o serviço a ser usado para numeração
     select case b.numeracao_automatica
                 when 1 then b.sq_menu -- Se o serviço tiver numeração própria
                 when 2 then c.sq_menu -- Se o serviço usar numeração de outro serviço
            end
       into w_menu
       from siw_solicitacao     a
            inner  join siw_menu b on (a.sq_menu           = b.sq_menu)
              left join siw_menu c on (b.servico_numerador = c.sq_menu)
      where a.sq_siw_solicitacao = p_solicitacao;

     select * into w_reg from siw_menu where sq_menu = w_menu;
  End If;

  -- Recupera o próximo número do ano informado.
  -- Se for do ano corrente, atualiza o sequencial. Caso contrário, deixa como está
  If w_ano >= w_reg.ano_corrente Then
     -- Verifica se há necessidade de reinicializar o sequencial em função da troca do ano
     If to_char(sysdate,'yyyy') > w_reg.ano_corrente Then
        -- Configura o ano do acordo para o ano informado corrente
        w_ano        := to_char(sysdate,'yyyy');
        w_sequencial := 1;
     Else
        w_ano        := w_reg.ano_corrente;
        w_sequencial := w_reg.sequencial + 1;
     End If;

     -- Atualiza a tabela de parâmetros
     Update siw_menu Set
         ano_corrente = w_ano,
         sequencial   = w_sequencial
     Where sq_menu = w_reg.sq_menu;
  Else
     -- Verifica se já há alguma solicitação com código interno gerado no ano informado.
     -- Se tiver, verifica o próximo sequencial. Caso contrário, usa 1.
     select count(*) into w_existe
       from siw_solicitacao      a
            inner join siw_menu  b on (a.sq_menu = b.sq_menu)
      where instr(a.codigo_interno,'/'||w_ano) > 0
        and b.sq_menu                = w_reg.sq_menu;

     If w_existe = 0 Then
        w_sequencial := 1;
     Else
        select nvl(max(replace(translate(a.codigo_interno,'0123456789ABCDEFGHIJKLMNOPQRSTUVWXZ-:. ','0123456789'),'/'||w_ano,'')),0)+1
          into w_sequencial
          from siw_solicitacao      a
               inner join siw_menu  b on (a.sq_menu = b.sq_menu)
         where instr(a.codigo_interno,'/'||w_ano) > 0
           and b.sq_menu                = w_reg.sq_menu;
     End If;
  End If;

  --  Retorna o sequencial a ser usado no acordo
  Result := Nvl(w_reg.prefixo,'')||w_sequencial||'/'||w_ano||Nvl(w_reg.sufixo,'');

  p_codigo := Result;

end GeraCodigoInterno;
/


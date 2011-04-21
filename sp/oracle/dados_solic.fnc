create or replace function dados_solic(p_chave in number) return varchar2 is
/**********************************************************************************
* Nome      : dados_solic
* Finalidade: Recuperar informa��es de uma solicita��o
* Autor     : Alexandre Vinhadelli Papad�polis
* Data      : 21/06/2007, 12:30
* Par�metros:
*    p_chave : chave prim�ria de SIW_SOLICITACAO
* Retorno: se a solicita��o n�o existir, retorna nulo
*          se a solicita��o existir, retorna string contendo informa��es sobre ela.
*          A string cont�m v�rios peda�os separados por |@|
*          1  - string para exibi��o em listagens, composta da sigla do m�dulo e do c�digo da solicita��o
*          2  - codigo da solicita��o
*          3  - titulo da solicita��o
*          4  - siw_menu.sq_menu - chave do menu ao qual a solicita��o est� ligada ()
*          5  - siw_menu.nome    - nome do menu
*          6  - siw_menu.sigla   - sigla do menu
*          7  - siw_menu.p1      - valor de p1
*          8  - siw_menu.p2      - valor de p2
*          9  - siw_menu.p3      - valor de p3
*          10 - siw_menu.p4      - valor de p4
*          11 - siw_menu.link    - link para a rotina de visualiza��o
*          12 - siw_modulo.sigla - sigla do m�dulo da solicita��o
***********************************************************************************/
  Result varchar2(32767) := null;
  w_reg  number(18);

  cursor c_dados is
     select a.sq_menu, a.nome, a.sigla, coalesce(to_char(a.p1),'') as p1, coalesce(to_char(a.p2),'') as p2, coalesce(to_char(a.p3),'') as p3, coalesce(to_char(a.p4),'') as p4,
            coalesce(a1.link, replace(lower(a.link),'inicial','visual')) as link,
            a2.sigla as sg_modulo,
            b.sq_siw_solicitacao,
            coalesce(b.codigo_interno, to_char(b.sq_siw_solicitacao)) as codigo,
            coalesce(b.titulo, d.assunto, b.descricao, b.justificativa) as titulo
       from siw_menu                             a
            left  join siw_menu                  a1 on (a.sq_menu             = a1.sq_menu_pai and
                                                        a1.sigla              like '%VISUAL%'
                                                       )
            inner join siw_modulo                a2 on (a.sq_modulo           = a2.sq_modulo)
            inner join siw_solicitacao           b  on (a.sq_menu             = b.sq_menu)
            left  join gd_demanda                d  on (b.sq_siw_solicitacao = d.sq_siw_solicitacao)
      where b.sq_siw_solicitacao = p_chave;
begin
  if p_chave is not null then
     -- Verifica se a solicita��o existe e, se existir, recupera seus dados
     select count(sq_siw_solicitacao) into w_reg from siw_solicitacao where sq_siw_solicitacao = p_chave;
     if w_reg > 0 then
        for crec in c_dados loop
            Result := crec.nome||': '||crec.codigo||'|@|'||crec.codigo||'|@|'||crec.titulo||'|@|'||crec.sq_menu||'|@|'||crec.nome||'|@|'||crec.sigla||'|@|'||crec.p1||'|@|'||crec.p2||'|@|'||crec.p3||'|@|'||crec.p4||'|@|'||crec.link||'|@|'||crec.sg_modulo;
        end loop;
     end if;
  end if;
  return(Result);
end dados_solic;
/


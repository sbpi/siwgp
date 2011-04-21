create or replace procedure sp_ajustaPesoEtapa(p_projeto in number default null, p_pai in number default null, p_todos in varchar2 default null) is
  w_existe number(18);

  -- Cursor que recupera todos os projetos
  cursor c_projetos is
    select a.sq_siw_solicitacao
           from pj_projeto a
                inner join pj_projeto_etapa b on (a.sq_siw_solicitacao = b.sq_siw_solicitacao);


  cursor c_raiz is
    select a.sq_projeto_etapa, a.peso/(case b.peso when 0 then 1 else b.peso end) as peso_projeto
           from pj_projeto_etapa a,
                (select sum(peso) as peso
                   from pj_projeto_etapa
                  where sq_siw_solicitacao = p_projeto
                    and sq_etapa_pai       is null
                )                b
          where a.sq_siw_solicitacao = p_projeto
            and a.sq_etapa_pai       is null;

  cursor c_nivel is
    select a.sq_projeto_etapa,
           a.peso/(case b.peso when 0 then 1 else b.peso end) as peso_pai,
           (a1.peso_projeto * (a.peso/(case b.peso when 0 then 1 else b.peso end))) as peso_projeto
           from pj_projeto_etapa            a
                inner join pj_projeto_etapa a1 on (a.sq_etapa_pai = a1.sq_projeto_etapa),
                (select sum(peso) as peso
                   from pj_projeto_etapa
                  where sq_siw_solicitacao = p_projeto
                    and sq_etapa_pai       = p_pai
                )                b
          where a.sq_siw_solicitacao = p_projeto
            and a.sq_etapa_pai       = p_pai;
begin
  If p_projeto is null Then
    If coalesce(p_todos,'nulo') <> 'TODOS' Then
       return;
    Else
       -- Atualiza os pesos relativos de todos os projetos
       for crec in c_projetos loop
          sp_ajustaPesoEtapa(crec.sq_siw_solicitacao,null);
       end loop;
    End If;
  Elsif p_projeto is not null Then
     select count(sq_siw_solicitacao) into w_existe from pj_projeto where sq_siw_solicitacao = p_projeto;
     If w_existe = 0 Then
        return;
     End If;
  End If;

  if p_pai is null then
     for crec in c_raiz loop
         update pj_projeto_etapa set peso_pai = peso, peso_projeto = crec.peso_projeto where sq_projeto_etapa = crec.sq_projeto_etapa;
         sp_ajustaPesoEtapa(p_projeto, crec.sq_projeto_etapa);
     end loop;
  Else
     for crec in c_nivel loop
         update pj_projeto_etapa set peso_pai = crec.peso_pai, peso_projeto = crec.peso_projeto where sq_projeto_etapa = crec.sq_projeto_etapa;
         sp_ajustaPesoEtapa(p_projeto, crec.sq_projeto_etapa);
     end loop;
  end if;
end sp_ajustaPesoEtapa;
/


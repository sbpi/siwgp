create or replace view vw_tarefas_projeto as
select s.id_programa,
       s.programa,
       s.sq_siw_solicitacao,
       s.titulo,
       s.id_responsavel,
       s.responsavel,
       s.id_unidade_responsavel,
       s.unidade_responsavel,
       et.ordem,
       et.sq_etapa_pai,
       et.sq_projeto_etapa,
       et.titulo as tarefa,
       co.nome as responsavel_tarefa,       
       et.descricao,
       et.inicio_previsto,
       et.fim_previsto,
       et.inicio_real,
       et.fim_real,
       et.perc_conclusao,
       to_char(ind.ide,'990D00') as ide,
       to_char(ind.idc,'990D00') as idc,
       to_char(ind.ige,'990D00') as ige,
       to_char(ind.igc,'990D00') as igc,
       s.id_plano_estrategico,
       s.plano_estrategico,
       case
            when inicio_real is not null and fim_real is not null then 1 -- concluído
            when inicio_real is not null and fim_real is null then 2     -- em execução
            when inicio_real is  null and fim_real is null then 3        -- execução não iniciada
       end as sq_fase,
       case
            when inicio_real is not null and fim_real is not null then 'Concluído'
            when inicio_real is not null and fim_real is null then 'Em Execução'
            when inicio_real is  null and fim_real is null then 'Execução não iniciada'
       end as fase,
       case
           when inicio_real is not null and fim_real is not null and fim_real = fim_previsto then 1 --'Dentro do prazo'
           when inicio_real is not null and fim_real is not null and fim_real < fim_previsto then 2 --'Antes do prazo'
           when inicio_real is not null and fim_real is not null and fim_real > fim_previsto then 3 --'Atrasado'
           when (( (inicio_real is not null and fim_real is null) OR (inicio_real is  null and fim_real is null) ) and  fim_previsto >= sysdate+5) then 1 --'Prazo final dentro do previsto'
           when (( (inicio_real is not null and fim_real is null) OR (inicio_real is  null and fim_real is null) ) and  fim_previsto > sysdate) then 4 --'Fim previsto próximo'
           when (( (inicio_real is not null and fim_real is null) OR (inicio_real is  null and fim_real is null) ) and  fim_previsto < sysdate) then 3 --'Fim previsto superado'
       end as sq_status,
       case
           when ((inicio_real is not null and fim_real is not null) and (fim_real = fim_previsto)) then 'Dentro do prazo'
           when inicio_real is not null and fim_real is not null and fim_real < fim_previsto then 'Antes do prazo'
           when inicio_real is not null and fim_real is not null and fim_real > fim_previsto then 'Atrasado'
           when (( (inicio_real is not null and fim_real is null) OR (inicio_real is  null and fim_real is null) ) and  fim_previsto >= sysdate+5) then 'Dentro do prazo'
           when (( (inicio_real is not null and fim_real is null) OR (inicio_real is  null and fim_real is null) ) and  fim_previsto > sysdate) then 'Próximo do prazo'
           when (( (inicio_real is not null and fim_real is null) OR (inicio_real is  null and fim_real is null) ) and  fim_previsto < sysdate) then 'Atrasado'
       end as status
  from pj_projeto_etapa et, vw_projeto s, vw_indicador_projeto ind, co_pessoa co
 where s.sq_siw_solicitacao = et.sq_siw_solicitacao
 and ind.sq_siw_solicitacao = s.sq_siw_solicitacao
 and et.pacote_trabalho = 'S'
 and et.sq_pessoa = co.sq_pessoa;
 

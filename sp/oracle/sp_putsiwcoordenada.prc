create or replace procedure SP_PutSiwCoordenada
   (p_operacao            in varchar2,
    p_chave               in number,
    p_cliente             in number,
    p_sq_pessoa           in number,
    p_tipo                in varchar2,
    p_nome                in varchar2 default null,
    p_latitude            in number    default null,
    p_longitude           in number    default null,
    p_icone               in varchar2  default null
   ) is
   w_existe     number(18);
   w_coordenada number(18);
   w_operacao   varchar2(1);
begin
   If p_tipo = 'ENDERECO' Then
      If p_operacao = 'E' Then
         -- Recupera a chave da tabela de coordenadasd
         select sq_siw_coordenada into w_coordenada from siw_coordenada_endereco where sq_pessoa_endereco = p_chave;

         -- Remove vinculação entre coordenadas e endereços
         delete siw_coordenada_endereco
          where sq_siw_coordenada = w_coordenada
            and sq_pessoa_endereco = p_chave;

         -- Remove a coordenada
         delete siw_coordenada where sq_siw_coordenada = w_coordenada;
      Else
         -- Verifica se o endereço já tem coordenada associada
         select count(*) into w_existe from siw_coordenada_endereco where sq_pessoa_endereco = p_chave;

         If w_existe > 0 Then
            -- Se existir, recupera a chave existente e configura para alteração
            select sq_siw_coordenada into w_coordenada from siw_coordenada_endereco where sq_pessoa_endereco = p_chave;
            w_operacao := 'A';
         Else
            -- Se não existir, gera nova chave a partir da sequence e configura para inclusão
            select sq_siw_coordenada.nextval into w_coordenada from dual;
            w_operacao := 'I';
         End If;

         If w_operacao = 'I' Then
            -- Insere o registro na tabela de coordenadas
            insert into siw_coordenada
              (sq_siw_coordenada, cliente,   nome,   latitude,   longitude,   icone,   tipo)
            values
              (w_coordenada,      p_cliente, p_nome, p_latitude, p_longitude, p_icone, p_tipo);

            -- Cria vínculo entre a coordenada e o endereço
            insert into siw_coordenada_endereco (sq_siw_coordenada, sq_pessoa_endereco)
            values (w_coordenada, p_chave);
         Else
            -- Atualiza os dados da coordenada
            update siw_coordenada set
               nome      = p_nome,
               latitude  = p_latitude,
               longitude = p_longitude,
               icone     = p_icone,
               tipo      = p_tipo
             where sq_siw_coordenada = w_coordenada;
         End If;
      End If;
   Elsif p_tipo = 'PROJETO' Then
      If p_operacao = 'E' Then
         -- Recupera a chave da tabela de coordenadas
         select sq_siw_coordenada into w_coordenada from siw_coordenada_solicitacao where sq_siw_solicitacao = p_chave;

         -- Remove vinculação entre coordenadas e a solicitação
         delete siw_coordenada_solicitacao
          where sq_siw_coordenada  = w_coordenada
            and sq_siw_solicitacao = p_chave;

         -- Remove a coordenada
         delete siw_coordenada where sq_siw_coordenada = w_coordenada;
      Else
         -- Verifica se a solicitação já tem coordenada associada
         select count(*) into w_existe from siw_coordenada_solicitacao where sq_siw_solicitacao = p_chave;

         If w_existe > 0 Then
            -- Se existir, recupera a chave existente e configura para alteração
            select sq_siw_coordenada into w_coordenada from siw_coordenada_solicitacao where sq_siw_solicitacao = p_chave;
            w_operacao := 'A';
         Else
            -- Se não existir, gera nova chave a partir da sequence e configura para inclusão
            select sq_siw_coordenada.nextval into w_coordenada from dual;
            w_operacao := 'I';
         End If;

         If w_operacao = 'I' Then
            -- Insere o registro na tabela de coordenadas
            insert into siw_coordenada
              (sq_siw_coordenada, cliente,   nome,   latitude,   longitude,   icone,   tipo)
            values
              (w_coordenada,      p_cliente, p_nome, p_latitude, p_longitude, p_icone, p_tipo);

            -- Cria vínculo entre a coordenada e a solicitação
            insert into siw_coordenada_solicitacao (sq_siw_coordenada, sq_siw_solicitacao)
            values (w_coordenada, p_chave);
         Else
            -- Atualiza os dados da coordenada
            update siw_coordenada set
               nome      = p_nome,
               latitude  = p_latitude,
               longitude = p_longitude,
               icone     = p_icone,
               tipo      = p_tipo
             where sq_siw_coordenada = w_coordenada;
         End If;
      End If;
   End if;
end SP_PutSiwCoordenada;
/


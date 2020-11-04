<?php

class Eventos_admin extends TPage{
   private $id;
	private $datagrid;
	private $pageNavigation;
  private  $form;
  
  use Adianti\Base\AdiantiFileSaveTrait; //Função automatica para salvar imagem

   public function __construct(){
      parent::__construct();
      
		$titulo = new TLabel('<h1>Todos os Eventos</h1>');
		$titulo->style = "width : 100%; text-align : left";
		//Botão Novo Eventos
		 $bt_eventos = TButton::create('bt_criarEventos', ['EventoEditar', 'onAux'], 'Criar Evento', 'fa:plus-circle blue');
		 $bt_eventos->style = "font-size: 15px";        
     $bt_importar = TButton::create('bt_importar', [$this, 'onImport'], 'Importar Eventos', 'fa:file-export blue');
     $bt_importar->style = "font-size: 13px";        
     $formbutton = new BootstrapFormBuilder('form_');
     $formbutton->addFields([$bt_eventos, $bt_importar]);         
					 
	   //Form de Pesquisa
		  $pesquisa = new TEntry('search');
		  $pesquisa->placeholder = 'Pesquisar Eventos';
		  $pesquisa->style= "min-width : 45%";
		  $button = new TButton( 'Pesquisar' );
		  $button->setAction(new TAction(array($this, 'onReload')), '');
		  $button->setImage('fa:search blue');
		// create the form
		 $tableform = new TTable;
		 $tableform->addRowSet($pesquisa, $button);
		 $form = new TForm('PesquisaForm');
		 $form->setFields( [$pesquisa, $button] );
		 $form->add($tableform);
		 $form->width = '100%'; 
      
  //DATAGRID
    $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid() );
    $this->datagrid->style = 'width: auto';//*/
    $this->datagrid->datatable = 'true'; //Opção mobile

    //*Criando colunas da datagrid
    $col_foto        = new TDataGridColumn('foto', 'Foto', 'left');
    $col_titulo      = new TDataGridColumn('nome', 'Nome', 'left');
    $col_palestrante = new TDataGridColumn('Palestrante', 'Palestrante', 'center');
    $col_pchave      = new TDataGridColumn('pchave', 'Palavra Chave', 'center');

    //Configurando as colunas
    $col_foto->setTransformer( function($imagem){
      $imagem = new TImage($imagem);
      $imagem->style = 'max-width: 100px';
      return $imagem;
    });
    //*Adicionando colunas na datagrid
    $this->datagrid->addColumn($col_foto);
    $this->datagrid->addColumn($col_titulo);
    $this->datagrid->addColumn($col_palestrante);
    $this->datagrid->addColumn($col_pchave);

    //Add Action
    $pchave = new TDataGridAction( [$this, 'addChaveEvento'] );
    $pchave->setLabel('Adicionar Palavra Chave');
    $pchave->setImage('fa:plus-square green');
    $pchave->setField('id');

    $editar = new TDataGridAction( ['EventoEditar', 'onEdit'] );
    $editar->setLabel('Editar Evento');
    $editar->setImage('fa:pen-square blue ');
    $editar->setField('id');

		$excluir = new TDataGridAction( [$this, 'onDelete'] );
		$excluir->setLabel('Excluir Evento');
		$excluir->setImage('fa:trash red ');
    $excluir->setField('id');
    
    $inserir = new TDataGridAction( [$this, 'onInsertAluno'] );
    $inserir->setLabel('Inserir alunos');
    $inserir->setImage('fa:user-plus  fa-lg');
    $inserir->setFields(['id', 'nome']);

		$action_group = new TDataGridActionGroup('Opções ', 'fa:cog');
        
		  //Botão de ação
        $action_group->addAction($pchave);
        $action_group->addAction($editar);
        $action_group->addAction($excluir);
        $action_group->addAction($inserir);
       //Adicionando as ações
       $this->datagrid->addActionGroup( $action_group );
       $this->datagrid->createModel();

      //Navegação
       $this->pageNavigation = new TPageNavigation; //Método que faz a paginação da datagrid
       $this->pageNavigation->setAction( new TAction( [$this, 'onReload'] ) ); //Sempre que o usuário clicar na paginação a ação de carga da datagrid deve ser executada (onReload)

      //Organização
       $panel = new TPanelGroup();
       $table = new TTable;
       $tablebutton = new TTable;
       $table->width = '100%';
       $tablebutton->addRowSet($bt_importar, $bt_eventos);
       $row = $table->addRow();
       $row->style= "width:100%; text-align : center";
       $cell = $row->addCell($tablebutton);
       $cell1 = $row->addCell($titulo);
       $cell2 = $row->addCell($form);
       $cell->style = "text-align : center";
       $panel->add($table);
       $tabledatagrid = new TTable;
       $tabledatagrid->addRowSet($this->datagrid);
       $row = $tabledatagrid->addRow();
       $row->addCell($this->pageNavigation);
       $panel->add($tabledatagrid);
      
      //Verificação
      if(TSession::getValue('logged')){
        $nivel = TSession::getValue('nivel');
        if($nivel == 1)
          parent::add($panel);
        else
          new TMessage('error', "você não tem permissão para entrar nessa pagina!", new TAction(['Home', 'onAux']));
      }else
        new TMessage('error', "você não está logado!", new TAction(['Login', 'onAux']));

   }

   public function onAux(){
      //Só para ser redirecionado
   }

   public function onDelete($param){
    $action1 = new TAction( [$this, 'excluir'] );
    $action1->setParameters( $param );
    $action2 = new TAction( [$this, 'onAux'] );
    new TQuestion('Deseja realmente apagar esse Evento?', $action1, $action2);
  }

  public function excluir($param){
    try{
       TTransaction::open('sample');
       if(! $param['id'])
       	throw new Exception('Não existe evento para apagar!');
       $evento = new Eventos($param['id']);
       unlink($evento->foto);
    	 $evento->delete();
       TTransaction::close();
    }catch(Exception $e){
       new TMessage('error', $e->getMessage());
    }
    new TMessage('info', 'Evento deletado com sucesso!');
    $this->onReload($param);
  }

    public function addChaveEvento($param){
      if(array_key_exists('id', $param) )
          $ideventos = $param['id'];

      //Dialogo com Input  
      $form = new BootstrapFormBuilder('input_form');          
      $id = new TEntry('id');
      $eventos_id = new TEntry('idevento');
      $chave = new TEntry('palavrachave');

      $eventos_id->setValue($ideventos);
      
      $id->setEditable(FALSE);
      $eventos_id->setEditable(FALSE);
      $form->addFields( [new TLabel('Id do Evento')], [$eventos_id]);
      $form->addFields( [new TLabel('Palavra Chave')], [$chave]);
      $form->addAction('Salvar', new TAction([$this, 'onSaveKey']), 'fa:save blue');

        new TInputDialog('Adicionar Palavra Chave!. ', $form);
    }

    public function onSaveKey($param){
      try{
        TTransaction::open('sample');
        $pChave = new Palavra_Chave_Evento();
        $pChave->chave   = $param['palavrachave'];
        $pChave->ideventos = $param['idevento'];

        $pChave->store();

        $action = new TAction([$this, 'onReload']);
        $action->setParameters( $param ); 
         new TMessage('info', 'Palavra Chave salvo com sucesso!', $action);
        TTransaction::close();
      }catch(Exception $e){
        new TMessage('error', $e->getMessage());
      }
    }

  //Receber a planilha
  public function onImport(){
    $this->formexcel = new BootstrapFormBuilder('myform');
    $this->formexcel->setFieldSizes('100%');
    
    //ADD Input
    $excel = new TFile('planilha');
    $imagens = new TMultiFile('foto');
    $excel->setAllowedExtensions( ['csv'] );
    $imagens->enableFileHandling();
    $imagens->setAllowedExtensions( ['gif', 'png', 'jpg', 'jpeg'] );
    $imagens->enableImageGallery(); //Para Aparecer a imagem que foi carregada
    $imagens->enablePopover('Preview', '<img style="max-width:300px" src="download.php?file={file_name}">');

    //Inserir no form
    $this->formexcel->addFields( [new TLabel('Planilha: ')], [$excel]);
    $this->formexcel->addFields( [new TLabel('Imagens: ')], [$imagens]);
    $this->formexcel->addFields( [new TLabel('Inserir Eventos atraves de planilhas e depois sua imagens sucetivamente')]);
    $this->formexcel->addAction('Enviar', new TAction([$this, 'onSaveAs']), 'fa:sign-in-alt blue');

    new TInputDialog('Inserir Eventos! ', $this->formexcel);      
  }
  //Salvar evento atraves das planilhas
  public function onSaveAs($param){
    $dados = $param;
    $caminho = array(getcwd(), 'tmp', $dados['planilha']);
    $url = implode("\\", $caminho);

    //Pegar dados do excel
    $handle = fopen( $url , 'r'); // Leitura do arquivo
    if(! $handle){
      new TMessage('error','Nâo foi possivel encontrar a planilha do excel'); 
      return;
    }
    $row = 0;
    while ($line = fgetcsv($handle, 1000, ",")) {
      if ($row == 0) {
        $row++;
        continue;
      }
      //Definir a marcação
      $eventos_excel[] = [
        'nome'         => $line[0],
        'palestrante'  => $line[1],
        'foto'         => $line[2],
        'palavrachave' => $line[3]
      ];
      $row++;
    }
    fclose($handle);

   //Salvar Evento
   try{
      TTransaction::open('sample');
      $i = 0;
      //Salvar Evento
      foreach($eventos_excel as $evento_excel){
        $evento = new Eventos();
        $evento->fromArray( $evento_excel );
        $foto = array( 'foto' => $dados['foto'][$i++] );
        $evento->store();
        $x = $this->saveFile($evento, (object)  $foto, 'foto', 'app/images');

        //Salvar Palavras Chaves
        $palavrachaves = explode(";", $evento_excel['palavrachave']);
        $id = Eventos::select('id')->where('nome', '=', $evento->nome)->load() [0]->id;
        foreach($palavrachaves as $chave){
          $pchave = new Palavra_chave_Evento();
          $pchave->chave = $chave;
          $pchave->ideventos = $id;
          $pchave->store();
          unset($pchave);
        }

        unset($evento);
      }
      TTransaction::close();
   }catch(Exception $e){
      new TMessage('error', $e->getMessage());
   }
   new TMessage('info','Eventos salvados com sucesso!');
   $this->onReload($param);
  }

  //Onde vai receber a planilha de aluno e cursos
  public function onInsertAluno($param){
    $formeventoaluno = new BootstrapFormBuilder('formcursoaluno');
    $formeventoaluno->setFieldSizes('100%');

    //add input
    $excel = new TFile('planilha');
    $combo = new TRadioGroup('evento');
    $excel->setAllowedExtensions( ['csv'] );
    $combo->setLayout('horizontal');
    $items = [$param['id'] =>'Inserir alunos no evento '. $param['nome']];
    $combo->addItems($items);

    //Inserir no form
    $formeventoaluno->addFields( [new TLabel('Planilha: ')], [$excel]);
    $formeventoaluno->addFields( [$combo]);
    $formeventoaluno->addAction('Salvar', new TAction([$this, 'onSaveEventoAlunos']), 'fa:save blue');

    new TInputDialog('Inserir Alunos! ',$formeventoaluno);
  }

   //Salvar Aluno nos Cursos
   public function onSaveEventoAlunos($param){
    $caminho = array(getcwd(), 'tmp', $param['planilha']);
    $url = implode("\\", $caminho);

    //Pegar dados do curso_excel
    $handle = fopen( $url, 'r' );//Leitura do arquivo
    if(! $handle){
      new TMessage('error','Nâo foi possivel encontrar a planilha do excel'); 
      return;
    }
    $row = 0;
    while ($line = fgetcsv($handle, 1000, ",")) {
      if ($row == 0) {
        $row++;
        continue;
      }
      //Definir a marcação
      $evento_aluno_excel[] = [
        'emailaluno'   => $line[0],
        'nomedoevento' => $line[1],
        'comeco'       => $line[2],
        'fim'          => $line[3],
        'situacao'     => $line[4]
      ];
      $row++;
    }
    fclose($handle); //Fecha o arquivo

       //Procurar alunos no outro banco de dados
       $aluno = array();
       try{
         TTransaction::open('teste');
         $con = TTransaction::get();
         foreach($evento_aluno_excel as $cae){
            $sql = $con->prepare("SELECT name,email FROM wp_4_wswebinars_subscribers WHERE email = :email");
           $sql->bindValue(":email", $cae['emailaluno']);
           if( $sql->execute() ){
             $result = $sql->fetch();
             $aluno[] = ["nome" => $result["name"], "email" => $result["email"]];
           }
         }
         //var_dump($aluno); 
         TTransaction::close();
       }catch(Exception $e){
          new TMessage('error', $e->getMessage());
       } 

    //Salvar Cursos alunos
    try{
       TTransaction::open('sample');
       $i = 0;
       foreach($evento_aluno_excel as $cae){
        //procurar o id de alunos e cursos
        $idaluno = Aluno::select('id')->where('email', '=', $cae['emailaluno'])->load();
        if(! $idaluno){
          if($aluno[$i]){
           $alunobj = new Aluno();
           $alunobj->nome  = $aluno[$i]['nome'];
           $alunobj->email = $aluno[$i]['email'];
           $alunobj->store();
           $idaluno = $alunobj->id;
           unset($alunobj);
         }
         else{
           new TMessage('error','Aluno não encontrado!');
           return;
         }
       }
       else
         $idaluno = $idaluno[0]->id;

        if(! array_key_exists('evento', $param) ){
          $idevento = Eventos::select('id')->where('nome', '=', $cae['nomedoevento'])->load();
          if($idevento){
            new TMessage('error','Curso não encontrado!');
            return;
          }
          else
            $idevento = $idevento[0]->id;
        }
        else
          $idevento = $param['evento'];

        $eventosAluno = new Eventos_Aluno();
        $eventosAluno->fromArray($cae);
        $eventosAluno->idaluno = $idaluno;
        $eventosAluno->idevento = $idevento;
        $eventosAluno->store();
        unset($eventosAluno);
       }
       TTransaction::close();
    }catch(Exception $e){
       new TMessage('error', $e->getMessage());
    }
    new TMessage('info','Aluno inseridos com sucesso!');
    $this->onReload($param);

  }

    //Atualizar dados do datagrid
    public function onReload($param){
      try{
        TTransaction::open('sample');
          $repository = new TRepository('Eventos');
          $limit = 5;
          //Em Breve
          $criteria = new TCriteria();
          $criteria->setProperty('limit', $limit);
          //Lê a URL e extrai as informações de paginação (limit, ofset etc)
          $criteria->setProperties( $param ); 
          
          //Vejo se alguem pesquisou
          if(array_key_exists('search', $param)){
           
            $criteria->add( new TFilter('nome', 'like', "%{$param['search']}%") );
          }

          // Faz a busca utilizando os filtros captudados depois colocar $criteria, false
          $objetos = $repository->load($criteria);

          $this->datagrid->clear(); //Limpo o datagrid
          
          if ($objetos){
            //Classe abstrata
            $objeto = new stdClass();
            foreach ($objetos as $obj){
              //Pegar Palavra Chave
                $repchave = new TRepository('Palavra_Chave_Evento');
                $criteriachave = new TCriteria;
                $criteriachave->add( new TFilter('ideventos', '=', $obj->id) ); 
                $pChaves = $repchave->load($criteriachave);
                $chave = false;
                if($pChaves){
                  $chave = array();
                  foreach($pChaves as $pChave)
                     array_push($chave, $pChave->chave);
               }
               //Preencher a classe
               $objeto->id           = $obj->id; 
               $objeto->nome         = $obj->nome; 
               $objeto->foto         = $obj->foto; 
               $objeto->palestrante  = $obj->palestrante; 
               if($chave)
                  $objeto->pchave = implode(',', $chave);
               else
               $objeto->pchave = 'Não existe Palavra Chave!';
               $this->datagrid->addItem($objeto);
              
            }
          }
          
          //PAGINAÇÃO
           $criteria->resetProperties(); //Não entendi
           $count = $repository->count( $criteria );
           $this->pageNavigation->setCount( $count );  //Quantos objetos foram carregados
           $this->pageNavigation->setProperties( $param ); 
           $this->pageNavigation->setLimit( $limit );
         
        TTransaction::close();
      }
      catch(Exception $e){
        new TMessage('error', $e->getMessage());
      }
      
    }
    //Onde tudo começa
    public function show(){
        $this->onReload( func_get_args() );
        parent::show();
    }

}
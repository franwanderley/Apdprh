<?php

class Cursos_adm extends TPage{   
   private $id;
   private $datagrid;
   private $pageNavigation;
   private $formexcel;

   use Adianti\Base\AdiantiFileSaveTrait; //Função automatica para salvar imagem
   
   public function  __construct(){
       parent::__construct();

        $titulo = new TLabel('<h1>Todos os cursos</h1>');
        $titulo->style = "width : 100%; text-align : left";
        //Novo cursos
         $bt_cursos = TButton::create('bt_criarCurso', ['CursoEditar', 'onAux'], 'Criar Cursos', 'fa:plus-circle blue');
         $bt_cursos->style = "font-size: 13px";        
         $bt_importar = TButton::create('bt_importar', [$this, 'onImport'], 'Importar Cursos', 'fa:file-export blue');
         $bt_importar->style = "font-size: 13px";        
         $formbutton = new BootstrapFormBuilder('form_');
         $formbutton->addFields([$bt_cursos, $bt_importar]);         
                 
      //Form de Pesquisa
        $pesquisa = new TEntry('search');
        $pesquisa->placeholder = 'Pesquisar Cursos';
        $pesquisa->style= "min-width : 45%";
        $button = new TButton( 'Pesquisar' );
        $button->setAction(new TAction(array($this, 'onReload')), '');
        $button->setImage('fa:search blue');
        $button->setStyle= "font-style = 1.5em; border : none !important;";
       // create the form
       $tableform = new TTable;
       $tableform->addRowSet($pesquisa, $button);
       $form = new TForm('PesquisaForm');
       $form->setFields( [$pesquisa, $button] );
       $form->add($tableform);
       $form->width = '100%';

      //Datagrid
         $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid() );
         $this->datagrid->style = 'width: auto';//*/
         $this->datagrid->datatable = 'true'; //Opção mobile
  
         //*Criando colunas da datagrid
          $col_foto    = new TDataGridColumn('fotodocurso', 'Foto', 'left');
          $col_titulo  = new TDataGridColumn('nomedocurso', 'Nome do Curso', 'left');
          $col_empresa = new TDataGridColumn('cargahoraria', 'Carga Horaria', 'center');
          $col_salario = new TDataGridColumn('professor', 'Professor', 'center');
          $col_pchave  = new TDataGridColumn('pchave', 'Palavra Chave', 'center');
  
         //Configurando as colunas
         $col_foto->setTransformer( function($imagem){
            $imagem = new TImage($imagem);
            $imagem->style = 'max-width: 100px';
            return $imagem;
         });
          //*Adicionando colunas na datagrid
          $this->datagrid->addColumn($col_foto);
          $this->datagrid->addColumn($col_titulo);
          $this->datagrid->addColumn($col_empresa);
          $this->datagrid->addColumn($col_salario);
          $this->datagrid->addColumn($col_pchave);

          //Add Action
          $pchave = new TDataGridAction( [$this, 'addChaveCurso'] );
          $pchave->setLabel('Adicionar Palavra Chave');
          $pchave->setImage('fa:plus-square green fa-lg');
          $pchave->setField('id');

          $editar = new TDataGridAction( ['CursoEditar', 'onEdit'] );
          $editar->setLabel('Editar Curso');
          $editar->setImage('fa:pen-square blue fa-lg');
          $editar->setField('id');

          $excluir = new TDataGridAction( [$this, 'onDelete'] );
          $excluir->setLabel('Excluir Curso');
          $excluir->setImage('fa:trash red fa-lg');
          $excluir->setField('id');
          
          $inserir = new TDataGridAction( [$this, 'onInsertAluno'] );
          $inserir->setLabel('Inserir alunos');
          $inserir->setImage('fa:user-plus  fa-lg');
          $inserir->setFields(['id', 'nomedocurso']);
         //Adicionando as ações
         $action_group = new TDataGridActionGroup('Opções ', 'fa:cog');
         $action_group->addAction( $pchave );
         $action_group->addAction( $editar );
         $action_group->addAction( $excluir );
         $action_group->addAction( $inserir );
        
         $this->datagrid->addActionGroup($action_group);
        $this->datagrid->createModel();
        //Navegação
         $this->pageNavigation = new TPageNavigation; //Método que faz a paginação da datagrid
         $this->pageNavigation->setAction( new TAction( [$this, 'onReload'] ) ); //Sempre que o usuário clicar na paginação a ação de carga da datagrid deve ser executada (onReload)
      //Organização
       $panel = new TPanelGroup();
       $tablebutton = new TTable;
       $table = new TTable;
       $table->width = '100%';
       $tablebutton->addRowSet($bt_importar, $bt_cursos);
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
       //$row->addCell('');
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
    //Chamar a questão
    public function onDelete($param){
      $action1 = new TAction( [$this, 'excluir'] );
      $action1->setParameters( $param );
      $action2 = new TAction( [$this, 'onAux'] );
      new TQuestion('Deseja realmente apagar esse Curso?', $action1, $action2);
    }
    //Excluir o Curso
    public function excluir($param){
      try{
        TTransaction::open('sample');
        if(! $param['id'])
				  throw new Exception('Não existe curso para apagar!');
        $curso = new Cursos($param['id']);
        //Apagar a imagem
        unlink($curso->fotodocurso);
			  $curso->delete();
        TTransaction::close();
      }catch(Exception $e){
         new TMessage('error', $e->getMessage());
		  }
		  new TMessage('info', 'Curso deletado com sucesso!');
		  $this->onReload($param);
    }

    public function onAux(){} //Só para ser redirecionado

    //Adicionar palavra chave
    public function addChaveCurso($param){
      if(array_key_exists('id', $param) )
          $idcursos = $param['id'];

      //Dialogo com Input  
      $form = new BootstrapFormBuilder('input_form');          
      $id = new TEntry('id');
      $cursos_id = new TEntry('idcurso');
      $chave = new TEntry('palavrachave');

      $cursos_id->setValue($idcursos);
      
      $id->setEditable(FALSE);
      $cursos_id->setEditable(FALSE);
      $form->addFields( [new TLabel('Id do Curso')], [$cursos_id]);
      $form->addFields( [new TLabel('Palavra Chave')], [$chave]);
      $form->addAction('Salvar', new TAction([$this, 'onSaveKey']), 'fa:save blue');

        new TInputDialog('Adicionar Palavra Chave!. ', $form);
    }
    //Salvar palavra chave
    public function onSaveKey($param){
      try{
         TTransaction::open('sample');
         $pChave = new Palavra_Chave();
         $pChave->chave   = $param['palavrachave'];
         $pChave->idcursos = $param['idcurso'];
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
      $imagens = new TMultiFile('fotodocurso');
      $excel->setAllowedExtensions( ['csv'] );
      $imagens->enableFileHandling();
      $imagens->setAllowedExtensions( ['gif', 'png', 'jpg', 'jpeg'] );
      $imagens->enableImageGallery(); //Para Aparecer a imagem que foi carregada
      $imagens->enablePopover('Preview', '<img style="max-width:300px" src="download.php?file={file_name}">');

      //Inserir no form
      $this->formexcel->addFields( [new TLabel('Planilha: ')], [$excel]);
      $this->formexcel->addFields( [new TLabel('Imagens: ')], [$imagens]);
      $this->formexcel->addFields( [new TLabel('Inserir Cursos atraves de planilhas e depois sua imagens')]);
      $this->formexcel->addAction('Enviar', new TAction([$this, 'onSaveAs']), 'fa:sign-in-alt blue');

      new TInputDialog('Inserir Cursos! ', $this->formexcel);      
    }

    //Salvar Curso atraves das planilhas
    public function onSaveAs($param){
      $dados = $param;
      $caminho = array(getcwd(), 'tmp', $dados['planilha']);
      $url = implode("\\", $caminho);


      //Pegar dados do excel
      $curso = array();
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
        $cursos_excel[] = [
          'nomedocurso'  => $line[0],
          'professor'    => $line[1],
          'cargahoraria' => $line[2],
          'fotodocurso'  => $line[3],
          'palavrachave' => $line[4]
        ];
        $row++;
      }
      fclose($handle);

     //Salvar Curso
     try{
        TTransaction::open('sample');
        $i = 0;
        //Salvar cursos
        foreach($cursos_excel as $curso_excel){
          $curso = new Cursos();
          $curso->fromArray( $curso_excel );
          $curso->store();
          
          $foto = array( 'fotodocurso' => $dados['fotodocurso'][$i++] );
          $x = $this->saveFile($curso, (object)  $foto, 'fotodocurso', 'app/images');
          
          //Salvar Palavras Chaves
          $palavrachaves = explode(";", $curso_excel['palavrachave']);
          $id = Cursos::select('id')->where('nomedocurso', '=', $curso->nomedocurso)->load() [0]->id;
          foreach($palavrachaves as $chave){
            $pchave = new Palavra_Chave();
            $pchave->chave = $chave;
            $pchave->idcursos = $id;
            $pchave->store();
            unset($pchave);
          }
          unset($curso);
        }
        TTransaction::close();
     }catch(Exception $e){
        new TMessage('error', $e->getMessage());
     }
     new TMessage('info','Cursos salvados com sucesso!');
     $this->onReload($param);
    }

    //Onde vai receber a planilha de aluno e cursos
    public function onInsertAluno($param){
      $formcursoaluno = new BootstrapFormBuilder('formcursoaluno');
      $formcursoaluno->setFieldSizes('100%');

      //add input
      $excel = new TFile('planilha');
      $combo = new TRadioGroup('curso');
      $excel->setAllowedExtensions( ['csv'] );
      $combo->setLayout('horizontal');
      $items = [$param['id'] =>'Inserir alunos no curso '. $param['nomedocurso']];
      $combo->addItems($items);

      //Inserir no form
      $formcursoaluno->addFields( [new TLabel('Planilha: ')], [$excel]);
      $formcursoaluno->addFields( [$combo]);
      $formcursoaluno->addAction('Salvar', new TAction([$this, 'onSaveCursoAlunos']), 'fa:save blue');

      new TInputDialog('Inserir Alunos! ',$formcursoaluno);
    }

    //Salvar Aluno nos Cursos
    public function onSaveCursoAlunos($param){
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
        $curso_aluno_excel[] = [
          'emailaluno'  => $line[0],
          'nomedocurso' => $line[1],
          'comeco'      => $line[2],
          'fim'         => $line[3],
          'situacao'    => $line[4]
        ];
        $row++;
      }
      fclose($handle); //Fecha o arquivo

      //Procurar alunos no outro banco de dados
      $aluno = array();
      try{
        TTransaction::open('teste');
        $con = TTransaction::get();
        foreach($curso_aluno_excel as $cae){
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
         foreach($curso_aluno_excel as $cae){
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
              new TMessage('error','Alunos não encontrado!');
              return;
            }
          }
          else
            $idaluno = $idaluno[0]->id;
            
          $idcursos = Cursos::select('id')->where('nomedocurso', '=', $cae['nomedocurso'])->load();
          if(! $idcursos){
            if(array_key_exists('curso', $param))
              $idcursos = $param['curso'];
            else{
              new TMessage('error','Curso não encontrado!');
              return;
            }
          }else
            $idcursos = $idcursos[0]->id;

          $cursosAluno = new Cursos_Aluno();
          $cursosAluno->fromArray($cae);
          $cursosAluno->idaluno = $idaluno;
          $cursosAluno->idcursos = $idcursos;
          $cursosAluno->store();
          unset($cursosAluno);
          $i++;
         }
         TTransaction::close();
      }catch(Exception $e){
         new TMessage('error', $e->getMessage());
      }
      new TMessage('info','Alunos inseridos com sucesso!');
      $this->onReload($param);

    }

    //Atualizar dados do datagrid
    public function onReload($param){
      try{
        TTransaction::open('sample');
          $repository = new TRepository('Cursos');
          $limit = 5;
          //Em Breve
          $criteria = new TCriteria();
          $criteria->setProperty('limit', $limit);
          $criteria->setProperties( $param ); //Lê a URL e extrai as informações de paginação (limit, ofset etc) e joga para dentro do critério que vai ser usado para carregar os registros da base de dados
                
          if(array_key_exists('search', $param)){
           
            $criteria->add( new TFilter('nomedocurso', 'like', "%{$param['search']}%") );
          }

          $objetos = $repository->load($criteria); // Faz a busca utilizando os filtros captudados depois colocar $criteria, false

          $this->datagrid->clear();
          
          if ($objetos){
            $objeto = new stdClass();
            foreach ($objetos as $obj){
              //Pegar Palavra Chave
              $repchave = new TRepository('Palavra_Chave');
              $criteriachave = new TCriteria;
              $criteriachave->add( new TFilter('idcursos', '=', $obj->id) ); 
              $pChaves = $repchave->load($criteriachave);
              $chave = false;
              if($pChaves){
                $chave = array();
                foreach($pChaves as $pChave){
                  array_push($chave, $pChave->chave);
                }
              }
              $objeto->id = $obj->id; 
              $objeto->nomedocurso = $obj->nomedocurso; 
              $objeto->professor = $obj->professor; 
              $objeto->cargahoraria = $obj->cargahoraria; 
              $objeto->fotodocurso = $obj->fotodocurso; 
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

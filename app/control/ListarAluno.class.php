<?php
class ListarAluno extends TPage{

  private $formexcel;
  use Adianti\Base\AdiantiFileSaveTrait; //Função automatica para salvar imagem
   
   public function __construct(){
       parent::__construct();
       $titulo = new TLabel('<h1>Todos os Alunos</h1>');
       $titulo->style = "width : 100%; text-align : left";
       //Botão Novo Eventos
        $bt_aluno = TButton::create('bt_aluno', ['NovoAdmin', 'onAux'], 'Novo Usuario', 'fa:plus-circle blue');
        $bt_aluno->style = "font-size: 15px";              
   
        $formbutton = new BootstrapFormBuilder('form_');
        $formbutton->addFields([$bt_aluno]);          
                    
      //Form de Pesquisa
        $pesquisa = new TEntry('search');
        $pesquisa->placeholder = 'Pesquisar Alunos';
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
   $this->datagrid->style = 'width: auto; text-align : center';//*/
   $this->datagrid->datatable = 'true'; //Opção mobile

   //*Criando colunas da datagrid
   $col_nome    = new TDataGridColumn('nome', 'Nome', 'left');
   $col_email   = new TDataGridColumn('email', 'Email', 'left');
   $col_celular = new TDataGridColumn('celular', 'Celular', 'center');
   $col_nivel   = new TDataGridColumn('nivel', 'Acesso', 'center');

   //Configurando as colunas
   $col_nivel->setTransformer( function($nivel){
     return $nivel == 1 ? 'ADMIN' : 'Aluno';
   });
   //*Adicionando colunas na datagrid
   $this->datagrid->addColumn($col_nome);
   $this->datagrid->addColumn($col_email);
   $this->datagrid->addColumn($col_celular);
   $this->datagrid->addColumn($col_nivel);

   //Add Action
   $addCurso = new TDataGridAction( [$this, 'addCurso'] );
   $addCurso->setLabel('Inscrever no Curso');
   $addCurso->setImage('fa:plus-square green fa-lg');
   $addCurso->setFields(['id','nome']);

   $editar = new TDataGridAction( ['NovoAdmin', 'onEdit'] );
   $editar->setLabel('Editar Aluno');
   $editar->setImage('fa:pen-square blue  fa-lg');
   $editar->setField('id');

    $excluir = new TDataGridAction( [$this, 'onDelete'] );
    $excluir->setLabel('Excluir Aluno');
    $excluir->setImage('fa:trash red  fa-lg');
    $excluir->setField('id');
      //Adicionando as ações
     $this->datagrid->addAction( $addCurso );
     $this->datagrid->addAction( $editar );
     $this->datagrid->addAction( $excluir );
       
      $this->datagrid->createModel();

    //Navegação
      $this->pageNavigation = new TPageNavigation; //Método que faz a paginação da datagrid
      $this->pageNavigation->setAction( new TAction( [$this, 'onReload'] ) ); //Sempre que o usuário clicar na paginação a ação de carga da datagrid deve ser executada (onReload)

    //Organização
      $panel = new TPanelGroup();
      $table = new TTable;
      $tablebutton = new TTable;
      $table->width = '100%';
      $tablebutton->addRowSet($bt_eventos);
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
   new TQuestion('Deseja realmente apagar esse ALuno?', $action1, $action2);
 }

 public function excluir($param){
   try{
      TTransaction::open('sample');
      if(! $param['id'])
          throw new Exception('Não existe ALuno para apagar!');
        $aluno = new Aluno($param['id']);
        $aluno->delete();
      TTransaction::close();
   }catch(Exception $e){
      new TMessage('error', $e->getMessage());
   }
   new TMessage('info', 'Aluno deletado com sucesso!');
   $this->onReload($param);
 }

   public function addCurso($param){
    //Dialogo com Input  
     $form = new BootstrapFormBuilder('input_form');          
     $id = new TEntry('id');
     $nome = new TEntry('nome');
     $curso = new TDBCombo('curso', 'sample', 'Cursos', 'id', 'nomedocurso');
     $curso->style = "width:100%";

     $id->setValue($param['id']);
     $nome->setValue($param['nome']);
     
     $id->setEditable(FALSE);
     $nome->setEditable(FALSE);
     $curso->enableSearch();

     $form->addFields( [new TLabel('Id:')], [$id]);
     $form->addFields( [new TLabel('Aluno:')], [$nome]);
     $form->addFields( [new TLabel('Curso:')], [$curso]);
     $form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save blue');

       new TInputDialog('Se inscrever no Curso! ', $form);
   }

   public function onSave($param){
     try{
       TTransaction::open('sample');
       $curso_aluno = new Cursos_Aluno();
       $curso_aluno->idaluno   = $param['id'];
       $curso_aluno->idcursos = $param['curso'];

       $curso_aluno->store();

       $action = new TAction([$this, 'onReload']);
       $action->setParameters( $param ); 
        new TMessage('info', 'Aluno se inscreveu no curso com sucesso!', $action);
       TTransaction::close();
     }catch(Exception $e){
       new TMessage('error', $e->getMessage());
     }
   }

   //Atualizar dados do datagrid
   public function onReload($param){
     try{
       TTransaction::open('sample');
         $repository = new TRepository('Aluno');
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
              //Preencher a classe
              $objeto->id      = $obj->id; 
              $objeto->nome    = $obj->nome;
              $objeto->nivel   = $obj->nivel; 
              $objeto->email   = $obj->email; 
              $objeto->celular = $obj->celular; 
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
<?php

class EventosForm extends TPage{   
   private $id;
   private $datagrid;
   private $pageNavigation;
   
   public function  __construct(){
       parent::__construct();
        $titulo = new TLabel('<h1 style="text-align : center">Eventos Concluidos</h1>');
        $titulo->style = "width : 100%; text-align : center";
        
       //Pegar dados da SESSÂO
       if(TSession::getValue('logged') == true){
           $this->id = TSession::getValue('id');
       }

      //Form de Pesquisa
        $pesquisa = new TEntry('search');
        $pesquisa->placeholder = 'Pesquisar Eventos';
        $pesquisa->style= "min-width : 45%";
        $button = new TButton( 'Pesquisar' );
        $button->setAction(new TAction(array($this, 'onReload')), '');
        $button->setImage('fa:search blue');
        $button->style = "font-size = 20px; border : none !important;";
       // create the form
       $tableform = new TTable;
       $tableform->addRowSet($pesquisa, $button);
       $form = new TForm('PesquisaForm');
       $form->setFields( [$pesquisa, $button] );
       $form->add($tableform);
       $form->width = '100%';

      //Datagrid
         $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid() );
         $this->datagrid->style = 'width: 100%';//*/
         $this->datagrid->datatable = 'true'; //Opção mobile                  
  
         //*Criando colunas da datagrid
          $col_foto    = new TDataGridColumn('foto', 'Foto', 'left');
          $col_titulo  = new TDataGridColumn('nome', 'Nome do Evento', 'left');
          $col_salario = new TDataGridColumn('palestrante', 'Palestrante', 'center');
          $col_comeco = new TDataGridColumn('comeco', 'Começou em', 'center');
          $col_fim = new TDataGridColumn('fim', 'Terminou em', 'center');
  
         //Configurando as colunas
         $col_foto->setTransformer( function($imagem){
            $imagem = new TImage($imagem);
            $imagem->style = 'max-width: 100px';
            return $imagem;
         });
          //*Adicionando colunas na datagrid
          $this->datagrid->addColumn($col_foto);
          $this->datagrid->addColumn($col_titulo);
          $this->datagrid->addColumn($col_salario);
          $this->datagrid->addColumn($col_comeco);
          $this->datagrid->addColumn($col_fim);
          //Add Action
          $baixar = new TDataGridAction( [$this, 'baixarCertificado'] );
          $baixar->setLabel('Baixar Certificado');
          $baixar->setImage('fa:download blue');
          $baixar->setField('id');

         //Adicionando as ações
         $this->datagrid->addAction( $baixar );
          
        $this->datagrid->createModel();
        //Navegação
         $this->pageNavigation = new TPageNavigation; //Método que faz a paginação da datagrid
         $this->pageNavigation->setAction( new TAction( [$this, 'onReload'] ) ); //Sempre que o usuário clicar na paginação a ação de carga da datagrid deve ser executada (onReload)
      //Organização
       $panel = new TPanelGroup();
       $table = new TTable;
       $table->width = '100%';
       $table->addRowSet($titulo, $form);
       $panel->add($table);
       $tabledatagrid = new TTable;
       $tabledatagrid->addRowSet($this->datagrid);
       $row = $tabledatagrid->addRow();
       //$row->addCell('');
       $row->addCell($this->pageNavigation);

      $panel->add($tabledatagrid);

      if(TSession::getValue('logged') == true){
        $this->id = TSession::getValue('id');
        parent::add($panel); //Só aceita um
      }
      else {
        $action = new TAction( ['Login', 'onAux'] );
       new TMessage('info','Você não está logado! ', $action);
      }
  }
  //Baixar Certificado
  public function baixarCertificado($param){
      TSession::setValue('evento_id', $param['id']);

      $situacao = null;
      try{
         TTransaction::open('sample');
         $eventos_aluno = Eventos_Aluno::select('situacao')->where('idevento', '=', $param['id'])->where('idaluno', '=', TSession::getValue('id'))->load();
         $situacao = $eventos_aluno[0]->situacao; 
         TTransaction::close();
      }catch(Exception $e){
         new TMessage('error', $e->getMessage());
      }
      //Dialogo com Input
      $form = new BootstrapFormBuilder('input_form');
            
      $login = new TEntry('palavrachave');
      $login->placeholder = 'Separe por virgula';
      $lbobs = new TLabel('Caso o evento não possuir palavra chave é só clicar em entrar!');
      $form->addFields( [new TLabel('Palavra Chave!')], [$login]);
      $form->addFields([], [$lbobs] );
      $form->addAction('Entrar', new TAction([$this, 'onKey']), 'fa:save blue');
      if(strpos( $situacao , 'Concluido' )){
        // show the input dialog
        new TInputDialog('Digite a palavra chave do evento para acessar o certificado. ', $form);
      }
       else
        AdiantiCoreApplication::gotoPage('CertificadoEvento'); // reload
  }

    public function onKey( $param ){
      //Validação
      try{
        TTransaction::open('sample');
        $repository = new TRepository('Palavra_Chave_Evento');

        $chave = array();
        $keys = explode(',',$param['palavrachave']); 
        foreach ($keys as $key) {
          array_push($chave, trim($key));
        }
        $keys = $chave;
        
        $criteria = new TCriteria();// Fazer na mão
        $criteria->add( new TFilter('ideventos', '=', TSession::getValue('evento_id')) );
        $count = $repository->count($criteria); //Vai contar todas as palavras chaves deste evento
        $criteria->add(new TFilter('chave', 'IN', $keys));
        $objs = $repository->count($criteria);
        
        TTransaction::close();

        if($count == $objs)
          AdiantiCoreApplication::gotoPage('CertificadoEvento'); // reload
        else{
          TSession::setValue('evento_id', null);
          new TMessage('error','Palavras Chaves não Encotrado');
        }
      }catch(Exception $e){
        new TMessage('error', $e->getMessage());
      }

    }

    public function onAux(){}
  
    //Atualizar dados do datagrid
    public function onReload($param){
      try{
        TTransaction::open('sample');
          $repository = new TRepository('Eventos');
          $limit = 5;
          //Em Breve
          $criteria = new TCriteria();
          $criteria->setProperty('limit', $limit);
          $criteria->setProperties( $param ); //Lê a URL e extrai as informações de paginação (limit, ofset etc) e joga para dentro do critério que vai ser usado para carregar os registros da base de dados
            
          if(array_key_exists('search', $param)){
           
            $criteria->add( new TFilter('nome', 'like', "%{$param['search']}%") );
          }
          if(TSession::getValue('logged') == true){
            $criteria->add( new TFilter( 'id' , 'IN', '(SELECT idevento FROM eventos_aluno WHERE idaluno = '.TSession::getValue('id').')' ));
          }

          $objetos = $repository->load($criteria); // Faz a busca utilizando os filtros captudados depois colocar $criteria, false

          $this->datagrid->clear();
          
          if ($objetos){
            $objeto = new stdClass();
            foreach ($objetos as $obj){
              $objeto->id = $obj->id; 
              $objeto->nome = $obj->nome; 
              $objeto->palestrante = $obj->palestrante; 
              $objeto->cargahoraria = $obj->cargahoraria; 
              $objeto->foto = $obj->foto; 
              $objeto->comeco = $obj->eventos_aluno[0]->comeco; 
              $objeto->fim = $obj->eventos_aluno[0]->fim; 
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

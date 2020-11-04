<?php
class NovoAdmin extends TPage{
   private $form;

   public function __construct(){
		parent::__construct();

		$this->form = new BootstrapFormBuilder('aluno_form');
		$this->form->setFormTitle('<h2>Novo Usuario</h2>');
		$this->form->style= "text-align : center";

		//Add Input
		 $id      = new TEntry('id');
		 $nome    = new TEntry('nome');
		 $email   = new TEntry('email');
		 $celular = new TEntry('celular');
		 $senha   = new TPassword('senha');
		 $nivel   = new TRadioGroup('nivel');

		//Hidden
		$id->setEditable(false);
		$celular->setMask("(99)99999-9999");
		$nivel->setLayout('horizontal');
		$nivel->setUseButton();
		//Set Items
		$items = [0 => 'ALUNO', 1 => 'ADMIN'];
		$nivel->addItems($items);

		// Validação de formularios
		 $nome->addValidation('nome', new TMinLengthValidator, array(10)); // cannot be less the 3 characters
		 $nome->addValidation('nome', new TRequiredValidator); // Obrigatorio
		 $senha->addValidation('senha', new TMinLengthValidator, array(8)); // cannot be greater the 20 characters
		 $senha->addValidation('senha', new TRequiredValidator); // Obrigatorio
		 $celular->addValidation('celular', new TRequiredValidator); // email field

		//Inserir os input no form
		 $this->form->addFields([new TLabel('Id:')],[$id]);
		 $this->form->addFields([new TLabel('Nome:')],[$nome]);
		 $this->form->addFields([new TLabel('Email:')],[$email]);
		 $this->form->addFields([new TLabel('Senha:')],[$senha]);
		 $this->form->addFields([new TLabel('Celular:')],[$celular]);
		 $this->form->addFields([new TLabel('Acesso:')],[$nivel]);

		 $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save blue');

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
	
	public function onSave($param){
		try{
			  $this->form->Validate(); //validar formulario
			  TTransaction::open('sample');
			  $data = (object) $this->form->getData();

			  $aluno = new Aluno($data->id);
			  $aluno->nome = $data->nome;
			  $aluno->celular = $data->celular;
			  $aluno->senha = $data->senha;
			  $aluno->nivel = $data->nivel;
			  $aluno->store();
			  
			  TTransaction::close();
		}catch(Exception $e){
			  new TMessage('error', $e->getMessage());
		}
		$ac = new TAction(['ListarAluno', 'onAux']);
		new TMessage('info', 'Usuario Cadastrado com Sucesso', $ac);
	}
	
	public function onEdit($param){
		if($param){
			try{
				TTransaction::open('sample');
				$aluno = new Aluno( $param['id'] );
				$this->form->setData($aluno);
				TTransaction::close();
			}catch(Exception $e){
				new TMessage('error', $e->getMessage());
			}
		}else{
			$action = new TAction(['ListarAluno', 'onAux']);
			new TMessage('error', "O Usuario não foi identificado!", $action);
		}
	}

	public function onAux(){}
}
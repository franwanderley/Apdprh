<?php

class AlunoEditar extends TPage{
    private $form;
    public function __construct(){
        parent::__construct();

        $this->form = new BootstrapFormBuilder('aluno_form');
        $this->form->setFormTitle('<h2 style="text-align:center">Editar Aluno</h2>');

        //Add Input
        $id      = new TEntry('id');
        $nome    = new TEntry('nome');
        $email   = new TEntry('email');
        $celular = new TEntry('celular');
        $senha   = new TPassword('senha');

        //Hidden
        $id->setEditable(false);
        $email->setEditable(false);
        $celular->setMask("(99)99999-9999");

        // Validação de formularios
        $nome->addValidation('nome', new TMinLengthValidator, array(10)); // cannot be less the 3 characters
        $nome->addValidation('nome', new TRequiredValidator); // Obrigatorio
        $senha->addValidation('senha', new TMinLengthValidator, array(8)); // cannot be greater the 20 characters
        $senha->addValidation('senha', new TRequiredValidator); // Obrigatorio
        $celular->addValidation('celular', new TRequiredValidator); // email field


        $this->form->addFields([new TLabel('Id:')],[$id]);
        $this->form->addFields([new TLabel('Nome:')],[$nome]);
        $this->form->addFields([new TLabel('Email:')],[$email]);
        $this->form->addFields([new TLabel('Senha:')],[$senha]);
        $this->form->addFields([new TLabel('Celular:')],[$celular]);

        $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save blue');

        //Verificação
        if( TSession::getValue('register') )
            parent::add($this->form);
        else
            new TMessage('error', "Você não tem permissão para entrar nessa pagina!", new TAction(['Login', 'onAux']));
    }

    public function onRegister(){
        if( TSession::getValue('email') ){
            $aluno = new Aluno();
            $aluno->nome = TSession::getValue('nome');
            $aluno->email = TSession::getValue('email');
            $this->form->setData($aluno);
        }
    } 

    public function onSave($param){
        new TSession; //limpar a sessão
        try{
            $this->form->Validate(); //validar formulario
            TTransaction::open('sample');
            $data = (object) $this->form->getData();
            
            $idaluno = ALuno::select('id')->where('email', '=', $data->email);
            if($idaluno)
               $aluno = new Aluno($idaluno[0]->id);
            else
               $aluno = new Aluno();
        
            $aluno->nome    = $data->nome;
            $aluno->email   = $data->email;
            $aluno->senha   = $data->senha;
            $aluno->celular = $data->celular;
            $aluno->store();
            
            //Gravar na Sessão
            $r = Aluno::select('id')->where('senha', '=', $aluno->senha)->load();
            TSession::setValue('logged', true);
            TSession::setValue('id', $r[0]->id );
            TSession::setValue('user', $aluno->nome);
            TSession::setValue('nivel', 0);
            AdiantiCoreApplication::gotoPage('Home');
            TTransaction::close();
        }catch(Exception $e){
             new TMessage('error', $e->getMessage());
        }
    }

    public function onAux(){}

}
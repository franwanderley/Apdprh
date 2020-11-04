<?php

class CursoEditar extends TPage{

   private $formcurso;
   use Adianti\Base\AdiantiFileSaveTrait; //Função automatica para salvar imagem

   public function __construct(){
       parent::__construct();
       
        //Criação deo Formulario
        $this->formcurso = new BootstrapFormBuilder('input_form');
		  $this->formcurso->setFormTitle('<h2 style="text-align:center">Criar ou Editar Curso</h2>');
       //Add Input
        $id           = new TEntry('id');
        $nome         = new TEntry('nomedocurso');
        $professor    = new TEntry('professor');
        $cargahoraria = new TEntry('cargahoraria');
        $fotodocurso  = new TFile('fotodocurso');

       $id->setEditable(FALSE);
       $fotodocurso->enableFileHandling();
       $fotodocurso->setAllowedExtensions( ['gif', 'png', 'jpg', 'jpeg'] );
       $fotodocurso->enableImageGallery(); //Para Aparecer a imagem que foi carregada
       $fotodocurso->enablePopover('Preview', '<img style="max-width:300px" src="download.php?file={file_name}">');

      // Validação de formularios
       $nome->addValidation('nomedocurso', new TMinLengthValidator, array(10)); // cannot be less the 3 characters
       $nome->addValidation('nomedocurso', new TRequiredValidator); // Obrigatorio
       $professor->addValidation('professor', new TMinLengthValidator, array(10)); // cannot be greater the 20 characters
       $professor->addValidation('professor', new TRequiredValidator); // Obrigatorio               $field5->addValidation('Field 5', new TRequiredValidator); // required field
       $cargahoraria->addValidation('cargahoraria', new TNumericValidator); // email field
       $cargahoraria->addValidation('cargahoraria', new TRequiredValidator); // email field

      //Inserir no form
       $this->formcurso->addFields( [new TLabel('Foto:')], [$fotodocurso, new TLabel('Se quiser continuar com a foto é só não colocar nada')]);
       $this->formcurso->addFields( [new TLabel('Id')], [$id]);
       $this->formcurso->addFields( [new TLabel('Nome:')], [$nome]);
       $this->formcurso->addFields( [new TLabel('Professor:')], [$professor]);
       $this->formcurso->addFields( [new TLabel('Carga Horária:')], [$cargahoraria]);
		 $this->formcurso->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save blue');
		 
		//Verificação
		if(TSession::getValue('logged')){
			$nivel = TSession::getValue('nivel');
			if($nivel == 1)
				parent::add($this->formcurso);
			else
				new TMessage('error', "você não tem permissão para entrar nessa pagina!", new TAction(['Home', 'onAux']));
		}else
         new TMessage('error', "você não está logado!", new TAction(['Login', 'onAux']));
   }

    public function onEdit($param){
		try{
			TTransaction::open('sample');
			$cursos = new Cursos($param['id']);
			$this->formcurso->setData($cursos);
			TTransaction::close();
	   }catch(Exception $e){
			new TMessage('error', $e->getMessage());
	   }
    }

   public function onSave($param){
     $data = (object) $this->formcurso->getData();
     try{
        // run form validation
        $this->formcurso->validate();
        TTransaction::open('sample');
        if($data->id)
              $curso = new Cursos($data->id);
        else
				 $curso = new Cursos();
			$aux = $curso->fotodocurso;
         $curso->fromArray( (array) $this->formcurso->getData());
         if($aux)
            $curso->fotodocurso = $aux;

         $curso->store();
         
			if($data->fotodocurso != null){
            $x = $this->saveFile($curso, $data, 'fotodocurso', 'app/images');// salvo na pasta
            //Apagar foto antiga
            if($aux)
               unlink($aux);
         }
         $action = new TAction( ['Cursos_adm', 'onAux'] );
       new TMessage('info', 'Curso salvo com Sucesso!', $action);
        TTransaction::close();
     }catch(Exception $e){
        new TMessage('error', $e->getMessage());
     }
   }    

   public function onAux(){

   }    
   
}
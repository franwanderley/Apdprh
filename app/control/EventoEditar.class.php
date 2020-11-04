<?php

class EventoEditar extends TPage{

   private $form;
   use Adianti\Base\AdiantiFileSaveTrait; //Função automatica para salvar imagem

    public function __construct(){
      parent::__construct();
       
      //Criação deo Formulario
      $this->form = new BootstrapFormBuilder('input_form');
		$this->form->setFormTitle('<h2 style="text-align:center">Criar ou Editar Evento</h2>');
      //Add Input
        $id             = new TEntry('id');
        $nome           = new TEntry('nome');
        $foto           = new TFile('foto');
        $palestrante    = new TEntry('palestrante');

        $id->setEditable(FALSE);
        $foto->enableFileHandling();
        $foto->setAllowedExtensions( ['gif', 'png', 'jpg', 'jpeg'] );
        $foto->enableImageGallery(); //Para Aparecer a imagem que foi carregada
        $foto->enablePopover('Preview', '<img style="max-width:300px" src="download.php?file={file_name}">');

      // Validação de formularios
       $nome->addValidation('nome', new TMinLengthValidator, array(10)); // cannot be less the 3 characters
       $nome->addValidation('nome', new TRequiredValidator); // Obrigatorio
       $palestrante->addValidation('palestrante', new TMinLengthValidator, array(10)); // cannot be greater the 20 characters
       $palestrante->addValidation('palestrante', new TRequiredValidator); // Obrigatorio               $field5->addValidation('Field 5', new TRequiredValidator); // required field

      //Inserir no form
       $this->form->addFields( [new TLabel('Foto:')], [$foto, new TLabel('Se quiser continuar com a foto é só não colocar nada')]);
       $this->form->addFields( [new TLabel('Id')], [$id]);
       $this->form->addFields( [new TLabel('Nome:')], [$nome]);
       $this->form->addFields( [new TLabel('Palestrante:')], [$palestrante]);
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

    public function onEdit($param){
		try{
			TTransaction::open('sample');
			$eventos = new Eventos($param['id']);
			$this->form->setData($eventos);
			TTransaction::close();
	   }catch(Exception $e){
			new TMessage('error', $e->getMessage());
	   }
    }

   public function onSave($param){
     $data = (object) $this->form->getData();
     try{
        // run form validation
		  $this->form->validate();
		  TTransaction::open('sample');
		  
        if($data->id)
              $evento = new Eventos($data->id);
        else
				 $evento = new Eventos();
			//Vai guardar a foto antiga
			$aux = $evento->foto;
         $evento->fromArray( (array) $this->form->getData());
         if($aux)
            $evento->foto = aux;
         $evento->store();
			if($data->foto != null){
            $x = $this->saveFile($evento, $data, 'foto', 'app/images');// salvo na pasta
            //Apagar foto antiga
            if($aux)
               unlink($aux);
         }
         $action = new TAction( ['Eventos_Admin', 'onAux'] );
         new TMessage('info', 'Evento salvo com Sucesso!', $action);
         TTransaction::close();
     }catch(Exception $e){
        new TMessage('error', $e->getMessage());
     }
   }    

   public function onAux(){

   }    
   
}
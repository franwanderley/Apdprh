<?php
/**
 * AreaDeInteresse Active Record
 * @author  <your-name-here>
 */
class Eventos extends TRecord{
    
    const TABLENAME = 'eventos';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    public $eventos_aluno;
    public $palavra_chave_evento;
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('foto');
        parent::addAttribute('palestrante');
    }

    public function getEventos_Aluno(){
        return $this->eventos_aluno;
    }

    public function getPalavra_Chave(){
        return $this->palavra_chave_evento;
    }

    public function load($id){
        $this->eventos_aluno = parent::loadComposite('Eventos_Aluno', 'idevento', $id);
        $this->palavra_chave_evento = parent::loadComposite('Palavra_Chave_Evento', 'ideventos', $id);

        return parent::load($id);
    }

    public function delete($id = NULL){
        $id = isset($id) ? $id : $this->id;
        parent::deleteComposite('Palavra_Chave_Evento', 'ideventos', $id);
        parent::deleteComposite('Eventos_Aluno', 'idevento', $id);

        parent::delete();
    }

}

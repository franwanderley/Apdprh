<?php
/**
 * AreaDeInteresse Active Record
 * @author  <Wanderley>
 */
class Cursos extends TRecord
{
    const TABLENAME = 'cursos';
    const PRIMARYKEY = 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    public $cursos_aluno;
    public $palavra_chave = array();
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nomedocurso');
        parent::addAttribute('professor');
        parent::addAttribute('cargahoraria');
        parent::addAttribute('fotodocurso');
    }

    public function getCurso_Aluno(){
        return $this->curso_aluno;
    }

    public function getPalavra_Chave(){
        return $this->palavra_chave;
    }

    public function savePalavrasChaves($id){
        $pchave->idcursos = $id;
        foreach($this->palavra_chave as $chave){
            $pchave = new Palavra_Chave();
            $pchave->chave = $chave;
            $pchave->store();
        }
    }

    public function load($id){
        $this->cursos_aluno = parent::loadComposite('Cursos_Aluno', 'idcursos', $id);
        $this->palavra_chave = parent::loadComposite('Palavra_Chave', 'idcursos', $id);

        return parent::load($id);
    }

    public function delete($id = NULL){
        $id = isset($id) ? $id : $this->id;
        parent::deleteComposite('Palavra_Chave', 'idcursos', $id);
        parent::deleteComposite('Cursos_Aluno', 'idcursos', $id);

        parent::delete();
    }

    public function store(){
        if($this->palavra_chave)
            parent::saveComposite('Palavra_Chave', 'idcursos', $this->id, $this->palavra_chave);

        parent::store();
    }


}

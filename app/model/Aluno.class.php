<?php
/**
 * AreaDeInteresse Active Record
 * @author  <wanderley>
 */
class Aluno extends TRecord
{
    const TABLENAME = 'aluno';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    private $eventos; //Agregação
    private $cursos; //Agregação
    
    
    /**
     * Classe Aluno
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('email');
        parent::addAttribute('senha');
        parent::addAttribute('celular');
        parent::addAttribute('nivel');
    }
    
    //Retorna os eventos do Aluno que já terminou
    public function getEventos(){
        return $this->eventos;
    }
    
    //Retorna os cursos do Aluno que já terminou
    public function getCursos(){
        return $this->cursos;
    }

    //Vai carregar  um aluno com id e todos os cursos e eventos
    public function load($id){
        //Carregar todos os cursos do Aluno
        $this->cursos = parent::loadAggregate('Cursos','Cursos_Aluno', 'idaluno', 'idcursos', $id);
        $this->eventos = parent::loadAggregate('Eventos','Eventos_Aluno', 'idaluno', 'idevento', $id);
        
        return parent::load($id);
    }
}

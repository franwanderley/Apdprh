<?php
/**
 * AreaDeInteresse Active Record
 * @author  <Wanderley>
 */
class Cursos_Aluno extends TRecord
{
    const TABLENAME = 'curso_aluno';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('idcursos');
        parent::addAttribute('idaluno');
        parent::addAttribute('comeco');
        parent::addAttribute('fim');
        parent::addAttribute('situacao');
        parent::addAttribute('codigo');
    }


}

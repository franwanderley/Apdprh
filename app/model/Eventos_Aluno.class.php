<?php
/**
 * AreaDeInteresse Active Record
 * @author  <your-name-here>
 */
class Eventos_Aluno extends TRecord
{
    const TABLENAME = 'eventos_aluno';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('idevento');
        parent::addAttribute('idaluno');
        parent::addAttribute('comeco');
        parent::addAttribute('fim');
        parent::addAttribute('situacao');
        parent::addAttribute('codigo');
    }


}

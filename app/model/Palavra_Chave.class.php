<?php
/**
 * AreaDeInteresse Active Record
 * @author  <your-name-here>
 */
class Palavra_Chave extends TRecord
{
    const TABLENAME = 'palavra_chave';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('chave');
        parent::addAttribute('idcursos');
    }


}

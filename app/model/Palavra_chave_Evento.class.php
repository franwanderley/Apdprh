<?php
/**
 * AreaDeInteresse Active Record
 * @author  Francisco Wanderley
 */
class Palavra_Chave_Evento extends TRecord
{
    const TABLENAME = 'palavra_chave_eventos';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('chave');
        parent::addAttribute('ideventos');
    }


}

<?php
class LogOff extends TPage{
    public function __construct(){
        parent::__construct();
    }

    public function LogOff(){
      TSession::setValue('logged', false);
      TSession::setValue('id', null);
      TSession::setValue('user', null);
      TSession::setValue('nivel', null);
      AdiantiCoreApplication::gotoPage('Login');
    }
}
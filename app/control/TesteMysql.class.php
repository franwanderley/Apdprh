<?php
class  TesteMysql extends TPage {
    public function __construct(){
        parent::__construct();
        
        try{
            TTransaction::open('teste');
            $con = TTransaction::get();
            print "oi ";
            $result = $con->query('SELECT email from wp_4_wswebinars_subscribers');
            foreach($result as $r){
                echo $r['email'] . "<br>";
            }
            TTransaction::close();
        }catch(Exception $e){
            new TMessage('error', $e->getMessage());
        }
    }
}

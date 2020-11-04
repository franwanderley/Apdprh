<?php
class Login extends TPage
{
    protected $form; // form
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct($param)
    {
        parent::__construct();

        //Limpar a Sessão
         TSession::setValue('logged', false);
         TSession::setValue('id', null);
         TSession::setValue('user', null);
         TSession::setValue('nivel', null);

        $ini  = AdiantiApplicationConfig::get();
        
        $this->style = 'clear:both';
        // creates the form
        $this->form = new BootstrapFormBuilder('form_login');
        $this->form->setFormTitle( '<b>Login</b>' );
        
        // create the form fields
        $email    = new TLabel('Login');
        $senha    = new TLabel('Senha');
        $login    = new TEntry('login');
        $password = new TPassword('password');
        
        // define the sizes
        $login->setSize('80%', 40);
        $password->setSize('80%', 40);
        $password->placeholder = "Se é a primeira vez aqui digite só o email";
        $login->placeholder = 'Email ou Telefone';
        $login->style = 'height:35px; font-size:14px;float:left;border-bottom-left-radius: 0;border-top-left-radius: 0;';
        $password->style = 'height:35px;font-size:14px;float:left;border-bottom-left-radius: 0;border-top-left-radius: 0;';
        
        
        $login->autofocus = 'autofocus';

        $user = '<span style="float:left;margin-left:44px;height:35px;" class="login-avatar"><span class="glyphicon glyphicon-user"></span></span>';
        $locker = '<span style="float:left;margin-left:44px;height:35px;" class="login-avatar"><span class="glyphicon glyphicon-lock"></span></span>';
        $unit = '<span style="float:left;margin-left:44px;height:35px;" class="login-avatar"><span class="fa fa-university"></span></span>';
        
        $this->form->addFields( [$email], [$login] );
        $this->form->addFields( [$senha], [$password] );
        
        
        $btn = $this->form->addAction('ENTRAR', new TAction(array($this, 'onLogin')), 'fa:sign-in-alt');
        $btn->class = 'btn btn-primary';
        $btn->style = 'height: 40px;display: block;margin: auto;font-size:17px;';
        
        $wrapper = new TElement('div');
        $wrapper->style = 'margin:auto; margin-top:100px;max-width:460px;';
        $wrapper->id    = 'login-wrapper';
        $wrapper->add($this->form);
        
        // add the form to the page
        parent::add($wrapper);
    }

    public function onAux(){
      //Função só para redirecionar
    }
    
    public static function onLogin($param){
      //Pegar alunos do wordpress
        if(! $param['password']){
          try{
             TTransaction::open('teste');
             $con = TTransaction::get();
             $result = $con->query("SELECT * FROM wp_4_wswebinars_subscribers");
             foreach($result as $r){
                 if($param['login'] == $r['email']){
                   TSession::setValue('register', true);
                   TSession::setValue('email', $r['email']);
                   TSession::setValue('nome', $r['name']);
                   new TMessage('info', 'Antes de baixar seus certificados é preciso de mais informações', new TAction( ['AlunoEditar', 'onRegister'] ));
                   return;
                 }
             }

             TTransaction::close();
          }catch(Exception $e){
             new TMessage('error', $e->getMessage());
          }
        }
      
      try{
            TTransaction::open('sample');

            $criteria = new TCriteria; 
            $criteria->add(new TFilter('senha', '=', $param['password']));
            if(strpos($param['login'], '.com') !== false){
              $criteria->add(new TFilter('email', '=', $param['login']));  
            }
            else
            $criteria->add(new TFilter('celular', '=', $param['login'])); 
            // load using repository
            $repository = new TRepository('Aluno'); 
            $alus = $repository->load($criteria);
            //var_dump($alus);
            
            if ($alus) { // se encontrou um funcionario
                foreach ($alus as $alu){ 
                    //Salvar na Sessão
                     TSession::setValue('logged', true);
                     TSession::setValue('id', $alu->id);
                     TSession::setValue('user', $alu->nome);
                     TSession::setValue('nivel', $alu->nivel);
                     
                     AdiantiCoreApplication::gotoPage('Home'); // reload
                }
            }
            else {
              new TMessage ('info', 'USUÁRIO NÃO ENCONTRADO.');
            }
       
            TTransaction::close();
      }
      catch(Exception $e) {
        new TMessage('error', $e->getMessage());
      }
    }


}

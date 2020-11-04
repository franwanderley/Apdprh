<?php

class CertificadoEvento extends TPage{

    public function __construct(){
        parent::__construct();

        $titulo = new TLabel('<h1>Certificado de Participação</h1>');
        
         //PDF
         $this->onGenerate(); //Gerar PDF
         $pdf = new TLabel('<iframe src="http://localhost/APDPRH/download.php?file=app/output/teste.pdf" title="Certificado PDF" width= "600px" height= "400px"></iframe>');

        $button = new TButton( 'email' );
        $button->style = 'font-size: 12px; height: auto; margin : 0 2%;  border-radius : 8px; background: #018FFF;';
        $button->setAction(new TAction(array($this, 'onSend')), 'Enviar p/ Email');
        $button->setImage('fa:mail-bulk black');

        $button2 = new TButton( 'imprimir' );
        $button2->style = "font-size: 12px; height: auto; margin : 0 2%;  border-radius : 8px; background: #018FFF;";
        $button2->setAction(new TAction(array($this, 'onDownload')), 'Imprimir');
        $button2->setImage('fa:download black');

        $form = new TForm('form-botao');
        $form->setFields(array($button, $button2));

        //Organização
        $table = new TTable;
        $table->style = 'border : none; display : flex; justify-content: center;';
        $row = $table->addRow();
        $row->style = 'text-align : center';
        $row->addCell($titulo);
        $table->addRowSet($pdf);
        $tablebutton  = new TTable;
        $tablebutton->style = ' margin : 2%; border : none; display : flex; justify-content: center;';
        $tablebutton->addRowSet($button, $button2);
        $table->addRowSet($tablebutton);

        if(TSession::getValue('logged') == true){
            $this->id = TSession::getValue('id');
            parent::add($table);
        }
        else {
            $action = new TAction( ['Login', 'onAux'] );
            new TMessage('info','Você não está logado! ', $action);
        }
    }

    public function onSend(){
        try{
            TTransaction::open('sample');
            $aluno = new Aluno( TSession::getValue('id') );
            $evento = new Eventos( TSession::getValue('evento_id') );
            TTransaction::close();
        }catch(Exception $e){
            new TMessage('error', $e->getMessage());
        }
        $mail = new TMail; // Email do Adianti (PHPMailer)
        $mensagem = 'Tudo bem, '.$aluno->nome.' aqui está o certificado do Evento '.$evento->nome.' feito pela APDPRH';
        $mensagem = $mensagem;            
        
        //$mail->setReplyTo($ini['repl']);      
        $mail->addAddress($aluno->email, $aluno->nome);
        $mail->setFrom('fullprogramer@gmail.com', 'APDPRH Cursos e Eventos');
        $mail->setSubject('Certificado de Participação');
        $mail->setHtmlBody($mensagem);
        $mail->addAttach('app/output/teste.pdf', 'certificado.pdf');
        $mail->SetUseSmtp();
        $mail->SetSmtpHost('smtp.gmail.com', '465'); // 465 porta com criptografia
        $mail->SetSmtpUser('fullprogramer@gmail.com', 'deley3101');
        try{
            $mail->send(); // enviar
            new TMessage('info', 'Email enviado com sucesso!');
        }catch(Exception $e){
            new TMessage('error', $e->getMessage());
        }
    }

    public function onDownload(){
        parent::openFile('  app/output/teste.pdf');
    }

    public function onGenerate(){
        try {
            TTransaction::open('sample');

            if(! TSession::getValue('id') || ! TSession::getValue('evento_id'))
                throw new Exception("Evento não encontrado!");
            
            $aluno = new Aluno(TSession::getValue('id'));
            $evento = new Eventos(TSession::getValue('evento_id'));
            $idevento_aluno = Eventos_Aluno::select('id')->where('idaluno', '=', $aluno->id)->where('idevento', '=', $evento->id)->load();
            $evento_aluno =  new Eventos_Aluno($idevento_aluno[0]->id);

            $codigo = $evento_aluno->codigo;
            if($evento_aluno->codigo == 0){
                $codigo = rand(1,10000);
                $codigo = strval($codigo);
                $codigo.= $evento_aluno->idaluno; 
                $codigo.= $evento_aluno->ideventos;
                $evento_aluno->codigo = $codigo;
                //echo $codigo;
                $evento_aluno->store();
            }

            TTransaction::close();
        } catch (Exception $e) {
            $action = new TAction(['EventosForm', onAux]);
            new TMessage('error', $e->getMessage(), $action);
        }

        //FPDF
        $pdf = new FPDF('P','cm', 'A4');
        $pdf->addPage('L');
        $pdf->setMargins(1,2,1.5);
        $pdf->Image('app/images/certificadoevento.png',1,1,28,20);

        //Inserir
        $pdf->addFont('Prata', '', 'Prata-Regular.php');
        $pdf->setFont('Prata', '', 40);
        $pdf->Cell(28,14, strtoupper(utf8_decode( $aluno->nome )), 0,0,'C'); //Largura, Altura, string, border ==1, pular linha ou continuar na mesma
        
        $pdf->setTextColor(109,173,169);
        $pdf->setXY(2,9.5);
        $pdf->setFont('Prata', '', 15);
        $pdf->Cell(0,0, utf8_decode('Pela participação do '.$evento->nome),0, 0,'C');
        $pdf->setXY(9,10.3);
        $pdf->Cell(0,0, utf8_decode( 'ensinado pelo palestrante '. $evento->palestrante),'C');
        
        //Validação
        if($evento_aluno->situacao !== 'verificado'){
            $pdf->setTextColor(0,0,0);
            $pdf->setXY(5,17.5);
            $pdf->setFont('Prata', '', 10);
            $pdf->cell(0,0, utf8_decode('Validar Certificado'),0, 0,'L',false, 'index.php?class=Validacao');
            $pdf->setXY(5,18.5);
            $pdf->setFont('Prata', '', 8);
            $pdf->cell(0,0, utf8_decode('Codigo de Validação '. $codigo),0, 0,'L');
        }else{
            $pdf->setTextColor(0,0,0);
            $pdf->setXY(5,17.5);
            $pdf->setFont('Prata', '', 10);
            $pdf->cell(0,0, utf8_decode('Certificado Validado'),0, 0,'L',false, 'index.php?class=Validado&codigo='. $codigo);
            $pdf->setXY(5,18.5);
            $pdf->setFont('Prata', '', 6);
            $pdf->cell(0,0, utf8_decode('http://localhost/APDPRH/index.php?class=Validacao'),0, 0,'L');
        }

        //Data
        $data = $evento_aluno->fim;
        $pdf->setXY(15,17.4);
        $pdf->setFont('Prata', '', 10);
        $pdf->cell(0,0, utf8_decode('São Paulo, '. $data),0, 0,'C');

        $pdf->output('app/output/teste.pdf');
    }

    public function onAux(){
        //Função só´para redirecionar
    }
}
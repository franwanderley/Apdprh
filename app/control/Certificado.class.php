<?php

class Certificado extends TPage{

    public function __construct(){
        parent::__construct();

        $titulo = new TLabel('<h1>Certificado de Conclusão</h1>');
        
        //PDF
        $this->onGenerate(); //Gerar PDF
        $pdf = new TLabel('<iframe src="http://localhost/APDPRH/download.php?file=app/output/teste.pdf" title="Certificado PDF" width= "600px" height= "400px"></iframe>');
        //$pdf->style = 'min-width: 1000px; min-height : 500px';

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
            $curso = new Cursos( TSession::getValue('curso_id') );
            TTransaction::close();
        }catch(Exception $e){
            new TMessage('error', $e->getMessage());
        }

        $mail = new TMail; // Email do Adianti (PHPMailer)
        $mensagem = 'Tudo bem, '.$aluno->nome.' aqui está o certificado do curso '.$curso->nomedocurso.' feito pela APDPRH';
        $mensagem = $mensagem;            
        
        //$mail->setReplyTo($ini['repl']);      
        $mail->addAddress($aluno->email, $aluno->nome);
        $mail->setFrom('fullprogramer@gmail.com', 'APDPRH Cursos');
        $mail->setSubject('Certificado de Conclusão');
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
        
        parent::openFile('app/output/teste.pdf');
    }

    public function onGenerate(){
        try {
            TTransaction::open('sample');

            if(! TSession::getValue('id') || ! TSession::getValue('curso_id'))
                throw new Exception('Curso não Encontrado!');

            $aluno = new Aluno(TSession::getValue('id'));
            $curso = new Cursos(TSession::getValue('curso_id'));
            $cursos_aluno = Cursos_Aluno::select('id')->where('idaluno', '=', $aluno->id)->where('idcursos', '=', $curso->id)->load();
            $curso_aluno =  new Cursos_Aluno($cursos_aluno[0]->id);
            $codigo = 0;

            if($curso_aluno->codigo == 0){
                $codigo = rand(1,10000);
                $codigo = strval($codigo);
                $codigo.= $curso_aluno->idaluno;
                $codigo.= $curso_aluno->idcursos;

                $curso_aluno->codigo = $codigo;
                $curso_aluno->store();
                
            }
            else
                $codigo = $curso_aluno->codigo;
            TTransaction::close();
        } catch (Exception $e) {
            $action = new TAction(['Home', onAux]);
            new TMessage('error', $e->getMessage(), $action);
        }

        //FPDF
        $pdf = new FPDF('P','cm', 'A4');
        $pdf->addPage('L');
        $pdf->setMargins(1,2,1.5);
        $pdf->Image('app/images/CERTIFICADOCERTO.png',1,1,28,20);

        //Inserir
        $pdf->addFont('Prata', '', 'Prata-Regular.php');
        $pdf->setFont('Prata', '', 40);
        $pdf->Cell(28,14, strtoupper(utf8_decode( $aluno->nome )), 0,0,'C'); //Largura, Altura, string, border ==1, pular linha ou continuar na mesma
        
        $pdf->setTextColor(109,173,169);
        $pdf->setXY(2,9.5);
        $pdf->setFont('Prata', '', 15);
        $pdf->Cell(0,0, utf8_decode('Pela Conclusão do curso '.$curso->nomedocurso),0, 0,'C');
        $pdf->setXY(6,10.3);
        $pdf->Cell(0,0, utf8_decode( 'ensinado pelo professor '. $curso->professor .' com a carga horaria de '.$curso->cargahoraria.' h'),'C');

        //Validação
         if($curso_aluno->situacao !== 'verificado'){
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
            $pdf->cell(0,0, utf8_decode('Certificado Validado'),0, 0,'L',false, utf8_decode('index.php?class=Validado&codigo='. $curso_aluno->codigo));
            $pdf->setXY(5,18.5);
            $pdf->setFont('Prata', '', 6);
            $pdf->cell(0,0, utf8_decode('http://localhost/APDPRH/index.php?class=Validacao'),0, 0,'L');
        }

        //Data
        $data = $curso_aluno->fim;
        $pdf->setXY(15,17.4);
        $pdf->setFont('Prata', '', 10);
        $pdf->cell(0,0, utf8_decode('São Paulo, '. $data),0, 0,'C');

        $pdf->output('app/output/teste.pdf');
    }

    public function onAux(){
        //Função só´para redirecionar
    }

}
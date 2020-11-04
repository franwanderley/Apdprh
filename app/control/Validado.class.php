<?php

class Validado extends TPage
{ 
   private $codigo;

    public function __construct($param){
        parent::__construct();

        if(array_key_exists('codigo', $param)){
            $this->codigo = $param['codigo'];

        $titulo = new TLabel('<h1>Este certificado é Validado</h1>');
        
        //PDF
        $this->onGenerate(); //Gerar PDF
        $pdf = new TLabel('<iframe src="http://localhost/APDPRH/download.php?file=app/output/teste.pdf" title="Certificado PDF" width= "600px" height= "400px"></iframe>');
        //$pdf->style = 'min-width: 1000px; min-height : 500px';

        //Organização
        $table = new TTable;
        $table->style = 'border : none; display : flex; justify-content: center;';
        $row = $table->addRow();
        $row->style = 'text-align : center';
        $row->addCell($titulo);
        $table->addRowSet($pdf);
        parent::add($table);
        }
        else
            new TMessage('error','Não conseguimos obter o certificado, tente novamente!', new TAction(['Login', 'onAux']));
    }


    public function onDownload(){
        
        parent::openFile('app/output/teste.pdf');
    }

    public function onGenerate(){
        try {
            TTransaction::open('sample');
            $idcursoaluno = Cursos_Aluno::select('id')->where('codigo', '=', $this->codigo)->load();
            if($idcursoaluno){
                $curso_aluno =  new Cursos_Aluno($idcursoaluno[0]->id);
                $aluno = new Aluno($curso_aluno->idaluno);
                $curso = new Cursos($curso_aluno->idcursos);
            }
            else{
                $ideventoaluno = Eventos_Aluno::select('id')->where('codigo', '=', $this->codigo)->load();
                if($ideventoaluno){
                    $evento_aluno =  new Cursos_Aluno($ideventoaluno[0]->id);
                    $this->onGenerateEvento($evento_aluno);
                    return;
                }
                else
                    new TMessage('error','Não foi possivel encontrar o certificado! Tente Novamente.',new TAction(['Login', 'onAux']));
            }

            TTransaction::close();
        }catch(Exception $e){
            new TMessage('error', $e->getMessage());
        }

        //FPDF
        $pdf = new FPDF('P','cm', 'A4');
        $pdf->addPage('L');
        $pdf->setMargins(1,2,1.5);
        $pdf->Image('app/images/CERTIFICADOCERTO.png',1,1,28,20);

        //Inserir
        $pdf->addFont('Prata', '', 'Prata-Regular.php');
        $pdf->setFont('Prata', '', 40);
        $pdf->Cell(28,14, strtoupper($aluno->nome), 0,0,'C'); //Largura, Altura, string, border ==1, pular linha ou continuar na mesma
        
        $pdf->setTextColor(109,173,169);
        $pdf->setXY(2,9.5);
        $pdf->setFont('Prata', '', 15);
        $pdf->Cell(0,0, utf8_decode('Pela Conclusão do curso '.$curso->nomedocurso),0, 0,'C');
        $pdf->setXY(11,10.3);
        $pdf->Cell(0,0, utf8_decode(' Com a carga horaria de '.$curso->cargahoraria.' h'),'C');

        
        $pdf->setTextColor(0,0,0);
        $pdf->setXY(5,17.5);
        $pdf->setFont('Prata', '', 10);
        $pdf->cell(0,0, utf8_decode('Certificado Validado'),0, 0,'L',false, utf8_decode('index.php?class=Validado&codigo='. $this->codigo));

        //Data
        $data = date("d/m/Y");
        $pdf->setXY(15,17.4);
        $pdf->setFont('Prata', '', 10);
        $pdf->cell(0,0, utf8_decode('São Paulo, '. $data),0, 0,'C');

        $pdf->output('app/output/teste.pdf');
    }

    public function onGenerateEvento($evento_aluno){
        try{
             TTransaction::open('sample');
             $aluno = new Aluno($evento_aluno->idaluno);
             $evento = new Eventos($evento_aluno->idevento);
             TTransaction::close();
        }catch(Exception $e){
             new TMessage('error', $e->getMessage());
        }

        //FPDF
        $pdf = new FPDF('P','cm', 'A4');
        $pdf->addPage('L');
        $pdf->setMargins(1,2,1.5);
        $pdf->Image('app/images/certificadoevento.png',1,1,28,20);

        //Inserir
        $pdf->addFont('Prata', '', 'Prata-Regular.php');
        $pdf->setFont('Prata', '', 40);
        $pdf->Cell(28,14, strtoupper($aluno->nome), 0,0,'C'); //Largura, Altura, string, border ==1, pular linha ou continuar na mesma
        
        $pdf->setTextColor(109,173,169);
        $pdf->setXY(2,9.5);
        $pdf->setFont('Prata', '', 15);
        $pdf->Cell(0,0, utf8_decode('Pela participação do '.$evento->nome),0, 0,'C');
        
        
        $pdf->setTextColor(0,0,0);
        $pdf->setXY(5,17.5);
        $pdf->setFont('Prata', '', 10);
        $pdf->cell(0,0, utf8_decode('Certificado Validado'),0, 0,'L',false, 'index.php?class=Validado&codigo='. $evento_aluno->codigo);

        //Data
        $data = date("d/m/Y");
        $pdf->setXY(15,17.4);
        $pdf->setFont('Prata', '', 10);
        $pdf->cell(0,0, utf8_decode('São Paulo, '. $data),0, 0,'C');

        $pdf->output('app/output/teste.pdf');

    }

    public function onAux(){
        //Função só´para redirecionar
    }

}




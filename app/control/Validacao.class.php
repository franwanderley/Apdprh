<?php

class Validacao extends TPage{

    public function __construct(){
        parent::__construct();

        //Titulo
        $titulo = new TLabel('<h1>Validação de Certificado</h1>');
        $form = new BootstrapFormBuilder('input_form');

        $codigo = new TEntry('codigo');
        $codigo->placeholder = 'Ex : 343743274238423';
        $form->addFields([new TLabel('Codigo:')], [$codigo]);
        $form->addAction('Baixar', new TAction([$this, 'onGenerate']), 'fa:download blue');

        $table = new TTable;
        $table->style = 'border : none; display : flex; justify-content: center;';
        $row = $table->addRow();
        $row->style = 'text-align : center';
        $row->addCell($titulo);
        $table->addRowSet($form);

        parent::add($table);
    }


    public function onGenerate($param){
        try {
            TTransaction::open('sample');
            //Vai procurar um curso_aluno pelo codigo
            $idcursos_aluno = Cursos_Aluno::select('id')->where('codigo', '=', $param['codigo'])->load();
            $ideventos_aluno = Eventos_Aluno::select('id')->where('codigo', '=', $param['codigo'])->load();
            
            if($ideventos_aluno){
                $evento_aluno = new Eventos_Aluno($ideventos_aluno[0]->id);
                $evento_aluno->situacao = 'verificado';
                $evento_aluno->store();

                $action = new TAction( ['CertificadoEvento', 'onAux'] );
                TSession::setValue('evento_id', $evento_aluno->idevento);
                new TMessage('info', 'Certificado verificado com sucesso', $action);
            }
            else if($idcursos_aluno){
                 $curso_aluno = new Cursos_Aluno($idcursos_aluno[0]->id);
                 $curso_aluno->situacao = 'verificado';
                 $curso_aluno->store();
                 $action = new TAction( ['Certificado', 'onAux'] );
                 TSession::setValue('curso_id', $curso_aluno->idcursos);
                 new TMessage('info', 'Certificado verificado com sucesso', $action);
             }
             else
                new TMessage('info', 'Certificado não encontrado');

            TTransaction::close();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }

    }
}
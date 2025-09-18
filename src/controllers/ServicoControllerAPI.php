<?php
namespace Vennizlab\Agendaki\controllers;

use Vennizlab\Agendaki\core\Auth;
use Vennizlab\Agendaki\core\Controller;
use Vennizlab\Agendaki\core\Retorno;
use Vennizlab\Agendaki\helpers\FiltroHelper;
use Vennizlab\Agendaki\models\FuncionarioModel;
use Vennizlab\Agendaki\models\ServicoModel;

class ServicoControllerAPI extends Controller
{
    public function cadastrar( )
    {
        if( $this->isPOST( ) )
        {
            $nome = $this->getCampo("nome");
            $descricao = $this->getCampo("descricao");
            $preco = $this->getCampo("preco");
            $data = $this->getCampo( "data" );
            $preco_inicio = $this->getCampo( "preco_inicio", "00:00" );
            $preco_fim = $this->getCampo( "preco_fim", "23:59" );
            $duracao = $this->getCampo( "duracao", "00:00" );

            $servicoModel = new ServicoModel( );
            $retorno = $servicoModel->cadastrarV1( $nome, $descricao, $preco, $data, $preco_inicio, $preco_fim, $duracao );

            return $this->response( $retorno->getStatusHTTP( ), $retorno->getMensagem( ) );            
        }
        else
            return $this->response( 404, "Não encontrado." );
    }

    public function servicoByFuncionario( )
    {
        if( $_SERVER['REQUEST_METHOD'] == "GET" )
        {
            $id = $_GET['id'] ?? null;

            if( isset( $id ) )
            {
                $servicoModel = new ServicoModel( );
                $servicos = $servicoModel->getByFuncionario( $id );

                if( !isset($servicos) )
                    $servicos = [];

                return $this->response( 200, $servicos );
            }

            return $this->response( 400, "O parametro id é obrigatório.");
        }
        else
            return $this->response( 404, "Não encontrado." );
    }

    public function cadastrarServicoFuncionario( )
    {
        if( $_SERVER['REQUEST_METHOD'] == "POST" )
        {
            $funcionario_id = $_POST['funcionario_id'] ?? null;
            $servicos = $_POST['servico'] ?? null;
            $duracao = $_POST['duracao'] ?? null;

            $validacao = "";

            if( !isset($funcionario_id) )
                $validacao .= "Funcionário obrigatório.\n";
            else
            {
                $funcionario_model = new FuncionarioModel( );
                $funcionario = $funcionario_model->getById( $funcionario_id );

                if( !$funcionario )
                    $validacao .= "Funcionário ($funcionario_id) não encontrado.";
            }

            if( !isset($servicos) )
                $validacao .= "Serviço(s) obrigatório.\n";
            else
            {
                if( !is_array($servicos) )
                    $validacao .= "Serviço precisa ser um array.";
                else
                {
                    $servicoModel = new ServicoModel( );

                    if( !$servicoModel->existeServico( $servicos ) )
                        $validacao .= "Algum serviço não foi encontrado.";
                }
            }

            if( !isset($duracao) )
                $validacao .= "Duração obrigatório.\n";
            else
            {
                if( !is_array($duracao) )
                    $validacao .= "Duração precisa ser um array.";
                else if( is_array($servicos) && count($duracao) != count($servicos) )
                    $validacao .= "Duração tem que ser do mesmo tamanho de Servicos\n";
                else
                {
                    foreach( $duracao as $d )
                        if( !empty($d) && !$this->validaHora($d) )
                            $validacao .= "A hora $d não é uma hora valida.\n";
                }
            }

            if( $validacao != "" )
                return $this->response(400, $validacao);
        
            $servicoModel = new ServicoModel( );
            $sucesso = $servicoModel->atualizaServicoFuncionario( $funcionario_id, $servicos, $duracao );

            if( $sucesso )
                return $this->response( 200, "Serviços vínculado ao funcionário.");
            else
                return $this->response( 400, "Falha ao víncular os serviços ao funcionário.");
        }
        else
            return $this->response( 404, "Não encontrado.");
    }

    function validaHora($hora) 
    {
        return preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9]$/', $hora) === 1;
    }

    public function getServicos( )
    {
        if( $this->isGET( ) )
        {
            $servicoModel = new ServicoModel( );

            $filtro = new FiltroHelper( $this );
            $filtro->add( "id" );

            if( Auth::isFuncionario( ) )
                $filtro->add( "inativo" );
            else
                $filtro->addFiltro( "inativo", false );

            $paginacao = $this->getPaginacao( );
            $retorno = $servicoModel->getPaginado( $filtro, $paginacao );
            
            return $this->responseRetorno( $retorno );
        }
        else
            return $this->responseNaoEncontrado( );
    }

    public function getPreco( )
    {
        if( $this->isGET( ) )
        {
            $data = $this->getCampo( "data" );
            $servico = $this->getCampo( "servico_id" );
            $inicio = $this->getCampo( "inicio", "00:00" );
            $fim = $this->getCampo( "fim", "23:59" );

            $servicoModel = new ServicoModel( );
            $retorno = $servicoModel->getPreco($servico, $data, $inicio, $fim );

            return $this->response( $retorno->getStatusHTTP(), $retorno->getMensagem() );
        }
        else
            return $this->response( 400, "Não encontrado.");
    }

    public function cadastrarPreco( )
    {
        if( $this->isPOST( ) )
        {
            $data = $this->getCampo( "data" );
            $servico = $this->getCampo( "servico_id" );
            $preco = $this->getCampo("preco", 0.0);
            $inicio = $this->getCampo( "inicio", "00:00" );
            $fim = $this->getCampo( "fim", "23:59" );

            $servicoModel = new ServicoModel( );
            $retorno = $servicoModel->cadastrarPreco($servico, $data, $preco, $inicio, $fim );

            return $this->response( $retorno->getStatusHTTP(), $retorno->getMensagem() );
        }
        else
            return $this->response( 400, "Não encontrado.");
    }

    public function editar( )
    {
        if( $this->isPOST( ) )
        {
            $id = $this->getCampo( "id" );
            $nome = $this->getCampo( "nome" );
            $descricao = $this->getCampo( "descricao" );
            $ativo = $this->getCampo( "ativo", false );

            $servicoModel = new ServicoModel( );
            $retornoEdicao = $servicoModel->editar( $id, $nome, $descricao, $ativo );

            if( $retornoEdicao->is( Retorno::SUCESSO ) )
            {
                $retorno = $servicoModel->get( $id );
                return $this->responseRetorno( new Retorno( Retorno::SUCESSO, ["mensagem" => $retornoEdicao->getMensagem( ),
                                                                               "data" => $retorno->getMensagem( ) ]) );
            }
            else
                $this->responseRetorno( $retornoEdicao );
        }
        else
            return $this->responseNaoEncontrado( );
    }

    public function inativar( )
    {
        if( $this->isPOST( ) )
        {
            $id = $this->getCampo( "id" );

            $servicoModel = new ServicoModel( );
            $retorno = $servicoModel->inativar( $id );

            return $this->responseRetorno( $retorno );
        }
        else
            return $this->responseNaoEncontrado( );
    }

    public function editarPreco( )
    {
        if( $this->isPOST( ) )
        {
            $id = $this->getCampo( "id" );
            $preco = $this->getCampo( "preco", 0.0 );
            $inicio = $this->getCampo( "inicio", "00:00" );
            $fim = $this->getCampo( "fim", "23:59" );

            $servicoModel = new ServicoModel( );
            $retorno = $servicoModel->editarPreco( $id, $preco, $inicio, $fim );

            return $this->responseRetorno( $retorno );
        }
        else
            return $this->response( 400, "Não encontrado." );
    }
}
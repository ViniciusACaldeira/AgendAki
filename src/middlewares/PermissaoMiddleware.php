<?php
namespace Vennizlab\Agendaki\middlewares;

use Vennizlab\Agendaki\core\Auth;
use Vennizlab\Agendaki\core\Middleware;
use Vennizlab\Agendaki\helpers\Permissoes;

class PermissaoMiddleware implements Middleware{
    
    private array $permissoes = [];

    public function __construct( array $permissoes )
    {
        $this->permissoes = $permissoes;    
    }

    public function handle( )
    {
        $permissoesUsuario = Auth::usuario( )->permissoes ?? null;

        if( !in_array( Permissoes::ADMINISTRADOR, $permissoesUsuario ) )
        {
            if( !$permissoesUsuario || !in_array($permissoesUsuario, $this->permissoes))
            {
                http_response_code( 403 );
                echo json_encode( ["mensagem" => "Acesso negado."]);
                exit;
            }
        }
    }
}
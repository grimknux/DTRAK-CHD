<?php

namespace App\Middleware;

use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Middleware\BaseMiddleware;

class XFrameOptionsMiddleware extends BaseMiddleware
{
    public function after(RequestInterface $request, ResponseInterface $response)
    {
        $response = $response instanceof Response ? $response : new Response($response);
        $response->setHeader('X-Frame-Options', 'SAMEORIGIN');

        return $response;
    }
}
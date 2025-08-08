<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class CORS implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Allow cross-origin requests from specific origins
        header('Access-Control-Allow-Origin: http://chd1.webex.org');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Origin, Content-Type, X-CSRF-Token');
        header('Access-Control-Allow-Credentials: true');
        
        // Handle preflight requests (OPTIONS method)
        if ($request->getMethod() === 'OPTIONS') {
            header('HTTP/1.1 200 OK');
            exit();
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No need to modify response
    }
}


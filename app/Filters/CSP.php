<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class CSP implements FilterInterface
{
    
    public function before(RequestInterface $request, $arguments = null)
    {
        // nothing needed before request
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $csp = $response->getCSP();

        // Example: directives
        $csp->setDefaultSrc('self');
        $csp->finalize($response);


        $response->setHeader('X-Frame-Options', 'DENY');
        // Optional modern approach
        $response->setHeader('Content-Security-Policy', "frame-ancestors 'self'");
        // Optional: enable Report-Only mode
        // $csp->reportOnly(true);
    }
}

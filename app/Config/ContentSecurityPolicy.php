<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class ContentSecurityPolicy extends BaseConfig
{
    // -------------------------------------------------------------------------
    // Broadbrush CSP management
    // -------------------------------------------------------------------------

    public bool $reportOnly = false;
    public ?string $reportURI = null;
    public bool $upgradeInsecureRequests = false;

    // -------------------------------------------------------------------------
    // Sources allowed
    // -------------------------------------------------------------------------

    public $defaultSrc = ['self'];

    public $scriptSrc = [
        'self',
        'unsafe-inline', // ⚠️ Keep only if really needed
    ];

    public $styleSrc = [
        'self',
        'unsafe-inline', // ⚠️ Keep only if really needed
        'http://fonts.googleapis.com',
    ];

    public $imageSrc = ['self', 'data:']; // include data: if using inline images/icons

    public $baseURI = ['self'];

    public $childSrc = ['self'];

    public $connectSrc = ['self', 'http://dtrak.dohchd1.local'];

    public $fontSrc = [
        'self',
        'http://fonts.gstatic.com',
    ];

    public $formAction = ['self', 'http://dtrak.dohchd1.local'];

    public $frameAncestors = ['self'];

    public $frameSrc = ['self']; // if you use iframes from other domains, add them here

    public $mediaSrc = ['self'];

    public $objectSrc = ['none']; // safer than 'self', Flash/Java applets are obsolete

    public $manifestSrc = ['self'];

    public $pluginTypes = []; // e.g. ['application/pdf'] if you need plugins

    public $sandbox = []; // e.g. ['allow-forms', 'allow-scripts']

    // -------------------------------------------------------------------------
    // Nonces
    // -------------------------------------------------------------------------

    public string $styleNonceTag = '{csp-style-nonce}';
    public string $scriptNonceTag = '{csp-script-nonce}';
    public bool $autoNonce = false;
}

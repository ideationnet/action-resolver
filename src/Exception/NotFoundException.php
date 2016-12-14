<?php

namespace IdNet\Exception;


use Exception;
use Psr\Http\Message\ServerRequestInterface;

class NotFoundException extends \Exception
{
    /** @var ServerRequestInterface */
    protected $request;

    public function __construct(ServerRequestInterface $request, $code = 404)
    {
        $this->request = $request;
        parent::__construct("Not Found", $code);
    }

    public function getRequest()
    {
        return $this->request;
    }
}
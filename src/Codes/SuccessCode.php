<?php
declare(strict_types=1);

namespace Codes;

use Code;
use Slim\Psr7\Response;

/**
 * Return HTTP success codes with status and description
 */
class SuccessCode extends Code
{
    /**
     * Success (200)
     *
     * @param string $description "Success"
     * @param bool $returnResponse Return Response object or JSON string
     * @return Response|string
     */
    public function success(string $description = "Success", bool $returnResponse = true): Response|string
    {
        return $this->response(200, $description, $returnResponse);
    }

    /**
     * Created (201)
     *
     * @param string $description "Created"
     * @param bool $returnResponse Return Response object or JSON string
     * @return Response|string
     */
    public function created(string $description = "Created", bool $returnResponse = true): Response|string
    {
        return $this->response(201, $description, $returnResponse);
    }
}
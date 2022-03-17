<?php
declare(strict_types=1);

namespace Codes;

use Code;
use Slim\Psr7\Response;

/**
 * Return HTTP client errors codes with status and description
 */
class ErrorCode extends Code
{
    /**
     * Custom error code
     *
     * @param int $code Error code
     * @param string $description Error description
     * @param bool $returnResponse Return Response object or JSON string
     * @return Response|string
     */
    public function customError(int $code, string $description, bool $returnResponse = true): Response|string
    {
        return $this->response($code, $description, $returnResponse);
    }

    /**
     * Bad Request (400)
     *
     * @param string $description "Bad Request"
     * @param bool $returnResponse Return Response object or JSON string
     * @return Response|string
     */
    public function badRequest(string $description = "Bad Request", bool $returnResponse = true): Response|string
    {
        return $this->response(400, $description, $returnResponse);
    }

    /**
     * Unauthorized (401)
     *
     * @param string $description "Unauthorized"
     * @param bool $returnResponse Return Response object or JSON string
     * @return Response|string
     */
    public function unauthorized(string $description = "Unauthorized", bool $returnResponse = true): Response|string
    {
        return $this->response(401, $description, $returnResponse);
    }

    /**
     * Forbidden (403)
     *
     * @param string $description "Forbidden"
     * @param bool $returnResponse Return Response object or JSON string
     * @return Response|string
     */
    public function forbidden(string $description = "Forbidden", bool $returnResponse = true): Response|string
    {
        return $this->response(403, $description, $returnResponse);
    }

    /**
     * Not Found (404)
     *
     * @param string $description "Not Found"
     * @param bool $returnResponse Return Response object or JSON string
     * @return Response|string
     */
    public function notFound(string $description = "Not Found", bool $returnResponse = true): Response|string
    {
        return $this->response(404, $description, $returnResponse);
    }

    /**
     * Method Not Allowed (405)
     *
     * @param string $description "Method Not Allowed"
     * @param bool $returnResponse Return Response object or JSON string
     * @return Response|string
     */
    public function methodNotAllowed(string $description = "Method Not Allowed", bool $returnResponse = true): Response|string
    {
        return $this->response(405, $description, $returnResponse);
    }

    /**
     * Conflict (409)
     *
     * @param string $description "Conflict"
     * @param bool $returnResponse Return Response object or JSON string
     * @return Response|string
     */
    public function conflict(string $description = "Conflict", bool $returnResponse = true): Response|string
    {
        return $this->response(409, $description, $returnResponse);
    }
}
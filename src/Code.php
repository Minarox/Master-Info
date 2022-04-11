<?php
declare(strict_types = 1);

use Slim\Psr7\Response;

/**
 * Abstract class for HTTP Codes
 */
abstract class Code
{
    /**
     * Assemble value and description in a json string
     *
     * @param int    $code           Http code
     * @param string $description    Code description
     * @param bool   $returnResponse Return Response object or JSON string
     *
     * @return Response|string
     */
    protected function response(int $code, string $description, bool $returnResponse = true): Response|string
    {
        $json = json_encode(
            array(
                "code_value"       => $code,
                "code_description" => $description
            )
        );

        if ($returnResponse) {
            $response = new Response();
            $response->getBody()->write($json);
            return $response->withStatus($code);
        } else {
            return $json;
        }
    }
}
<?php

namespace TODOListApi\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class BaseController
{
    /**
     * @param array $errors
     *
     * @return JsonResponse
     */
    protected function buildBadRequestResponse(array $errors)
    {
        return new JsonResponse(
            [
                'success' => false,
                'errors' => $errors,
            ],
            Response::HTTP_BAD_REQUEST,
            ['Content-Type', 'application/json']
        );
    }

    /**
     * @return JsonResponse
     */
    protected function buildBadResponse()
    {
        return new JsonResponse(
            [
                'success' => false,
            ],
            Response::HTTP_INTERNAL_SERVER_ERROR,
            ['Content-Type', 'application/json']
        );
    }

    /**
     * @param array $data
     *
     * @return JsonResponse
     */
    protected function buildSuccessfulResponse(array $data = [])
    {
        $response = ['success' => true];

        if (false === empty($data)) {
            $response = array_merge($response, $data);
        }

        return new JsonResponse($response, Response::HTTP_OK, ['Content-Type', 'application/json']);
    }
}

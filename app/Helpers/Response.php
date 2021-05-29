<?php

namespace App\Helpers;

class Response
{
    public function success($data)
    {
        return response()->json([
      'success' => true,
      'error' => null,
      'statusCode' => 200,
      'message' => 'Consulta exitosa.',
      '_data' => $data
    ]);
    }

    public function created($data)
    {
        return response()->json([
      'success' => true,
      '_data' => $data,
      'error' => null,
      'statusCode' => 201,
      'message' => 'Registro creado exitosamente.'
    ]);
    }
    public function exception($exception)
    {
        return response()->json([
      'success' => false,
      '_data' => null,
      'error' => $exception,
      'statusCode' => 500,
      'message' => 'Error grave. Contacte al administrador'
    ], 500);
    }
    public function unauthorized()
    {
        return response()->json([
      'success' => false,
      '_data' => null,
      'error' => 'Unauthorized.',
      'statusCode' => 401,
      'message' => 'Sin autorización.'
    ], 401);
    }
    public function badRequest()
    {
        return response()->json([
      'success' => false,
      '_data' => null,
      'error' => 'Malformed URL.',
      'statusCode' => 400,
      'message' => 'Url no corresponde.'
    ], 400);
    }
    public function noContent()
    {
        return response()->json([
      'success' => false,
      '_data' => null,
      'error' => 'No content',
      'statusCode' => 204,
      'message' => 'Registro no encontrado'
    ]);
    }

    public function customMessageResponse($message, $code)
    {
        return response()->json([
      'success' => false,
      '_data' => null,
      'error' => 'Custom message',
      'statusCode' => $code,
      'message' => $message
    ]);
    }
}

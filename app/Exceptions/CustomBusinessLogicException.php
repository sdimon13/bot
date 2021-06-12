<?php

namespace App\Exceptions;

use Exception;

class CustomBusinessLogicException extends Exception
{

    protected $errorDetails = [];
    
    protected $status = \Illuminate\Http\Response::HTTP_BAD_REQUEST;
    
    protected $logLevel = 'debug';
    
    /**
     * 
     * @param string $message
     * @param string|array|null $errorDetails
     * @param string|null $logLevel
     */
    public function __construct($message, $errorDetails = null, $logLevel = null)
    {
        $this->setDetails($errorDetails);
        
        if ($logLevel) {
            $this->logLevel = $logLevel;
        }
        
        parent::__construct(trans($message));
    }

    /**
     * 
     * @param unknown $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function render($request)
    {
        $responseData = [
            'success' => 0,
            'status' => $this->status,
            'errors' => [
                'list' => [
                    ['message' => trans($this->getMessage())],    
                ],    
            ],
            'params' => $request->all(),
        ];
        
        // Добавляем строки деталей, при наличии
        if (!empty($this->errorDetails)) {
            $responseData['errors']['list'][0]['details'] = [
                'error' => $this->errorDetails,
            ];
        }

        return response($responseData, $this->status);
    }
    
    /**
     * Установка статуса
     * 
     * @param int $status
     * @return \App\Exceptions\CustomBusinessLogicException
     */
    public function setStatus(int $status = null) 
    {
        if (is_scalar($var)) {
            $this->status = $status;
        }
        return $this;
    }
    
    /**
     * Установка деталей
     * 
     * @param array|string|null $details
     * @return \App\Exceptions\CustomBusinessLogicException
     */
    public function setDetails($details = null) 
    {
        if (!is_null($details)) {
            if (is_string($details)) {
                $details = [trans($details)];
            } else {
                foreach ($details ?? [] as &$value) {
                    if (is_string($value)) {
                        $value = trans($value);
                    }
                }
            }
            $this->errorDetails = $details;
        }
        return $this;
    }

    /**
     * Получение деталей
     * 
     * @return array
     */
    public function getDetails() 
    {
        return $this->errorDetails;
    }
    
}

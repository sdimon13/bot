<?php

namespace App\Exceptions;

class ValidationException extends \Illuminate\Validation\ValidationException
{
    /**
     * The status code to use for the response.
     *
     * @var int
     */
    public $status = 400;

    /**
     * Create a new exception instance.
     *
     * @param  mixed $errors
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @param  string  $errorBag
     * @return void
     */
    public function __construct($errors, $response = null, $errorBag = 'default')
    {
        if ($errors instanceof \Illuminate\Validation\Validator) {
            $validator = $errors;
        } else {
            if (is_scalar($errors)) {
                $errors = ['error' => $errors];
            }

            $validator = \Validator::make([], []);
            foreach ($errors as $key => $item) {
                foreach ((array)$item as $subItem) {
                    $validator->errors()->add($key, $subItem);
                }
            }
        }

        parent::__construct($validator, $response, $errorBag);
    }

}

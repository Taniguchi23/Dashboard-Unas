<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class Recaptcha implements Rule
{


    public function passes($attribute, $value)
    {
        return $value;
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify',[
            'secret' => '6LcFkKgnAAAAAMWIgv3FooId-HYJ26qsb8lsb1i9',
            'response' => $value
        ])->object();

        return $response->success;
    }

    public function message()
    {
        return 'The reCAPTCHA verification failed.';
    }
}

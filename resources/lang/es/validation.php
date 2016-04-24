<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => 'El :attribute debe ser aceptado.',
    'active_url'           => 'El :attribute no es una URL válida.',
    'after'                => 'El :attribute debe ser una fecha después :date.',
    'alpha'                => 'El :attribute sólo puede contener letras.',
    'alpha_dash'           => 'El :attribute sólo puede contener letras, números y guiones.',
    'alpha_num'            => 'El :attribute sólo puede contener letras y números.',
    'array'                => 'El :attribute debe ser una matriz.',
    'before'               => 'El :attribute debe ser una fecha antes :date.',
    'between'              => [
        'numeric' => 'El :attribute debe estar entre :min y :max.',
        'file'    => 'El :attribute debe estar entre :min y :max kilobytes.',
        'string'  => 'El :attribute debe estar entre :min y :max characters.',
        'array'   => 'El :attribute debe tener entre :min y :max ítem.',
    ],
    'boolean'              => 'El campo de :attribute debe ser verdadera o falso.',
    'confirmed'            => 'El :attribute la confirmación no coincide.',
    'date'                 => 'La :attribute no es una fecha válida.',
    'date_format'          => 'El :attribute no coincide con el formato :format.',
    'different'            => 'El :attribute y :other debe ser diferente.',
    'digits'               => 'El :attribute debe ser :digits dígitos.',
    'digits_between'       => 'El :attribute debe estar entre :min y :max digits.',
    'email'                => 'El :attribute debe ser una dirección válida de correo electrónico.',
    'filled'               => 'El :attribute es obligatorio.',
    'exists'               => 'El :attribute seleccionado es inválido.',
    'image'                => 'El :attribute debe ser una imagen.',
    'in'                   => 'El :attribute seleccionado es inválido.',
    'integer'              => 'El :attribute debe ser un entero.',
    'ip'                   => 'El :attribute debe ser una dirección IP válida.',
    'max'                  => [
        'numeric' => 'El :attribute no puede ser mayor que :max.',
        'file'    => 'El :attribute no puede ser mayor que than :max kilobytes.',
        'string'  => 'El :attribute no puede ser mayor que :max caracteres.',
        'array'   => 'El :attribute no puede tener más de :max items.',
    ],
    'mimes'                => 'El :attribute debe ser un archivo de type: :values.',
    'min'                  => [
        'numeric' => 'El :attribute al menos debe ser :min.',
        'file'    => 'El :attribute al menos debe ser :min kilobytes.',
        'string'  => 'El :attribute al menos debe ser :min caracteres.',
        'array'   => 'El :attribute debe tener al menos :min items.',
    ],
    'not_in'               => 'El :attribute seleccionado es inválido.',
    'numeric'              => 'El :attribute tiene que ser un número.',
    'regex'                => 'El :attribute formato no es válido.',
    'required'             => 'El :attribute formato no es válido.',
    'required_if'          => 'El campo :attribute es obligatorio cuando :other es :value.',
    'required_with'        => 'El campo :attribute es obligatorio cuando :values está presente.',
    'required_with_all'    => 'El campo :attribute es obligatorio cuando :values está presente.',
    'required_without'     => 'El campo :attribute es obligatorio cuando :values está presente.',
    'required_without_all' => 'El campo :attribute es obligatorio cuando ninguno de los :values están presente.',
    'same'                 => 'El :attribute y :other debe coincidir con.',
    'size'                 => [
        'numeric' => 'El :attribute debe ser :size.',
        'file'    => 'El :attribute debe ser :size kilobytes.',
        'string'  => 'El :attribute debe ser :size caracteres.',
        'array'   => 'El :attribute debe contener :size items.',
    ],
    'string'               => 'El :attribute debe ser una cadena.',
    'timezone'             => 'El :attribute debe ser una zona válida.',
    'unique'               => 'El :attribute ya se ha tomado.',
    'url'                  => 'El :attribute formato no es válido.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];

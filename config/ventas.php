<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Política de crédito para ventas
    |--------------------------------------------------------------------------
    | - 'strict': bloquea nuevas ventas si el cliente tiene saldo pendiente.
    | - 'warn'  : permite vender pero devuelve un warning en la respuesta.
    */
    'credit_policy' => env('VENTAS_CREDIT_POLICY', 'strict'),

    /*
    |--------------------------------------------------------------------------
    | Límite de crédito por cliente (opcional)
    |--------------------------------------------------------------------------
    | Si estableces un límite (>0), se valida el saldo pendiente acumulado del
    | cliente contra este monto. 0 = sin límite.
    */
    'credit_limit' => (float) env('VENTAS_CREDIT_LIMIT', 0),
];

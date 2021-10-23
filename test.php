<?php

require_once('Basket.php');
require_once('Interactions.php');

CONST PRODUCT_CATALOGUE = [
    [
        'product' => 'Red Widget',
        'code' => 'R01',
        'price' => 32.95
    ],
    [
        'product' => 'Green Widget',
        'code' => 'G01',
        'price' => 24.95
    ],
    [
        'product' => 'Blue Widget',
        'code' => 'B01',
        'price' => 7.95
    ],
];

CONST DELIVERY_CHARGE_RULES = [
    [
        'min_total_price' => 0,
        'max_total_price' => 50,
        'delivery_cost' => 4.95
    ],
    [
        'min_total_price' => 51,
        'max_total_price' => 89,
        'delivery_cost' => 2.95
    ],
    [
        'min_total_price' => 90,
        'max_total_price' => INF,
        'delivery_cost' => 0
    ],
];

CONST SPECIAL_OFFERS = [
    [
        'product_code' => 'R01',
        'min_quantity' => 2,
        'discount_multiplier' => 0.5
    ]
];

$basket = new Basket(
    PRODUCT_CATALOGUE,
    DELIVERY_CHARGE_RULES,
    SPECIAL_OFFERS
);

$interactions = new Interactions($basket);
$interactions->interact();
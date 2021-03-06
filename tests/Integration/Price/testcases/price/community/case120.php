<?php
/*
 * Price enter mode: brutto
 * Price view mode: brutto
 * Product count: 2
 * Short description: product's variant inherits parent's unit name and quantity (bug fix for #3876)
 */
$aData = array(
    'articles' => array(
        0 => array(
            'oxid'            => '_testId_1',
            'oxprice'         => 50.80,
            'oxunitquantity'  => 40,
            'oxunitname'      => 'm',
        ),
        1 => array(
            'oxid'            => '_testId_1_childId_1',
            'oxparentid'      => '_testId_1',
        ),
    ),
    'expected' => array(
        '_testId_1' => array(
            'base_price'      => '50,80',
            'price'           => '50,80',
            'unit_price'      => '1,27',
        ),
        '_testId_1_childId_1' => array(
            'base_price'      => '50,80',
            'price'           => '50,80',
            'unit_price'      => '1,27',
        ),
    ),
    'options' => array(
        'config' => array(
            'blEnterNetPrice' => false,
            'blShowNetPrice'  => false,
        ),
    ),
);

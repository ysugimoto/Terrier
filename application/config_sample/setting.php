<?php if ( ! defined('BASE_PATH') ) exit;

$setting['name'] = array(
    'label'  => 'お名前',
    'rules'  => array(
        'required',
        'max_length@10'
    )
);

$setting['email'] = array(
    'label'  => 'お名前',
    'rules'  => array(
        'required',
        'valid_email'
    )
);
$setting['gender'] = array(
    'label'  => '性別',
    'rules'  => array(
        'required',
        'in@男性:女性'
    )
);

$setting['zipcode1'] = array(
    'label'  => '郵便番号1',
    'rules'  => array(
        'required',
        'exact_length@3'
    )
);
$setting['zipcode2'] = array(
    'label'  => '郵便番号2',
    'rules'  => array(
        'required',
        'exact_length@4'
    )
);
$setting['pref'] = array(
    'label'  => '都道府県',
    'rules'  => array(
        'required'
    )
);
$setting['address'] = array(
    'label'  => '住所',
    'rules'  => array(
        'required',
        'max_length@255'
    )
);
$setting['message'] = array(
    'label'  => '内容',
    'rules'  => array(
        'required',
        'max_length@500'
    )
);

return $setting;

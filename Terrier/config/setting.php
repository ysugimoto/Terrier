<?php if ( ! defined('BASE_PATH') ) exit;

$setting[] = array(
    'component' => 'text',
    'field'     => 'name',
    'label'     => '名前',
    'rules'     => array(
        'required'
    )
);

return $setting;

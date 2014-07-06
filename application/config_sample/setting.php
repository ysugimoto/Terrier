<?php if ( ! defined('BASE_PATH') ) exit;

$setting['name'] = array(
    'label'  => '名前',
    'upload' => true,
    'rules'  => array(
        'required',
        'min_length@10'
    )
);

return $setting;

<?php if ( ! defined('BASE_PATH') ) exit;

/**
 * Terrier Validation fields settings sample
 *
 * @example
 * <code>
 *  // key is fieldname
 *  $setting['name'] = array(
 *      // label is field display name
 *      'label' => 'お名前',
 *      // rules is array, validation rules
 *      'rules' => array(
 *          'required', // this field is required
 *          'max_length@10' // input value must be less than 10 length (seprataror is @)
 *      )
 *  );
 *  </code>
 */

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

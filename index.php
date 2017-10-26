<?php
require_once 'OCR.php';
$captchaUrl = "http://211.66.88.71/jwweb/sys/ValidateCode.aspx?t=".rand(100,999); // 验证码页面

$ocr = new OCR($captchaUrl);
$ocr->changeKeys();
$array = [
    '3ER5' => ['3','E','R','5'],
    'X7V7' => ['X','7','V','7'],
    '4FSP' => ['4','F','S','P'],
    'KNA4' => ['K','N','A','4'],
    'FXNM' => ['F','X','N','M'],
    '9ZBE' => ['9','Z','B','E'],
    'XJS8' => ['X','J','S','8'],
//    'FYPC' => ['F','Y','P','C'],
//    'H3WJ' => ['H','3','W','J'],
//    'GGYD' => ['G','G','Y','D'],
//    'EYCD' => ['E','Y','C','D'],
    'BZ5V' => ['B','Z','5','V']
];
foreach ($array as $key => $val) {
    $ocr->setImagePath('./captcha/'.$key.'.jpg');
    $ocr->getHec();
    $ocr->filterInfo();
    $ocr->study($val);
//    echo $key.":<br>";
//    $ocr->getKeys();
}
$captcha = $ocr->getCaptcha();
echo $captcha;
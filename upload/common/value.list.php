<?php

if (!defined('OK_LOADME')) {
    die('o o p s !');
}

// ----------------
// Array of Payment Options
// ----------------
$avalpaymentopt_array = array(
    'stripeacc' => 'Credit Card',
    'payfastmercid' => 'Payfast',
    'paystackpub' => 'Paystack',
    'perfectmoneyacc' => 'Perfectmoney',
    'paypalacc' => 'Paypal',
    'coinpaymentsmercid' => 'Bitcoin',
    'ewalletlabel' => 'EWallet',
    'manualpayipn' => 'manualpayname',
    'system' => 'System',
    'other' => 'Other'
);

// ----------------
// Array of Payment Icon or Logo
// ----------------
$avalpaygateicon_array = array(
    'payfastmercid' => "<i class='fa fa-coins fa-fw'></i>",
    'paystackpub' => "<i class='fa fa-credit-card fa-fw'></i>",
    'perfectmoneyacc' => "<i class='fa fa-file-invoice fa-fw'></i>",
    'paypalacc' => "<i class='fab fa-paypal fa-fw'></i>",
    'stripeacc' => "<i class='fa fa-file-invoice fa-fw'></i>",
    'coinpaymentsmercid' => "<i class='fa fa-coins fa-fw'></i>",
    'ewalletlabel' => "<i class='fa fa-wallet fa-fw'></i>",
    'manualpayipn' => "<i class='fa fa-handshake fa-fw'></i>",
);

// ----------------
// Array of Withdraw Options
// ----------------
$avalwithdrawgate_array = array(
    'payfastmercid' => 'Payfast',
    'paystackpub' => 'Paystack',
    'perfectmoneyacc' => 'Perfectmoney',
    'paypalacc' => 'Paypal',
    'stripeacc' => 'Credit Card',
    'coinpaymentsmercid' => 'Bitcoin',
    'manualpayipn' => 'manualpayname',
);

// ----------------
// Array of Admin Pages
// ----------------
$avaladminpage_array = array(
    'dashboard' => 1,
    'userlist' => 1,
    'getuser' => 1,
    'historylist' => 1,
    'withdrawlist' => 1,
    'genealogylist' => 1,
    'digifile' => 1,
    'digicontent' => 1,
    'managegroup' => 1,
    'getstart' => 1,
    'termscon' => 1,
    'notifylist' => 1,
    'generalcfg' => 1,
    'payplancfg' => 1,
    'paymentopt' => 1,
    'languagelist' => 1,
    'updates' => 1
);

// ----------------
// Array of Member Pages
// ----------------
$avalmemberpage_array = array(
    'dashboard' => 1,
    'getstarted' => 1,
    'planreg' => 1,
    'planpay' => 1,
    'userlist' => 1,
    'getuser' => 1,
    'historylist' => 1,
    'withdrawreq' => 1,
    'genealogyview' => 1,
    'digiload' => 1,
    'digiview' => 1,
    'accountcfg' => 1,
    'feedback' => 1
);

// ----------------
// Array of Interval
// ----------------
$rangeinterval_array = array(
    '' => 'Lifetime',
    '1m' => '/ Month',
    '3m' => '/ 3 Month',
    '1y' => '/ Year',
);

// ----------------
// Array of Country
// ----------------
$country_array = array(
    'AF' => 'AFGHANISTAN',
    'AL' => 'ALBANIA',
    'DZ' => 'ALGERIA',
    'AS' => 'AMERICAN SAMOA',
    'AO' => 'ANGOLA',
    'AR' => 'ARGENTINA',
    'AW' => 'ARUBA',
    'AU' => 'AUSTRALIA',
    'AZ' => 'AZERBAIJAN',
    'BS' => 'BAHAMAS',
    'BD' => 'BANGLADESH',
    'BY' => 'BELARUS',
    'BO' => 'BOLIVIA',
    'BA' => 'BOSNIA HERZEGOVINA',
    'BW' => 'BOTSWANA',
    'BR' => 'BRAZIL',
    'BN' => 'BRUNEI DARUSSALAM',
    'BG' => 'BULGARIA',
    'BI' => 'BURUNDI',
    'KH' => 'CAMBODIA',
    'CM' => 'CAMEROON',
    'CA' => 'CANADA',
    'KY' => 'CAYMAN ISLANDS',
    'CF' => 'CENTRAL AFRICAN REPUBLIC',
    'CL' => 'CHILE',
    'CC' => 'COCOS KEELING ISLANDS',
    'CO' => 'COLOMBIA',
    'KM' => 'COMOROS',
    'CG' => 'CONGO',
    'CK' => 'COOK ISLANDS',
    'CR' => 'COSTA RICA',
    'CI' => 'COTE D IVOIRE',
    'CU' => 'CUBA',
    'CY' => 'CYPRUS',
    'CZ' => 'CZECH REPUBLIC',
    'DM' => 'DOMINICA',
    'EC' => 'ECUADOR',
    'EG' => 'EGYPT',
    'SV' => 'EL SALVADOR',
    'ET' => 'ETHIOPIA',
    'FR' => 'FRANCE',
    'GA' => 'GABON',
    'GM' => 'GAMBIA',
    'GE' => 'GEORGIA',
    'DE' => 'GERMANY',
    'GH' => 'GHANA',
    'GI' => 'GIBRALTAR',
    'GR' => 'GREECE',
    'GT' => 'GUATEMALA',
    'HT' => 'HAITI',
    'HK' => 'HONGKONG',
    'HU' => 'HUNGARY',
    'IS' => 'ICELAND',
    'IN' => 'INDIA',
    'ID' => 'INDONESIA',
    'IR' => 'IRAN',
    'IQ' => 'IRAQ',
    'IE' => 'IRELAND',
    'IT' => 'ITALY',
    'JO' => 'JORDAN',
    'KZ' => 'KAZAKSTAN',
    'KE' => 'KENYA',
    'KP' => 'KOREA NORTH',
    'KR' => 'KOREA SOUTH',
    'KW' => 'KUWAIT',
    'KG' => 'KYRGYZSTAN',
    'LV' => 'LATVIA',
    'LB' => 'LEBANON',
    'LR' => 'LIBERIA',
    'LY' => 'LIBYA',
    'MO' => 'MACAU',
    'MK' => 'MACEDONIA',
    'MG' => 'MADAGASCAR',
    'MW' => 'MALAWI',
    'MY' => 'MALAYSIA',
    'MV' => 'MALDIVES',
    'MX' => 'MEXICO',
    'MD' => 'MOLDOVA',
    'MA' => 'MOROCCO',
    'MZ' => 'MOZAMBIQUE',
    'NA' => 'NAMIBIA',
    'NP' => 'NEPAL',
    'NL' => 'NETHERLANDS',
    'NZ' => 'NEW ZEALAND',
    'NI' => 'NICARAGUA',
    'NG' => 'NIGERIA',
    'OM' => 'OMAN',
    'PK' => 'PAKISTAN',
    'PS' => 'PALESTINE',
    'PA' => 'PANAMA',
    'PG' => 'PAPUA NEW GUINEA',
    'PY' => 'PARAGUAY',
    'PE' => 'PERU',
    'PH' => 'PHILIPPINES',
    'PL' => 'POLAND',
    'PT' => 'PORTUGAL',
    'PR' => 'PUERTO RICO',
    'QA' => 'QATAR',
    'RO' => 'ROMANIA',
    'RU' => 'RUSSIAN FEDERATION',
    'RW' => 'RWANDA',
    'SA' => 'SAUDI ARABIA',
    'SN' => 'SENEGAL',
    'SC' => 'SEYCHELLES',
    'SL' => 'SIERRA LEONE',
    'SG' => 'SINGAPORE',
    'SK' => 'SLOVAKIA',
    'SO' => 'SOMALIA',
    'ZA' => 'SOUTH AFRICA',
    'GS' => 'SOUTH GEORGIA',
    'ES' => 'SPAIN',
    'LK' => 'SRI LANKA',
    'SD' => 'SUDAN',
    'SR' => 'SURINAME',
    'SZ' => 'SWAZILAND',
    'SE' => 'SWEDEN',
    'CH' => 'SWITZERLAND',
    'TW' => 'TAIWAN',
    'TJ' => 'TAJIKISTAN',
    'TZ' => 'TANZANIA',
    'TH' => 'THAILAND',
    'TG' => 'TOGO',
    'TK' => 'TOKELAU',
    'TT' => 'TRINIDAD AND TOBAGO',
    'TN' => 'TUNISIA',
    'TR' => 'TURKEY',
    'TM' => 'TURKMENISTAN',
    'UG' => 'UGANDA',
    'UA' => 'UKRAINE',
    'GB' => 'UNITED KINGDOM',
    'US' => 'UNITED STATES',
    'UY' => 'URUGUAY',
    'UZ' => 'UZBEKISTAN',
    'VE' => 'VENEZUELA',
    'YE' => 'YEMEN',
    'YU' => 'YUGOSLAVIA',
    'ZM' => 'ZAMBIA',
    'ZW' => 'ZIMBABWE',
    '' => 'ANOTHER'
);

// ----------------------------
// Array of Website Category
// ----------------------------
$webcategory_array = array(
    'Business General' => 'Business General',
    'Affiliate and Reseller Programs' => 'Affiliate and Reseller Programs',
    'Domain and Hosting' => 'Domain and Hosting',
    'Business and Finance' => 'Business and Finance',
    'Directories and Search Engines' => 'Directories and Search Engines',
    'MLM Related Programs' => 'MLM Related Programs',
    'Career and Education' => 'Career and Education',
    'Marketing and Advertising' => 'Marketing and Advertising',
    'Computers and Technology' => 'Computers and Technology',
    'Health and Sports' => 'Health and Sports',
    'Shopping and Merchants' => 'Shopping and Merchants',
    'Home and Lifestyle' => 'Home and Lifestyle',
    'Entertainment' => 'Entertainment',
    'Charity and Donations' => 'Charity and Donations',
    'Other' => 'Other'
);

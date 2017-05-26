<?php
require_once "simple_html_dom.php";

$result = array();
for ($i = 11; $i <= 20; $i++) {
    $html = file_get_html('https://spb.novostroyki.org/zastroyschiki/?search_text=&per_page=20&page=' . $i);

    //получаем массив: [название компании] = 'ссылка на страницу'
    $arr_of_company_link = array();
    foreach ($html->find('div.company-box div.co-link a') as $company_link) {
        $temp = $company_link;
        $company_name = $company_link->title;
        $arr_of_company_link[$company_name] = $temp->href;
    }
    $html->clear();



    //получаем массив: [название компании] = 'e-mail'
    foreach ($arr_of_company_link as $company_name => $company_link) {
        $html = file_get_html('https://spb.novostroyki.org/zastroyschiki/' . $company_link);

        if (count($html->find('div.company-box-in ul li a'))) {
            foreach ($html->find('div.company-box-in ul li a') as $item) {
                $mail = $item->innertext;
                if (preg_match('*@*', $mail)) {
                    $result[$company_name] = $mail;
                } else
                    $result[$company_name] = "no mail";


                break;
            }
        }
        else{
            $result[$company_name] = "no mail";
        }
        $html->clear();
    }
}

//пишем результат в файл
foreach ($result as $item => $value) {
    $des = fopen('text.txt', 'a');
    fwrite($des, $item . ' - ' . $value . PHP_EOL);
}

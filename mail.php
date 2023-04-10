<?php
    $from = "noreply@" . $_SERVER["HTTP_HOST"];
    $to = $_POST["contact_email"];

    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=utf-8 \r\n";
    $headers .= "From: <$from>\r\n";

    $msg = "
        <p> Имя: ".$_POST["contact_name"]."</p>
        <p> Телефон: ".$_POST["contact_phone"]."</p>
        <p> Почта: ".$_POST["contact_email"]."</p>
        <p> Сообщение: ".$_POST["contact_message"]."</p>
    ";

    $Subject = "Заявка с сайта Матрица";

    if (mail($to, $Subject, $msg, $headers)) {
        echo "<span style='font-size: 25px; color: black;'><center>Спасибо! Ваше сообщение успешно отправлено!</center></span>";
        echo "<meta http-equiv='refresh' content='5;URL=/'>";
    } else {
        echo "Error: Email has not been sent.";
    }
    
    
    $Url = 'https://b24-ahvmbw.bitrix24.ru/rest/1/a2kf5wgszn3dpz9d/crm.lead.add.json';
    // описываем параметры  лида
    if ($_COOKIE['sbjs_current']){
        $sbjs_current_parts = explode("|||", $_COOKIE['sbjs_current']);
        $sbjs_current = array();
        foreach ($sbjs_current_parts as $sbjs_current_part) {
            list($name, $val) = explode("=", $sbjs_current_part);
            $sbjs_current[$name] = $val;
        }
    }
    
    $ParamLid = http_build_query(array(
        'fields' => array(
            'TITLE' => "Заполнена форма 'Единственная' на сайте '$_SERVER[HTTP_HOST]'", // НАЗВАНИЕ
            'NAME' => str_replace(" ","",$_POST["contact_name"]), // ИМЯ
            'PHONE' => Array(
                "n0" => Array(
                    "VALUE" => str_replace(" ","",$_POST["contact_phone"]),
                    "VALUE_TYPE" => "WORK",
                )), // РАБОЧИЙ ТЕЛЕФОН в массиве
            'OPENED' => 'Y', // Доступно для всех
            'SOURCE_ID' => "WEB", //Источник вебсайт
            'COMMENTS' => $_POST["contact_message"], //Передаем сообщение
            'EMAIL' => Array(
                "n0" => Array(
                    "VALUE" => str_replace(" ","",$_POST["contact_email"]),
                    "VALUE_TYPE" => "WORK",
                ),
            ), // Рабочая эл. почта
            'UTM_SOURCE' =>  str_replace(" ","",$sbjs_current["src"]), // UTM метка
            "UTM_CAMPAIGN" => str_replace(" ","",$sbjs_current["cmp"]), // UTM метка
            "UTM_CONTENT" => str_replace(" ","",$sbjs_current["cnt"]), // UTM метка
            "UTM_MEDIUM" => str_replace(" ","",$sbjs_current["mdm"]), // UTM метка
            "UTM_TERM" => str_replace(" ","",$sbjs_current["trm"]), // UTM метка
            'ASSIGNED_BY_ID' => 1, // Ид ответственного
    
        ),
        'params' => array("REGISTER_SONET_EVENT" => "Y")
    ));
    // обращаемся к сформированному URL при помощи функции curl_exec для создания лида
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_POST => 1,
        CURLOPT_HEADER => 0,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $Url,
        CURLOPT_POSTFIELDS => $ParamLid,
    ));
    $result2 = curl_exec($ch);
    curl_close($ch);
    
?>
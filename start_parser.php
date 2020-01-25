<?php
    $path_parts = pathinfo($_SERVER['SCRIPT_FILENAME']); 
    chdir($path_parts['dirname']);
     
    ini_set('error_reporting', E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);

    require_once '../../../wp-load.php'; 
    require_once 'Pleer.php';
    
    $pleer = Pleer::get_instance();

    //Получаем все товары
    $all_products = $pleer ->getAllProducts();
    foreach($all_products as $key => $value) {
            //Проверяем метку
            $check_update = get_post_meta($value->ID, 'update', true);
            if ($check_update != 1)
            {
                //Получаем ссылку на описание товара.
                $url = $pleer ->getWoocomerceAllUrlProduct($value->ID);
                //Получаем описание товара. (Техническое описание)
                $description = $pleer ->getDetailProduct($url);
                if (!$description)
                {               
                    continue;
                }
                //Обновляем запись.
                $pleer ->createDetailProduct($value->ID, $description, $url); 
            } 
                   
    }




    
?>



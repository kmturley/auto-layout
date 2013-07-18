<?php

$data = array(
    array(
        "type" => "section",
        "class" => " w1of1",
        "data" => array(
            array(
                "type" => "row",
                "class" => "",
                "data" => array(
                    array(
                        "type" => "col",
                        "class" => " w1of1 dark",
                        "data" => 'Navigation'
                    )
                )
            )
        )
    ),
    array(
        "type" => "section",
        "class" => " w960 auto",
        "data" => array(
            array(
                "type" => "row",
                "class" => "",
                "data" => array(
                    array(
                        "type" => "col",
                        "class" => " w1of1 dark",
                        "data" => 'Banner'
                    )
                )
            )
        )
    ),
    array(
        "type" => "section",
        "class" => " w960 auto",
        "data" => array(
            array(
                "type" => "row",
                "class" => " flow-m",
                "data" => array(
                    array(
                        "type" => "col",
                        "class" => " w1of3",
                        "data" => 'Panel'
                    ),
                    array(
                        "type" => "col",
                        "class" => " w1of3",
                        "data" => 'Panel'
                    ),
                    array(
                        "type" => "col",
                        "class" => " w1of3",
                        "data" => 'Panel'
                    )
                )
            )
        )       
    )
);

function renderCss($items) {
    $html = '';
    foreach ($items as $item) {
        if ($item['type'] == 'col') {
            $html .= '<link href="css/'.$item['data'].'.css" rel="stylesheet" />';
        } else {
            $html .= renderCss($item['data']);
        }
    }
    return $html;
}

function renderView($items) {
    $html = '';
    $index = 1;
    $total = count($items);
    foreach ($items as $item) {
        if ($item['type'] == 'col') {
            $html .= '<div class="'.$item['type'].$item['class'].' '.strtolower($item['data']).'">'.file_get_contents('html/'.$item['data'].'.html').'</div>';
        } else {
            $item['data'] = renderView($item['data']);
            $html .= '<div class="'.$item['type'].$item['class'].'">'.$item['data'].'</div>';
        }
        $index += 1;
    }
    return $html;
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="initial-scale=1,minimum-scale=1,maximum-scale=1" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <title>Products</title>
    <link href="http://fonts.googleapis.com/css?family=Lato" rel="stylesheet" />
    <link href="css/base/reset.css" rel="stylesheet" />
    <link href="css/base/layout.css" rel="stylesheet" />
    <link href="css/base/themes.css" rel="stylesheet" />
    <?php echo renderCss($data); ?>
</head>
<body class="default flow-xs">
    <?php echo renderView($data); ?>
</body>
</html>
<?php

    $tz = 'Europe/Kirov';
    $startTimestamp = microtime(true);
    $dt = new DateTime("now", new DateTimeZone($tz));
    $dt->setTimestamp($startTimestamp);
    $startTime = $dt->format('d.m.Y, H:i:s');

    $x = $_GET['x'] ?? null;
    $y = $_GET['y'] ?? null;
    $r = $_GET['radius'] ?? null;

    if (!(is_null($x) && is_null($y) && is_null($r))) {
        if (is_numeric($x) && is_numeric($y) && is_numeric($r) && $r >= 1 && $r <= 4 && $y >= -3 && $y <= 3 && in_array($x, [-5, -4, -3, -2, -1, 0, 1, 2, 3])) {
            $status = 200;
            $answer = true;
            if ($x >= 0 && $y >= 0) {
                $answer = $y <= -$x + $r;
            } else if ($x < 0 && $y >= 0) {
                $answer = false;
            } else if ($x < 0 && $y < 0) {
                $answer = $y <= $r && $x <= $r;
            } else if ($x >= 0 && $y < 0) {
                $answer = $x * $x + $y * $y <= $r * $r;
            }
            $execTime = round((microtime(true) - $startTimestamp) * 1000, 4) . ' мс';

            $prev = isset($_COOKIE['prev']) ? json_decode($_COOKIE['prev']) : [];
            $prev[] = [
                'x' => $x,
                'y' => $y,
                'r' => $r,
                'answer' => $answer,
                'startTime' => $startTime,
                'execTime' => $execTime,
            ];
            setcookie('prev', json_encode($prev));

        } else {
            $status = 400;
        }
    } else {
        $status = 400;
    }

    ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Лабораторная работа №1</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: "Roboto", monospace;
        }
        table {
            width: 100%;
        }
        * {
            box-sizing: border-box;
        }
        html, body {
            padding: 0;
            margin: 0;
        }

        /*HTML-страница должна иметь "шапку", содержащую ФИО студента, номер группы и новер варианта. При оформлении шапки необходимо явным образом задать шрифт (monospace), его цвет и размер в каскадной таблице стилей.*/
        thead {
            font-size: 14px;
            color: #000;
            background: #e5c584;
        }
        #main-table tbody td {
            padding-right: 30px;
            padding-left: 30px;
        }
        #main-table thead th {
            width: 25%;
        }
        #main-table thead {
            padding: 10px 15px 0;
        }
        #main-table thead > span {
            font-weight: bold;
        }

        #main-table thead th div {
            position: relative;
            margin: 5% 7%;
            cursor: pointer;
        }
        #main-table thead th div:before {
            position: absolute;
            left: 0;
            content: '';
            display: block;
            bottom: -4px;
            width: 100%;
            background: #000;
            height: 2px;
            transition: 0.3s;
            transform: scaleX(0);
        }
        #main-table thead th div:hover:before {
            transform: scaleX(1);
        }

        /*Отступы элементов ввода должны задаваться в процентах.*/
        .task-input {
            margin-right: 15px;
            margin-top: 15px;
        }
        .task-input--buttons > button {
            margin-bottom: 5px;
        }
        .task-input--buttons > button._active {
            background: cadetblue;
        }
        .task-input label {
            display: block;
        }
        .task-image {
            margin-top: 15px;
        }

        .task-input input {
            width: 100%;
            padding: 5px 15px;
        }
        .task-input label {
            margin-bottom: 7px;
        }
        .task-input select {
            width: 100%;
            padding: 5px 15px;
        }
        td[data-trigger="input-submit"] > button {
            outline: none;
            border: none;
            background: #e5c584;
            color: #fff;
            padding: 5px 15px;
            width: 100%;
        }
        #result-table {
            padding: 30px;
        }
        #result-table td {
            padding: 10px;
        }
        #result-table th {
            padding-top: 10px;
            padding-bottom: 10px;
        }

        ._buttons-container {
            display: flex;
            align-items: center;
        }
        ._button {
            cursor: pointer;
            text-align: center;
            border-radius: 3px;
            font-size: 14px;
            line-height: 50px;
            padding: 0 20px;
            transition: all 0.3s ease;
            outline: none;
            background: #e5c584;
            color: #121212;
            margin-right: 15px;
            text-decoration: none;
            font-weight: normal;
            display: flex;
            align-items: center;
            border: none;
            position: relative;
            overflow: hidden;
        }
        .task-input-top {
            vertical-align: top;
        }
        .task-input-bottom {
            vertical-align: bottom;
        }
        ._button:hover:before {
            animation: hoverFlare 1.5s infinite;
        }
        ._button:before {
            content: '';
            transition: 3s ease-in-out;
            background: linear-gradient(90deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.5));
            height: 100%;
            width: 20%;
            position: absolute;
            top: 0;
            transform: skewX(-45deg);
            left: -900px;
        }
        ._button img {
            height: 18px;
            margin-right: 12px;
        }

        @keyframes hoverFlare {
            0% {
                left: -90px;
            }
            100% {
                left: 150%;
            }
        }

    </style>

</head>
<body>

<div id="root">

    <table id="main-table">
        <thead style="font-family: monospace">
        <tr>
            <th>
                <div>Лабораторная работа #1</div>
            </th>
            <th>
                <div>ФИО <span>Зинатулин Артём Витальевич</span></div>
            </th>
            <th>
                <div>Группа <span>P32121</span></div>
            </th>
            <th>
                <div>Вариант <span>11223</span></div>
            </th>
        </tr>
        </thead>
        <tbody>
        <tr class="task-input-bottom">
            <td colspan="3">
                <h1>Проверить, попадает ли точка в заданную область</h1>
            </td>
            <td colspan="1">
                <div class="task-image">
                    <img src="assets/images/areas.png" alt="">
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="1">
                <div class="_buttons-container">
                    <a href="./index.html" class="_button"><img src="./assets/icons/back.svg" alt="<-"> Назад</a>
                    <div>
                        <form action="./clearHistory.php" method="POST">
                            <button class="_button">Очистить</button>
                        </form>
                    </div>
                </div>
            </td>
        </tr>
        <tr>

        </tbody>
    </table>

    <table id="result-table">
        <thead>
            <th>№</th>
            <th>X</th>
            <th>Y</th>
            <th>R</th>
            <th>Результат</th>
            <th>Время начала</th>
            <th>Время обработки</th>
        </thead>
        <tbody>
        <?php
        if (isset($_COOKIE['prev'])):
            foreach (json_decode($_COOKIE['prev']) as $key => $item):
                ?>
                <tr>
                    <td><?php echo $key + 1?></td>
                    <td data-param="x"><?php echo $item->x ?></td>
                    <td data-param="y"><?php echo $item->y ?></td>
                    <td data-param="r"><?php echo $item->r ?></td>
                    <td data-param="answer"><?php echo $item->answer ? 'попал' : 'мимо' ?></td>
                    <td data-param="startTime"><?php echo $item->startTime ?></td>
                    <td data-param="execTime"><?php echo $item->execTime ?></td>
                </tr>
                <?php
            endforeach;
        endif;
        ?>
        <?php
            if ($status == 200):
        ?>
            <tr>
                <td><?php echo isset($_COOKIE['prev']) ? count(json_decode($_COOKIE['prev'])) + 1 : 1 ?></td>
                <td data-param="x"><?php echo $x ?></td>
                <td data-param="y"><?php echo $y ?></td>
                <td data-param="r"><?php echo $r ?></td>
                <td data-param="answer"><?php echo $answer ? 'попал' : 'мимо' ?></td>
                <td data-param="startTime"><?php echo $startTime ?></td>
                <td data-param="execTime"><?php echo $execTime ?></td>
            </tr>
        <?php
            endif
        ?>
        </tbody>
    </table>
</div>

</body>
</html>


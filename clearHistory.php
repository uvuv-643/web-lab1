<?php

    if (isset($_COOKIE['prev'])) {
        unset($_COOKIE['prev']);
        setcookie('prev', json_encode([]));
    }

    header("Location: index.html");


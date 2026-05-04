<?php

require_once __DIR__ . '/../midtrans/Midtrans.php';

\Midtrans\Config::$serverKey = 'SB-Mid-server-XXXXXXXX';
\Midtrans\Config::$clientKey = 'SB-Mid-client-XXXXXXXX';

\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;
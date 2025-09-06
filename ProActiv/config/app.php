<?php
return [
    "app" => [
        "name" => "ProActiv",
        "version" => "1.0",
        "debug" => true,
        "timezone" => "Europe/Paris",
        "locale" => "fr",
        "maintenance" => false,
        "paths" => [
            "root" => "/home/ngqu0806/public_html/ProActiv",
            "base_url" => "/ProActiv/public",
            "public" => "/ProActiv/public",
            "assets" => "/ProActiv/public/assets",
            "templates" => "/home/ngqu0806/public_html/ProActiv/templates",
            "storage" => "/home/ngqu0806/public_html/ProActiv/storage",
            "uploads" => "/home/ngqu0806/public_html/ProActiv/public/uploads"
        ]
    ],
    "database" => [
        "enabled" => true,
        "host" => "localhost",
        "port" => 3306,
        "name" => "ngqu0806_proactiv",
        "username" => "ngqu0806_proactiv",
        "password" => "01Petite-Ro!",
        "charset" => "utf8mb4",
        "options" => [
            "PDO::ATTR_ERRMODE" => "PDO::ERRMODE_EXCEPTION",
            "PDO::ATTR_DEFAULT_FETCH_MODE" => "PDO::FETCH_ASSOC"
        ]
    ],
    "security" => [
        "csrf_protection" => true,
        "session_lifetime" => 3600,
        "password_min_length" => 8,
        "jwt_secret" => "5807a75b759f87d2a2971d9450150926d46a42e1ede20960cdcb75c4ece5acbb",
        "encryption_key" => "c78d62cf58eb857ab19d329c0a812150"
    ],
    "features" => [
        "comments" => true,
        "subscriptions" => true,
        "multi_country" => true,
        "analytics" => true,
        "forum" => true,
        "documents" => true,
        "calculators" => true
    ],
    "paths" => [
        "root" => "/home/ngqu0806/public_html/ProActiv",
        "base_url" => "/ProActiv/public",
        "public" => "/ProActiv/public",
        "assets" => "/ProActiv/public/assets",
        "templates" => "/home/ngqu0806/public_html/ProActiv/templates",
        "storage" => "/home/ngqu0806/public_html/ProActiv/storage",
        "uploads" => "/home/ngqu0806/public_html/ProActiv/public/uploads"
    ],
    "mail" => [
        "driver" => "smtp",
        "host" => "localhost",
        "port" => 587,
        "username" => "",
        "password" => "",
        "encryption" => "tls",
        "from_address" => "noreply@proactiv.local",
        "from_name" => "ProActiv"
    ],
    "cache" => [
        "enabled" => false,
        "driver" => "file",
        "ttl" => 3600
    ],
    "logging" => [
        "enabled" => true,
        "level" => "error",
        "file" => "/home/ngqu0806/public_html/ProActiv/storage/logs/app.log"
    ]
];
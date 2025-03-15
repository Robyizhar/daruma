<?php
    function base_url($path = '') {
        return 'http://codingo_webapp.test/' . ltrim($path, '/');
    }

    /* Database Configuration */
    // $config = [
    //     'db_host' => 'localhost',
    //     'db_user' => 'inf1005-sqldev',
    //     'db_pass' => 'r2Qr3YjS',
    //     'db_name' => 'daruma_db',
    // ];
    $config = [
        'db_host' => 'localhost',
        'db_user' => 'root',
        'db_pass' => '',
        'db_name' => 'daruma_db',
    ];

    $conn = new mysqli($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);

    if ($conn->connect_error) 
        die("Koneksi gagal: " . $conn->connect_error);

    /* Database Configuration */
?>

<?php
    function base_url($path = '') {
        return 'http://codingo_webapp.test/' . ltrim($path, '/');
    }

    function checkImage($url) {
        $headers = @get_headers($url);
        if ($headers && strpos($headers[0], '200')) {
            return $url;
        }
        return null;
    }

    /* Database Configuration */
    // $config = [
    //     'db_host' => 'localhost',
    //     'db_user' => 'inf1005-sqldev',
    //     'db_pass' => 'r2Qr3YjS',
    //     'db_name' => 'daruma_db',
    // ];


    /* Database Configuration */
?>

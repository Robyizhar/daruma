<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#000000">
    
    <?php 
        require_once __DIR__ . '/../config/config.php'; 
        
        // Default title
        $pageTitle = "Daruma!";
        
        // Dynamically set the title based on the current page
        $currentFile = basename($_SERVER['PHP_SELF'], ".php");

        switch ($currentFile) {
            case 'index':
                $pageTitle .= " - Home";
                break;
            case 'products':
                $pageTitle .= " - Products";
                break;
            case 'about':
                $pageTitle .= " - About Us";
                break;
            case 'account':
                $pageTitle .= " - Account";
                break;
            case 'register':
                $pageTitle .= " - Register";
                break;
            case 'cart':
                $pageTitle .= " - Shopping Cart";
                break;
            case 'checkout':
                $pageTitle .= " - Checkout";
                break;
            case 'admin':
                $pageTitle .= " - Admin Panel";
                break;
            default:
                $pageTitle .= "";
                break;
        }
    ?>

    <title><?= $pageTitle ?></title>

    <link rel="icon" type="image/x-icon" href="<?= base_url('images/favicon.ico') ?>">
    <link rel="shortcut icon" href="/favicon.ico">

    <link href="<?= base_url('css/bootstrap.min.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/styles.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/fontawesome-free-5.15.4-web/css/all.min.css') ?>">

    <script src="<?= base_url('js/jquery-3.7.1.min.js') ?>"></script>
    <script src="<?= base_url('js/bootstrap.bundle.js') ?>"></script>
    <script src="<?= base_url('css/fontawesome-free-5.15.4-web/js/all.min.js') ?>"></script>
    <script defer src="<?= base_url('js/script.js') ?>"></script>
</head>
<body>

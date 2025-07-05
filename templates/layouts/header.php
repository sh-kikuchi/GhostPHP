<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/public/assets/css/common.css">
    <link rel="stylesheet" href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/public/assets/css/ghost.css">

    <script type="text/javascript" src="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/public/assets/js/ghost.js" defer></script>
    <title>Ghost PHP</title>
</head>
<body>
<header class="ghost-header">
    <div id="title">
      Ghost PHP
    </div>
    <div>
        <div class="ghost-hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    
    <nav class="ghost-header-nav ghost-nav">
        <ul class="ghost-nav-items">
            <li class="ghost-nav-item"><a class="ghost-nav-item" href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>">TOP</a></li>
            <?php if (!isset($_SESSION['signin_user'])) : ?>
                <li class="ghost-nav-item"><a class="ghost-nav-item" href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/signin">Signin</a></li>
                <li class="ghost-nav-item"><a class="ghost-nav-item" href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/signup">Signup</a></li>
            <?php else: ?>
                <li class="ghost-nav-item"><a class="ghost-nav-item" href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/index">My Page</a></li>
                <li class="ghost-nav-item flex-box justify-center">
                    <form action="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/signout" method="POST">
                        <input type="submit" name="signout" class="input-init" value="Signout" style="font-size:21px; cursor: pointer;">
                    </form>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
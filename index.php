<?php
// Using Library: https://github.com/Spomky-Labs/otphp/blob/master/doc/Use.md
session_start();
?>
<html>
<head>
    <title>OTP Test</title>
    <style>
        body {
            padding: 0;
        }

        /* Alert */

        #alert {
            position: relative;
        }

        #alert:hover:after {
            background: hsla(0, 0%, 0%, .8);
            border-radius: 3px;
            color: #f6f6f6;
            content: 'Click to dismiss';
            font: bold 12px/30px sans-serif;
            height: 30px;
            left: 50%;
            margin-left: -60px;
            position: absolute;
            text-align: center;
            top: 50px;
            width: 120px;
        }

        #alert:hover:before {
            border-bottom: 10px solid hsla(0, 0%, 0%, .8);
            border-left: 10px solid transparent;
            border-right: 10px solid transparent;
            content: '';
            height: 0;
            left: 50%;
            margin-left: -10px;
            position: absolute;
            top: 40px;
            width: 0;
        }

        #alert:target {
            display: none;
        }

        .alert {
            background-color: #c4453c;
            background-image: -webkit-linear-gradient(135deg, transparent,
            transparent 25%, hsla(0, 0%, 0%, .05) 25%,
            hsla(0, 0%, 0%, .05) 50%, transparent 50%,
            transparent 75%, hsla(0, 0%, 0%, .05) 75%,
            hsla(0, 0%, 0%, .05));
            background-image: -moz-linear-gradient(135deg, transparent,
            transparent 25%, hsla(0, 0%, 0%, .1) 25%,
            hsla(0, 0%, 0%, .1) 50%, transparent 50%,
            transparent 75%, hsla(0, 0%, 0%, .1) 75%,
            hsla(0, 0%, 0%, .1));
            background-image: -ms-linear-gradient(135deg, transparent,
            transparent 25%, hsla(0, 0%, 0%, .1) 25%,
            hsla(0, 0%, 0%, .1) 50%, transparent 50%,
            transparent 75%, hsla(0, 0%, 0%, .1) 75%,
            hsla(0, 0%, 0%, .1));
            background-image: -o-linear-gradient(135deg, transparent,
            transparent 25%, hsla(0, 0%, 0%, .1) 25%,
            hsla(0, 0%, 0%, .1) 50%, transparent 50%,
            transparent 75%, hsla(0, 0%, 0%, .1) 75%,
            hsla(0, 0%, 0%, .1));
            background-image: linear-gradient(135deg, transparent,
            transparent 25%, hsla(0, 0%, 0%, .1) 25%,
            hsla(0, 0%, 0%, .1) 50%, transparent 50%,
            transparent 75%, hsla(0, 0%, 0%, .1) 75%,
            hsla(0, 0%, 0%, .1));
            background-size: 20px 20px;
            box-shadow: 0 5px 0 hsla(0, 0%, 0%, .1);
            color: #f6f6f6;
            display: block;
            font: bold 16px/40px sans-serif;
            height: 40px;
            position: absolute;
            text-align: center;
            text-decoration: none;
            top: -45px;
            width: 100%;
            -webkit-animation: alert 1s ease forwards;
            -moz-animation: alert 1s ease forwards;
            -ms-animation: alert 1s ease forwards;
            -o-animation: alert 1s ease forwards;
            animation: alert 1s ease forwards;
        }

        /* Animation */

        @-webkit-keyframes alert {
            0% {
                opacity: 0;
            }
            50% {
                opacity: 1;
            }
            100% {
                top: 0;
            }
        }

        @-moz-keyframes alert {
            0% {
                opacity: 0;
            }
            50% {
                opacity: 1;
            }
            100% {
                top: 0;
            }
        }

        @-ms-keyframes alert {
            0% {
                opacity: 0;
            }
            50% {
                opacity: 1;
            }
            100% {
                top: 0;
            }
        }

        @-o-keyframes alert {
            0% {
                opacity: 0;
            }
            50% {
                opacity: 1;
            }
            100% {
                top: 0;
            }
        }

        @keyframes alert {
            0% {
                opacity: 0;
            }
            50% {
                opacity: 1;
            }
            100% {
                top: 0;
            }
        }
    </style>
</head>
<body>
<?php
require __DIR__ . "/vendor/autoload.php";
use OTPHP\TOTP;
use Base32\Base32;

if (isset($_POST["checkCode"])) {
    $totp = new TOTP(
        "SEC " . $_SESSION["generated_key"],
        $_SESSION["generated_key"]
    );

    $totp->setIssuer("The Town");

    $success = false;
    if ($totp->verify($_POST["code"])) {
        $success = true;
    }

    ?>
    <div id="alert">
        <a class="alert"
           href="index.php"><?php echo $success ? "SUCCESS: Right OTP" : "FAILED: Wrong OTP because it is " . $totp->now() . " and not " . $_POST["code"]; ?></a>
    </div>
    <div style="text-align: center;">
        <img src="<?php echo $success ? "https://media.giphy.com/media/nXxOjZrbnbRxS/giphy.gif" : "https://media.giphy.com/media/Jpnj2hI9OZvkk/giphy.gif" ?>">
    </div>
    <?php
} else {
    if (!isset($_SESSION["generated_key"]) || empty($_SESSION["generated_key"])){
        $_SESSION["generated_key"] = str_replace("=", "", Base32::encode(random_bytes(16)));
    }
    $totp = new TOTP(
        "SEC " . $_SESSION["generated_key"],
        $_SESSION["generated_key"]
    );

    $totp->setIssuer("The Town");

    $google_chart = $totp->getQrCodeUri();
    echo "<img src='{$google_chart}'>";
    echo "<br/>" . $totp->getProvisioningUri();
    echo "<br/><br/><br/>";
    if (!is_writable(session_save_path())) {
       echo 'Session path "'.session_save_path().'" is not writable for PHP!'; 
    } else {
        echo "written seassion to " . session_save_path() . " with id " . session_id();;
    }
    ?>
    <p>
    <form action="" method="post">
        <label for="code">6-stelliger OTP Code:
            <input id="code" name="code" type="text" placeholder="069194" autocomplete="off"/>
        </label>
        <button id="checkCode" name="checkCode" type="submit">Überprüfen</button>
    </form>
    </p>
    <?php
}

if (isset($_GET["clear"])){
    session_destroy();
    session_unset();
}
?>
</body>
</html>


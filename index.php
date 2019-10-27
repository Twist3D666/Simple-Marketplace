<?php session_start();
require_once 'easybitcoin.php';
?>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="style.css" media="screen"/>
</head>
<body>
<div class="wrapper">
    <div id="header">
        <img src="images/logo.png">
    </div>
    <div id="navbar">
        <a href="index.php?page=market">Marketplace</a>&nbsp;<?php
        if ($_SESSION['STATUS'] == 'VENDOR') {
            echo "<a
            href='index.php?page=orders&action=check'>Check Orders</a>";
        }
        if (!isset($_SESSION['USERNAME'])) {
            echo "<a href='index.php?page=profile&action=login'>Login</a>&nbsp;<a href='index.php?page=profile&action=register'>Register Account</a>";
        } else {
            echo "<a href='index.php?page=profile'>Edit Profile</a>&nbsp;<a href='index.php'>View your Orders</a>&nbsp;<a href='index.php?page=profile&action=logout'>Logout</a>";
        } ?>
    </div>
    <div id="left">
        <a class="Button" href="index.php?page=market&category=AMPHETAMINE">Amphetamine</a><br>
        <a class="Button" href="index.php?page=market&category=BARBITURATE">Barbiturates</a><br>
        <a class="Button" href="index.php?page=market&category=BENZODIAZEPINE">Benzos</a><br>
        <a class="Button" href="index.php?page=market&category=CANNABIS">Cannabis</a><br>
        <a class="Button" href="index.php?page=market&category=COCAINE">Cocaine</a><br>
        <a class="Button" href="index.php?page=market&category=DMT">Tryptamines</a><br>
        <a class="Button" href="index.php?page=market&category=DRUG">Prescription</a><br>
        <a class="Button" href="index.php?page=market&category=HEROINE">Heroine</a><br>
        <a class="Button" href="index.php?page=market&category=LSD">Hallucinogen</a><br>
        <a class="Button" href="index.php?page=market&category=METHAMPHETAMINE">Crystal</a><br>
        <a class="Button" href="index.php?page=market&category=MDMA">MDMA</a><br>
        <a class="Button" href="index.php?page=market&category=OPIATES">Opiates</a><br>
        <a class="Button" href="index.php?page=market&category=SEEDS">Seeds</a><br>
        <a class="Button" href="index.php?page=market&category=STEROID">Steroids</a><br>
        <a class="Button" href="index.php?page=market&category=REASEARCH_CHEMICAL">Research</a><br>
    </div>
    <div id="content">
        <?php
        $db = @new mysqli('localhost', 'root', 'password', 'database');
        $bitcoin = @new Bitcoin('RPC_USER', 'RPC_PASSWORD', 'localhost', 'RPC_PORT');

        switch ($_GET['page']) {
            case 'profile':
                switch ($_GET['action']) {
                    case 'login':
                        if (isset($_POST['LOGIN'])) {
                            $username = htmlspecialchars($_POST['USERNAME']);
                            $password = hash("sha256", htmlspecialchars($_POST['PASSWORD']));
                            $select_user = "SELECT * FROM users WHERE USERNAME = '$username' AND PASSWORD = '$password';";
                            $user = $db->query($select_user);
                            if ($user->num_rows == '1') {
                                $_SESSION['USERNAME'] = $username;
                                $row = $user->fetch_assoc();
                                $_SESSION['STATUS'] = $row['STATUS'];
                                echo "<br><div id='entry'>You successfully logged in!<br>Click this <a href='index.php?page=market'>Link</a> to proceed. </div>";
                            } else {
                                echo "<br><div id='entry'>Username and Password combination did not match.<br><form action='index.php?page=profile&action=login' method='post'>Username:<input type='text' name='USERNAME' required='required' /><br>Password:<input type='password' name='PASSWORD' required='required' /><br><input type='submit' name='LOGIN' value='Login' /></form></div>";
                            }
                        } else {
                            echo "<br><div id='entry'><form action='index.php?page=profile&action=login' method='post'>Username:<input type='text' name='USERNAME' required='required' /><br>Password:<input type='password' name='PASSWORD' required='required' /><br><input type='submit' name='LOGIN' value='Login' /></form></div>";
                        }
                        break;
                    case 'register':
                        if (isset($_POST['REGISTER'])) {
                            $username = htmlspecialchars($_POST['USERNAME']);
                            $password = hash("sha256", htmlspecialchars($_POST['PASSWORD']));
                            $rcv = $bitcoin->getaccountaddress($username);
                            $register_user = "INSERT INTO users (USERNAME,PASSWORD,STATUS,ADDRESS) VALUES('$username','$password','USER','$rcv');";
                            $register = $db->query($register_user);
                            if ($register == 'true') {
                                $_SESSION['USERNAME'] = $username;
                                $_SESSION['STATUS'] = 'USER';
                                echo "<br><div id='entry'> Registration successfully!<br>Click this <a href='index.php?page=market'>Link</a> to proceed.";
                            }
                        } else {
                            echo "<br><div id='entry'><form action='index.php?page=profile&action=register' method='post'>Username:<input type='text' name='USERNAME' required='required' /><br>Password:<input type='password' name='PASSWORD' required='required' /><br><input type='submit' name='REGISTER' value='Register Account' /></form></div>";
                        }
                        break;
                    case 'logout':
                        session_destroy();
                        break;
                }
                if (isset($_SESSION['USERNAME'])) {
                    if (!isset($_POST['CHANGE'])) {
                        echo "<br><div id='entry'><form action='index.php?page=profile'>Enter your Public Key:<br><textarea name='PGP'></textarea><br><input type='submit' name='CHANGE' value='Change PGP' /></form></div>";
                    } else {
                        $change_key = "UPDATE users SET PGP = '$pgp' WHERE USERNAME = '$_SESSION[USERNAME]';";
                        $change = $db->query($change_key);
                        if ($change == 'true') {
                            echo "<br><div id='entry'>You successfully changed your Public Key!</div>";
                        }
                    }
                }
                break;
            case 'market':
                if (!isset($_GET['category'])) {
                    $select_content = "SELECT * FROM content ORDER BY SALES DESC LIMIT 0,5";
                    $content = $db->query($select_content);
                    while ($row = $content->fetch_assoc()) {
                        echo "<br><div id='entry'>$row[PRODUCT]<br>Price:$row[PRICE]<br>Rating: $row[RATING_UP]<img src='images/thumb_up.png'> $row[RATING_DOWN]<img src='images/thumb_down.png'><br>Put in Cart: <a href='index.php?page=orders&action=place&id=$row[ID]'>Click 2 Shop</a></div>";
                    }
                } else {
                    $select_content = "SELECT * FROM content WHERE CATEGORY = '$_GET[category]' ORDER BY SALES DESC LIMIT 0,5";
                    $content = $db->query($select_content);
                    while ($row = $content->fetch_assoc()) {
                        echo "<br><div id='entry'>$row[PRODUCT]<br>Price:$row[PRICE]<br>Rating: $row[RATING_UP]<img src='images/thumb_up.png'> $row[RATING_DOWN]<img src='images/thumb_down.png'><br>Put in Cart: <a href='index.php?page=orders&action=place&id=$row[ID]'>Click 2 Shop</a></div>";
                    }
                }
                break;
            case 'vendors':
                if (isset($_GET['profile'])) {
                    $select_content = "SELECT * FROM users WHERE USERNAME = '$_GET[profile]'";
                    $content = $db->query($select_content);
                    if ($content == 'true') {
                        $select_offers = "SELECT * FROM content WHERE USERNAME = '$_GET[profile]'";
                        $offers = $db->query($select_offers);
                        while ($row = $offers->fetch_assoc()) {
                            echo "<br><div id='entry'>$row[PRODUCT]<br>Price: $row[PRICE]<br>Listing: <a href='index.php?page=market&offer=$row[ID]'>Click</a></div>";
                        }
                    }
                }
                break;
            case 'orders':
                if (isset($_SESSION['USERNAME'])) {
                    switch ($_GET['action']) {
                        case 'check':
                            $select_orders = "SELECT * FROM orders WHERE VENDOR = '$_SESSION[USERNAME]';";
                            $orders = $db->query($select_orders);
                            while ($row = $orders->fetch_assoc()) {
                                $rcvd = $bitcoin->getreceivedbyaddress($row['RECEIVE_ADDRESS']);
                                if ($rcvd == '$row[PRICE]') {
                                    $update_order = "UPDATE orders SET STATUS = 'PAID' WHERE ID = '$row[ID]';";
                                }
                            }
                            break;
                        case 'place':
                            if (isset($_GET['id'])) {
                                if (!isset($_POST['PLACE'])) {
                                    $vendor = "SELECT VENDOR FROM content WHERE ID = '$_GET[id]';";
                                    $price = "SELECT PRICE FROM content WHERE ID = '$_GET[id]';";
                                    echo "<form method='post'><textarea name='SHIPPING'></textarea><br><input type='submit' name='PLACE' /></form>";
                                    if (isset($_POST['PLACE'])) {
                                        $shipto = $_POST['SHIPPING'];
                                        $new_tx = $bitcoin->getnewaddress($vendor);
                                        $place_order = "INSERT INTO orders (PRICE,SHIPPING_ADDRESS,RECEIVE_ADDRESS,USERNAME,VENDOR) VALUES('$price','$shipto','$new_tx','$_SESSION[USERNAME]','$vendor');";
                                        $place = $db->query($place_order);
                                        if ($place == 'true') {
                                            echo "Please send $price to the Bitcoin-Address: $new_tx to pay your order!";
                                        }
                                    }
                                }
                            }
                            break;
                    }
                }
                break;
            case 'offer':
                switch ($_GET['action']) {
                    case 'add':
                        if ($_SESSION['STATUS'] == 'VENDOR') {
                            if (!isset($_POST['ADD'])) {
                                $category = $_POST['CATEGORY'];
                                $price = $_POST['PRICE'];
                                $product = htmlspecialchars($_POST['PRODUCT']);
                                $description = htmlspecialchars($_POST['DESCRIPTION']);
                                $insert_offer = "INSERT INTO content (PRODUCT,CATEGORY,PRICE,DESCRIPTION,VENDOR) VALUES('$product','$category','$price','$description','$_SESSION[USERNAME]');";
                                $insert = $db->query($insert_offer);
                                if ($insert == 'true') {
                                    echo "<br><div id='entry'>Your Listing has successfully been added!</div>";
                                    echo "<br><div id='entry'><form action='index.php?page=offer&action=add' method='post'>Category:<select name='CATEGORY'><option value='AMPHETAMINE'>Amphetamine</option><option value='BARBITURATE'>Barbiturates</option><option value='BENZODIAZEPINE'>Benzodiazepines</option><option value='CANNABIS'>Cannabis</option><option value='COCAINE'>Cocaine</option><option value='DMT'>DMT</option><option value='DRUG'>Prescription</option><option value='HEROINE'>Diacetylmorphin</option><option value='LSD'>LSD</option><option value='METHAMPHETAMINE'>Methamphetamine</option><option value='MDMA'>MDMA</option><option value='OPIATES'>Opiates</option><option value='STEROID'>Steroids</option><option value='SEEDS'>Seeds</option><option value='RESEARCH_CHEMICAL'>Research Chemicals</option></select><br>Product: <input type='text' name='PRODUCT' /><br>Description:<textarea name='DESCRIPTION'></textarea><br>Price: <input type='text' name='PRICE' /><br><input type='submit' name='ADD' value='Add Listing' /></form></div>";

                                }

                            } else {
                                echo "<br><div id='entry'><form action='index.php?page=offer&action=add' method='post'>Category:<select name='CATEGORY'><option value='AMPHETAMINE'>Amphetamine</option><option value='BARBITURATE'>Barbiturates</option><option value='BENZODIAZEPINE'>Benzodiazepines</option><option value='CANNABIS'>Cannabis</option><option value='COCAINE'>Cocaine</option><option value='DMT'>DMT</option><option value='DRUG'>Prescription</option><option value='HEROINE'>Diacetylmorphin</option><option value='LSD'>LSD</option><option value='METHAMPHETAMINE'>Methamphetamine</option><option value='MDMA'>MDMA</option><option value='OPIATES'>Opiates</option><option value='STEROID'>Steroids</option><option value='SEEDS'>Seeds</option><option value='RESEARCH_CHEMICAL'>Research Chemicals</option></select><br>Product: <input type='text' name='PRODUCT' /><br>Description:<textarea name='DESCRIPTION'></textarea><br>Price: <input type='text' name='PRICE' /><br><input type='submit' name='ADD' value='Add Listing' /></form></div>";
                            }
                        }
                }
                break;
            default:
                $balance = $bitcoin->getbalance($_SESSION['USERNAME']);
                echo "<br>Your current Balance is: $balance BTC";
                if ($_SESSION['STATUS'] == 'USER') {
                    $select_content = "SELECT * FROM orders WHERE USERNAME = '$_SESSION[USERNAME]'";
                    $content = $db->query($select_content);
                    while ($row = $content->fetch_assoc()) {
                        echo "<br><div id='entry'>Ordernumber: $row[ID]<br>Status: $row[STATUS]<br>Vendor: $row[VENDOR]<br>Address to send $row[PRICE] BTC to: $row[RECEIVE_ADDRESS]</div>";
                    }
                } elseif ($_SESSION['STATUS'] == 'VENDOR') {
                    $select_content = "SELECT * FROM orders WHERE VENDOR = '$_SESSION[USERNAME]'";
                    $content = $db->query($select_content);
                    while ($row = $content->fetch_assoc()) {
                        echo "<br><div id='entry'>Ordernumber: $row[ID]<br>Status: $row[STATUS]<br>Shipping Address: $row[SHIPPING_ADDRESS]<br></div>";
                    }
                }

                break;
        }
        ?>
    </div>
    <div id="footer">
        &copy StYl3z @ 2019
    </div>
</body>
</html>
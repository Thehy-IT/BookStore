<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Books">
    <meta name="author" content="Shivangi Gupta">
    <title>Online Bookstore</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/my.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
</head>
<style>
    .modal-header {
        background: #D67B22;
        color: #fff;
        font-weight: 800;
    }

    .modal-body {
        font-weight: 800;
    }

    .modal-body ul {
        list-style: none;
    }

    .modal .btn {
        background: #D67B22;
        color: #fff;
    }

    .modal a {
        color: #D67B22;
    }

    .modal-backdrop {
        position: inherit !important;
    }

    #login_button,
    #register_button {
        background: none;
        color: #D67B22 !important;
    }

    #query_button {
        position: fixed;
        right: 0px;
        bottom: 0px;
        padding: 10px 80px;
        background-color: #D67B22;
        color: #fff;
        border-color: #f05f40;
        border-radius: 2px;
    }

    @media(max-width:767px) {
        #query_button {
            padding: 5px 20px;
        }
    }
</style>

<body>
    <?php
    session_start();
    include "dbconnect.php";
    function print_timeout_alert($message)
    {
        $safe_message = addslashes($message);

        print '
    <script type="text/javascript">
    (function() {
        // Tạo hộp thông báo
        var alertBox = document.createElement("div");
        alertBox.innerHTML = "<strong>localhost says:</strong><br>' . $safe_message . '";
       
        // CSS
        alertBox.style.position = "fixed";
        alertBox.style.top = 0;
        alertBox.style.left = "50%";
        alertBox.style.transform = "translateX(-50%)";
        alertBox.style.padding = "20px 30px";
        alertBox.style.minWidth = "400px";
        alertBox.style.backgroundColor = "white";
        alertBox.style.border = "1px solid #ccc";
        alertBox.style.borderRadius = "12px";
        alertBox.style.boxShadow = "0 4px 10px rgba(0,0,0,0.2)";
        alertBox.style.zIndex = "9999";
        alertBox.style.fontFamily = "Arial, sans-serif";
        alertBox.style.fontSize = "16px";

        // Thêm vào trang
        // Chờ trang tải xong rồi mới thêm, để đảm bảo <body> đã tồn tại
        document.addEventListener("DOMContentLoaded", function() {
             document.body.appendChild(alertBox);
        });

        // Hẹn giờ 2 giây (2000ms) rồi tự xóa
        setTimeout(function() {
            if (alertBox.parentNode) {
                alertBox.parentNode.removeChild(alertBox);
            }
        }, 2000);
    })();
    </script>
    ';
    }

    if (isset($_GET['Message'])) {
        print_timeout_alert($_GET['Message']);
    }

    if (isset($_GET['response'])) {
        print '<script type="text/javascript">
               alert("' . $_GET['response'] . '");
           </script>';
    }

    if (isset($_POST['submit'])) {
        if ($_POST['submit'] == "login") {
            $username = $_POST['login_username'];
            $password_input = $_POST['login_password'];
            $stmt = $con->prepare("SELECT * FROM users WHERE UserName = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                if (password_verify($password_input, $row['Password'])) {
                    $_SESSION['user'] = $row['UserName'];
                    print_timeout_alert("successfully logged in!!!");
                } else {
                    print_timeout_alert("Incorrect Username Or Password!!");
                }
            } else {
                print_timeout_alert("Incorrect Username Or Password!!");
            }
            $stmt->close();
        } else if ($_POST['submit'] == "register") {
            $username = $_POST['register_username'];
            $password = $_POST['register_password'];
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $con->prepare("INSERT INTO users (UserName, Password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hashed_password);
            if ($stmt->execute()) {
                print_timeout_alert("Successfully Registered!!!");
            } else {
                print_timeout_alert("username is taken");
            }
        }
    }
    ?>
    <!-- ============== nav  ==============-->
    <nav class="navbar navbar-default navbar-fixed-top navbar-inverse">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <!-- ================================================================================================================================================ -->
                <!-- <a class="navbar-brand" href="#" style="padding: 1px;"><img class="img-responsive" alt="BookZ"
                            src="img/logo.jpg" style="width: 147px;margin: 0px;"></a> -->
            </div>

            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <?php
                    if (!isset($_SESSION['user'])) {
                        echo '
                    <li class="dropdown" id="account-dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <span class="glyphicon glyphicon-user"></span> Account <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><button type="button" class="btn-dropdown" data-toggle="modal" data-target="#login">Login</button></li>
                            <li><button type="button" class="btn-dropdown" data-toggle="modal" data-target="#register">Sign Up</button></li>
                        </ul>
                    </li>';
                    } else {
                        echo '
                    <li>
                    <a href="#" class="nav-icon-link">
                        <span class="glyphicon glyphicon-user"></span> ' . $_SESSION['user'] . '
                    </a>
                    </li>
                    <li>
                    <a href="cart.php" class="nav-icon-link">
                        <span class="glyphicon glyphicon-shopping-cart"></span> Cart
                    </a>
                    </li>
                    <li>
                    <a href="destroy.php" class="nav-icon-link" style="text-transform: capitalize;">
                        <span class="glyphicon glyphicon-log-out"></span> LogOut
                    </a>
                    </li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
    </nav>

    <div id="login" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title text-center">Login Form</h4>
                </div>
                <div class="modal-body">
                    <form class="form" role="form" method="post" action="index.php" accept-charset="UTF-8">
                        <div class="form-group">
                            <label class="sr-only" for="username">Username</label>
                            <input type="text" name="login_username" class="form-control" placeholder="Username"
                                required>
                        </div>
                        <div class="form-group">
                            <label class="sr-only" for="password">Password</label>
                            <input type="password" name="login_password" class="form-control" placeholder="Password"
                                required>
                        </div>
                        <div class="form-group">
                            <button type="submit" name="submit" value="login" class="btn btn-block">
                                Sign in
                            </button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="register" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title text-center">Member Registration Form</h4>
                </div>
                <div class="modal-body">
                    <form class="form" role="form" method="post" action="index.php" accept-charset="UTF-8">
                        <div class="form-group">
                            <label class="sr-only" for="username">Username</label>
                            <input type="text" name="register_username" class="form-control" placeholder="Username"
                                required>
                        </div>
                        <div class="form-group">
                            <label class="sr-only" for="password">Password</label>
                            <input type="password" name="register_password" class="form-control" placeholder="Password"
                                required>
                        </div>
                        <div class="form-group">
                            <button type="submit" name="submit" value="register" class="btn btn-block">
                                Sign Up
                            </button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ============== top  ==============-->
    <div id="top" style="position: relative;">
        <!-- <div id="searchbox" class="container-fluid" style="width:112%;margin-left:-6%;margin-right:-6%;"> -->
        <div id="searchbox" class="container-fluid">
            <div class="search-container">
                <button id="category-menu-toggle" type="button">
                    <span class="glyphicon glyphicon-align-justify"></span>
                </button>
                <form role="search" method="POST" action="Result.php" class="search-form">
                    <input type="text" class="form-control" name="keyword"
                        placeholder="Search for a Book , Author Or Category">
                </form>
            </div>

        </div>

        <div id="category">
            <div style="background:#D67B22;color:#fff;font-weight:800;border:none;padding:15px;"> The Book Shop
            </div>
            <ul>
                <li> <a href="Product.php?value=entrance%20exam"> Entrance Exam </a> </li>
                <li> <a href="Product.php?value=Literature%20and%20Fiction"> Literature & Fiction </a> </li>
                <li> <a href="Product.php?value=Academic%20and%20Professional"> Academic & Professional </a>
                </li>
                <li> <a href="Product.php?value=Biographies%20and%20Auto%20Biographies"> Biographies & Auto
                        Biographies
                    </a> </li>
                <li> <a href="Product.php?value=Children%20and%20Teens"> Children & Teens </a> </li>
                <li> <a href="Product.php?value=Regional%20Books"> Regional Books </a> </li>
                <li> <a href="Product.php?value=Business%20and%20Management"> Business & Management </a> </li>
                <li> <a href="Product.php?value=Health%20and%20Cooking"> Health and Cooking </a> </li>
            </ul>
        </div>

    </div>

    <!-- ============== container-fluid  ==============-->
    <div class="container-fluid" id="header">
        <div class="row">

            <div class="col-md-9 col-lg-9">
                <div id="myCarousel" class="carousel slide carousel-fade" data-ride="carousel">
                    <ol class="carousel-indicators">
                        <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                        <li data-target="#myCarousel" data-slide-to="1"></li>
                        <li data-target="#myCarousel" data-slide-to="2"></li>
                        <li data-target="#myCarousel" data-slide-to="3"></li>
                        <li data-target="#myCarousel" data-slide-to="4"></li>
                        <li data-target="#myCarousel" data-slide-to="5"></li>
                    </ol>

                    <div class="carousel-inner" role="listbox">
                        <div class="item active">
                            <img class="img-responsive" src="img/carousel/1.jpg">
                        </div>
                        <div class="item">
                            <img class="img-responsive " src="img/carousel/2.jpg">
                        </div>
                        <div class="item">
                            <img class="img-responsive" src="img/carousel/3.jpg">
                        </div>
                        <div class="item">
                            <img class="img-responsive" src="img/carousel/4.jpg">
                        </div>
                        <div class="item">
                            <img class="img-responsive" src="img/carousel/5.jpg">
                        </div>
                        <div class="item">
                            <img class="img-responsive" src="img/carousel/6.jpg">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-lg-3" id="offer">
                <a href="Product.php?value=Regional%20Books"> <img class="img-responsive center-block"
                        src="img/offers/1.png"></a>
                <a href="Product.php?value=Health%20and%20Cooking"> <img class="img-responsive center-block"
                        src="img/offers/2.png"></a>
                <a href="Product.php?value=Academic%20and%20Professional"> <img class="img-responsive center-block"
                        src="img/offers/3.png"></a>
            </div>
        </div>
    </div>

    <!-- ============== flash sale  ==============-->
    <div class="fs_bg">
        <div id="flashsale-slider" style="display: block;">
            <div class="flashsale_header fhs_center_space">
                <div class="fhs_center_left">
                    <a style="position: relative;" class="flashsale-slider-title" href="flashsale">
                        <img src="img/label-flashsale.svg">
                        <div class="margin_left_big" style="margin-top:2px"><span
                                style="color:black; font-weight:600; font-size:1.2em; font-family:'Nunito Sans'"
                                class="flashsale-page-countdown-label">Ending in</span></div>
                        <div class="flashsale-countdown margin_left_normal">
                            <span class="flashsale-countdown-temp"></span>
                            <span id="fs_hours" class="flashsale-countdown-number">00</span>
                            <span>:</span>
                            <span id="fs_minutes" class="flashsale-countdown-number">00</span>
                            <span>:</span>
                            <span id="fs_seconds" class="flashsale-countdown-number">00</span>
                        </div>
                    </a>
                </div>
                <a style="position: relative;" class="fhs_center_right padding_left_big" href="flashsale">
                    <span class="icon_seemore_blue desktop_only"></span>
                    <span class="icon_seemore_gray right mobile_only"></span>
                </a>
            </div>

            <div class="fhs-product-slider-content" style="position:relative;">
                <div id="flashsale_slider150976" class="swiper">
                    <ul id="flashsale_grid_item150976" class="swiper-wrapper fhs-product-slider-list"
                        style="transform: translate3d(0px, 0px, 0px);">
                        <li class="fhs_product_basic swiper-slide flashsale-item swiper-slide-active">
                            <div class="item-inner">
                                <div class="ma-box-content">
                                    <div class="products clear">
                                        <div class="product images-container"><a href="#"
                                                title="Kế Toán Vỉa Hè - Thực Hành Báo Cáo Tài Chính Căn Bản Từ Quầy Bán Nước Chanh"
                                                class="product-image">
                                                <div class="product-image"><img class=" lazyloaded"
                                                        src="https://cdn1.fahasa.com/media/catalog/product/9/7/9786047787616.jpg"
                                                        data-src="https://cdn1.fahasa.com/media/catalog/product/9/7/9786047787616.jpg"
                                                        alt="Kế Toán Vỉa Hè - Thực Hành Báo Cáo Tài Chính Căn Bản Từ Quầy Bán Nước Chanh">
                                                </div>
                                            </a></div>
                                    </div>
                                    <div>
                                        <h2 class="product-name-no-ellipsis"><a href="#"
                                                title="Kế Toán Vỉa Hè - Thực Hành Báo Cáo Tài Chính Căn Bản Từ Quầy Bán Nước Chanh"><span>Kế
                                                    Toán Vỉa Hè - Thực Hành Báo Cáo Tài Chính Căn Bản Từ Quầy Bán
                                                    Nước Chanh</span></a></h2>
                                        <div class="price-label">
                                            <p class="special-price"><span
                                                    class="price m-price-font fhs_center_left">119.400 đ</span><span
                                                    class="discount-percent fhs_center_left">-40%</span></p>
                                            <p class="old-price"><span class="price">199.000</span></p>
                                        </div>
                                        <div class="clear"></div>
                                        <div class="progress"><span class="progress-value">Sold 12</span>
                                            <div class="progress-bar" role="progressbar"
                                                style="width: 25.53191489361702%;" aria-valuenow="25.53191489361702"
                                                aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li class="fhs_product_basic swiper-slide flashsale-item swiper-slide-next">
                            <div class="item-inner">
                                <div class="ma-box-content">
                                    <div class="products clear">
                                        <div class="product images-container"><a href="#"
                                                title="Người Giàu Có Nhất Thành Babylon" class="product-image">
                                                <div class="product-image"><img class=" lazyloaded"
                                                        src="https://cdn1.fahasa.com/media/catalog/product/9/7/9786043941807.jpg"
                                                        data-src="https://cdn1.fahasa.com/media/catalog/product/9/7/9786043941807.jpg"
                                                        alt="Người Giàu Có Nhất Thành Babylon"></div>
                                            </a></div>
                                    </div>
                                    <div>
                                        <h2 class="product-name-no-ellipsis"><a href="#"
                                                title="Người Giàu Có Nhất Thành Babylon"><span>Người Giàu Có Nhất
                                                    Thành Babylon</span></a></h2>
                                        <div class="price-label">
                                            <p class="special-price"><span
                                                    class="price m-price-font fhs_center_left">50.000 đ</span><span
                                                    class="discount-percent fhs_center_left">-48%</span></p>
                                            <p class="old-price"><span class="price">98.000</span></p>
                                        </div>
                                        <div class="clear"></div>
                                        <div class="progress"><span class="progress-value">Sold 20</span>
                                            <div class="progress-bar" role="progressbar"
                                                style="width: 36.36363636363637%;" aria-valuenow="36.36363636363637"
                                                aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li class="fhs_product_basic swiper-slide flashsale-item">
                            <div class="item-inner">
                                <div class="ma-box-content">
                                    <div class="products clear">
                                        <div class="product images-container"><a href="#"
                                                title="Lược Sử Kinh Tế Học Lầy Lội - Khủng Hoảng Dạy Cho Ta Những Gì? - Tập 1"
                                                class="product-image">
                                                <div class="product-image"><img class=" lazyloaded"
                                                        src="https://cdn1.fahasa.com/media/catalog/product/8/9/8935246944660.jpg"
                                                        data-src="https://cdn1.fahasa.com/media/catalog/product/8/9/8935246944660.jpg"
                                                        alt="Lược Sử Kinh Tế Học Lầy Lội - Khủng Hoảng Dạy Cho Ta Những Gì? - Tập 1">
                                                </div>
                                                <!-- <div class="episode-label">Tập 1</div> -->
                                            </a></div>
                                    </div>
                                    <div>
                                        <h2 class="product-name-no-ellipsis"><a href="#"
                                                title="Lược Sử Kinh Tế Học Lầy Lội - Khủng Hoảng Dạy Cho Ta Những Gì? - Tập 1"><span>Lược
                                                    Sử Kinh Tế Học Lầy Lội - Khủng Hoảng Dạy Cho Ta Những Gì? - Tập
                                                    1</span></a></h2>
                                        <div class="price-label">
                                            <p class="special-price"><span
                                                    class="price m-price-font fhs_center_left">155.000 đ</span><span
                                                    class="discount-percent fhs_center_left">-32%</span></p>
                                            <p class="old-price"><span class="price">229.000</span></p>
                                        </div>
                                        <div class="clear"></div>
                                        <div class="progress"><span class="progress-value">Sold 7</span>
                                            <div class="progress-bar" role="progressbar"
                                                style="width: 9.333333333333334%;" aria-valuenow="9.333333333333334"
                                                aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li class="fhs_product_basic swiper-slide flashsale-item">
                            <div class="item-inner">
                                <div class="ma-box-content">
                                    <div class="products clear">
                                        <div class="product images-container"><a href="#"
                                                title="Lược Sử Kinh Tế Học Lầy Lội - Khủng Hoảng Dạy Cho Ta Những Gì? - Tập 2"
                                                class="product-image">
                                                <div class="product-image"><img class=" lazyloaded"
                                                        src="https://cdn1.fahasa.com/media/catalog/product/8/9/8935246944677.jpg"
                                                        data-src="https://cdn1.fahasa.com/media/catalog/product/8/9/8935246944677.jpg"
                                                        alt="Lược Sử Kinh Tế Học Lầy Lội - Khủng Hoảng Dạy Cho Ta Những Gì? - Tập 2">
                                                </div>
                                                <!-- <div class="episode-label">Tập 2</div> -->
                                            </a></div>
                                    </div>
                                    <div>
                                        <h2 class="product-name-no-ellipsis"><a href="#"
                                                title="Lược Sử Kinh Tế Học Lầy Lội - Khủng Hoảng Dạy Cho Ta Những Gì? - Tập 2"><span>Lược
                                                    Sử Kinh Tế Học Lầy Lội - Khủng Hoảng Dạy Cho Ta Những Gì? - Tập
                                                    2</span></a></h2>
                                        <div class="price-label">
                                            <p class="special-price"><span
                                                    class="price m-price-font fhs_center_left">121.000 đ</span><span
                                                    class="discount-percent fhs_center_left">-32%</span></p>
                                            <p class="old-price"><span class="price">179.000</span></p>
                                        </div>
                                        <div class="clear"></div>
                                        <div class="progress"><span class="progress-value">Sold 16</span>
                                            <div class="progress-bar" role="progressbar"
                                                style="width: 25.806451612903224%;" aria-valuenow="25.806451612903224"
                                                aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li class="fhs_product_basic swiper-slide flashsale-item">
                            <div class="item-inner">
                                <div class="ma-box-content">
                                    <div class="products clear">
                                        <div class="product images-container"><a href="#"
                                                title="Chiến Tranh Tiền Tệ - Phần 1 - Ai Thực Sự Là Người Giàu Nhất Thế Giới? (Tái bản 2025)"
                                                class="product-image">
                                                <div class="product-image"><img class=" lazyloaded"
                                                        src="https://cdn1.fahasa.com/media/catalog/product/1/7/1743385319248.jpg"
                                                        data-src="https://cdn1.fahasa.com/media/catalog/product/1/7/1743385319248.jpg"
                                                        alt="Chiến Tranh Tiền Tệ - Phần 1 - Ai Thực Sự Là Người Giàu Nhất Thế Giới? (Tái bản 2025)">
                                                </div>
                                                <!-- <div class="episode-label">Tập 1</div> -->
                                            </a></div>
                                    </div>
                                    <div>
                                        <h2 class="product-name-no-ellipsis"><a href="#"
                                                title="Chiến Tranh Tiền Tệ - Phần 1 - Ai Thực Sự Là Người Giàu Nhất Thế Giới? (Tái bản 2025)"><span>Chiến
                                                    Tranh Tiền Tệ - Phần 1 - Ai Thực Sự Là Người Giàu Nhất Thế Giới?
                                                    (Tái bản 2025)</span></a></h2>
                                        <div class="price-label">
                                            <p class="special-price"><span
                                                    class="price m-price-font fhs_center_left">153.000 đ</span><span
                                                    class="discount-percent fhs_center_left">-17%</span></p>
                                            <p class="old-price"><span class="price">185.000</span></p>
                                        </div>
                                        <div class="clear"></div>
                                        <div class="progress"><span class="progress-value">Sold 20</span>
                                            <div class="progress-bar" role="progressbar"
                                                style="width: 34.48275862068966%;" aria-valuenow="34.48275862068966"
                                                aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li class="fhs_product_basic swiper-slide flashsale-item">
                            <div class="item-inner">
                                <div class="ma-box-content">
                                    <div class="products clear">
                                        <div class="product images-container"><a href="#"
                                                title="Chiến Tranh Tiền Tệ Phần IV: Siêu Cường Về Tài Chính - Tham Vọng Về Đồng Tiền Chung Châu Á"
                                                class="product-image">
                                                <div class="product-image"><img class=" lazyloaded"
                                                        src="https://cdn1.fahasa.com/media/catalog/product/9/7/9786043603590.jpg"
                                                        data-src="https://cdn1.fahasa.com/media/catalog/product/9/7/9786043603590.jpg"
                                                        alt="Chiến Tranh Tiền Tệ Phần IV: Siêu Cường Về Tài Chính - Tham Vọng Về Đồng Tiền Chung Châu Á">
                                                </div>
                                                <!-- <div class="episode-label">Tập 4</div> -->
                                            </a></div>
                                    </div>
                                    <div>
                                        <h2 class="product-name-no-ellipsis"><a href="#"
                                                title="Chiến Tranh Tiền Tệ Phần IV: Siêu Cường Về Tài Chính - Tham Vọng Về Đồng Tiền Chung Châu Á"><span>Chiến
                                                    Tranh Tiền Tệ Phần IV: Siêu Cường Về Tài Chính - Tham Vọng Về
                                                    Đồng Tiền Chung Châu Á</span></a></h2>
                                        <div class="price-label">
                                            <p class="special-price"><span
                                                    class="price m-price-font fhs_center_left">153.000 đ</span><span
                                                    class="discount-percent fhs_center_left">-17%</span></p>
                                            <p class="old-price"><span class="price">185.000</span></p>
                                        </div>
                                        <div class="clear"></div>
                                        <div class="progress"><span class="progress-value">Sold 6</span>
                                            <div class="progress-bar" role="progressbar"
                                                style="width: 8.450704225352112%;" aria-valuenow="8.450704225352112"
                                                aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li class="fhs_product_basic swiper-slide flashsale-item">
                            <div class="item-inner">
                                <div class="ma-box-content">
                                    <div class="products clear">
                                        <div class="product images-container"><a href="#"
                                                title="Kinh Tế Học Thiêng Liêng - Tiền Bạc, Quà Tặng Và Xã Hội Trong Thời Đại Chuyển Giao (Tái Bản 2024)"
                                                class="product-image">
                                                <div class="product-image"><img class=" lazyloaded"
                                                        src="https://cdn1.fahasa.com/media/catalog/product/9/7/9786048458034_1.jpg"
                                                        data-src="https://cdn1.fahasa.com/media/catalog/product/9/7/9786048458034_1.jpg"
                                                        alt="Kinh Tế Học Thiêng Liêng - Tiền Bạc, Quà Tặng Và Xã Hội Trong Thời Đại Chuyển Giao (Tái Bản 2024)">
                                                </div>
                                            </a></div>
                                    </div>
                                    <div>
                                        <h2 class="product-name-no-ellipsis"><a href="#"
                                                title="Kinh Tế Học Thiêng Liêng - Tiền Bạc, Quà Tặng Và Xã Hội Trong Thời Đại Chuyển Giao (Tái Bản 2024)"><span>Kinh
                                                    Tế Học Thiêng Liêng - Tiền Bạc, Quà Tặng Và Xã Hội Trong Thời
                                                    Đại Chuyển Giao (Tái Bản 2024)</span></a></h2>
                                        <div class="price-label">
                                            <p class="special-price"><span
                                                    class="price m-price-font fhs_center_left">280.000 đ</span><span
                                                    class="discount-percent fhs_center_left">-20%</span></p>
                                            <p class="old-price"><span class="price">350.000</span></p>
                                        </div>
                                        <div class="clear"></div>
                                        <div class="progress"><span class="progress-value">Sold 17</span>
                                            <div class="progress-bar" role="progressbar"
                                                style="width: 30.909090909090907%;" aria-valuenow="30.909090909090907"
                                                aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li class="fhs_product_basic swiper-slide flashsale-item">
                            <div class="item-inner">
                                <div class="ma-box-content">
                                    <div class="products clear">
                                        <div class="product images-container"><a href="#"
                                                title="Phương Thức Toyota: Câu Chuyện Về Đội Nhóm Tuyệt Mật Đã Làm Nên Thành Công Của Toyota"
                                                class="product-image">
                                                <div class="product-image"><img class=" lazyloaded"
                                                        src="https://cdn1.fahasa.com/media/catalog/product/i/m/image_195509_1_10684.jpg"
                                                        data-src="https://cdn1.fahasa.com/media/catalog/product/i/m/image_195509_1_10684.jpg"
                                                        alt="Phương Thức Toyota: Câu Chuyện Về Đội Nhóm Tuyệt Mật Đã Làm Nên Thành Công Của Toyota">
                                                </div>
                                            </a></div>
                                    </div>
                                    <div>
                                        <h2 class="product-name-no-ellipsis"><a href="#"
                                                title="Phương Thức Toyota: Câu Chuyện Về Đội Nhóm Tuyệt Mật Đã Làm Nên Thành Công Của Toyota"><span>Phương
                                                    Thức Toyota: Câu Chuyện Về Đội Nhóm Tuyệt Mật Đã Làm Nên Thành
                                                    Công Của Toyota</span></a></h2>
                                        <div class="price-label">
                                            <p class="special-price"><span
                                                    class="price m-price-font fhs_center_left">119.000 đ</span><span
                                                    class="discount-percent fhs_center_left">-40%</span></p>
                                            <p class="old-price"><span class="price">199.000</span></p>
                                        </div>
                                        <div class="clear"></div>
                                        <div class="progress"><span class="progress-value">Sold 6</span>
                                            <div class="progress-bar" role="progressbar"
                                                style="width: 13.043478260869565%;" aria-valuenow="13.043478260869565"
                                                aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li class="fhs_product_basic swiper-slide flashsale-item">
                            <div class="item-inner">
                                <div class="ma-box-content">
                                    <div class="products clear">
                                        <div class="product images-container"><a href="#"
                                                title="Thuật Dùng Người - Bí Quyết Để Trở Thành Nhà Quản Lí Tài Ba"
                                                class="product-image">
                                                <div class="product-image"><img class="lazyload"
                                                        src="https://cdn1.fahasa.com/media/fahasa_web_image/product_skeleton.png"
                                                        data-src="https://cdn1.fahasa.com/media/catalog/product/8/9/8936067608755.jpg"
                                                        alt="Thuật Dùng Người - Bí Quyết Để Trở Thành Nhà Quản Lí Tài Ba">
                                                </div>
                                            </a></div>
                                    </div>
                                    <div>
                                        <h2 class="product-name-no-ellipsis"><a
                                                href="https://www.fahasa.com/thuat-dung-nguoi-bi-quyet-de-tro-thanh-nha-quan-li-tai-ba.html?fhs_campaign=FLASHSALE"
                                                title="Thuật Dùng Người - Bí Quyết Để Trở Thành Nhà Quản Lí Tài Ba"><span>Thuật
                                                    Dùng Người - Bí Quyết Để Trở Thành Nhà Quản Lí Tài Ba</span></a>
                                        </h2>
                                        <div class="price-label">
                                            <p class="special-price"><span
                                                    class="price m-price-font fhs_center_left">84.000 đ</span><span
                                                    class="discount-percent fhs_center_left">-30%</span></p>
                                            <p class="old-price"><span class="price">120.000</span></p>
                                        </div>
                                        <div class="clear"></div>
                                        <div class="progress"><span class="progress-value">Sold 18</span>
                                            <div class="progress-bar" role="progressbar"
                                                style="width: 33.9622641509434%;" aria-valuenow="33.9622641509434"
                                                aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li class="fhs_product_basic swiper-slide flashsale-item">
                            <div class="item-inner">
                                <div class="ma-box-content">
                                    <div class="products clear">
                                        <div class="product images-container"><a href="#"
                                                title="Bertelsmann - Ẩn Mình Trong Trụ Sở Nhỏ Bé Nhưng Vẫn Là Hoàng Đế Của Vũ Trụ Truyền Thông"
                                                class="product-image">
                                                <div class="product-image"><img class="lazyload"
                                                        src="https://cdn1.fahasa.com/media/fahasa_web_image/product_skeleton.png"
                                                        data-src="https://cdn1.fahasa.com/media/catalog/product/b/e/bertelsmann_1.jpg"
                                                        alt="Bertelsmann - Ẩn Mình Trong Trụ Sở Nhỏ Bé Nhưng Vẫn Là Hoàng Đế Của Vũ Trụ Truyền Thông">
                                                </div>
                                            </a></div>
                                    </div>
                                    <div>
                                        <h2 class="product-name-no-ellipsis"><a href="#"
                                                title="Bertelsmann - Ẩn Mình Trong Trụ Sở Nhỏ Bé Nhưng Vẫn Là Hoàng Đế Của Vũ Trụ Truyền Thông"><span>Bertelsmann
                                                    - Ẩn Mình Trong Trụ Sở Nhỏ Bé Nhưng Vẫn Là Hoàng Đế Của Vũ Trụ
                                                    Truyền Thông</span></a></h2>
                                        <div class="price-label">
                                            <p class="special-price"><span
                                                    class="price m-price-font fhs_center_left">104.000 đ</span><span
                                                    class="discount-percent fhs_center_left">-20%</span></p>
                                            <p class="old-price"><span class="price">130.000</span></p>
                                        </div>
                                        <div class="clear"></div>
                                        <div class="progress"><span class="progress-value">Sold 8</span>
                                            <div class="progress-bar" role="progressbar"
                                                style="width: 11.267605633802818%;" aria-valuenow="11.267605633802818"
                                                aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
                </div>
                <div id="fhs-tab-slider-prev150976" class="fhs-tab-slider-prev position-tab-prev swiper-button-prev">
                </div>
                <div id="fhs-tab-slider-next150976" class="fhs-tab-slider-next position-tab-next swiper-button-next">
                </div>
            </div>
        </div>
        <div class="fs_bg_2" style="display: block;"></div>
    </div>

    <!-- ============== new books  ==============-->
    <div class="container-fluid text-center" id="new">
        <div class="row">
            <div class="col-sm-6 col-md-3 col-lg-3">
                <a href="description.php?ID=NEW-1&category=new">
                    <div class="book-block">
                        <div class="tag">New</div>
                        <div class="tag-side"><img src="img/tag.png"></div>
                        <img class="book block-center img-responsive" src="img/new/1.jpg">
                        <hr>
                        Like A Love Song <br>
                        Rs 113 &nbsp
                        <span style="text-decoration:line-through;color:#828282;"> 175 </span>
                        <span class="label label-warning">35%</span>
                    </div>
                </a>
            </div>

            <div class="col-sm-6 col-md-3 col-lg-3">
                <a href="description.php?ID=NEW-2&category=new">
                    <div class="book-block">
                        <div class="tag">New</div>
                        <div class="tag-side"><img src="img/tag.png"></div>
                        <img class="block-center img-responsive" src="img/new/2.jpg">
                        <hr>
                        General Knowledge 2017 <br>
                        Rs 68 &nbsp
                        <span style="text-decoration:line-through;color:#828282;"> 120 </span>
                        <span class="label label-warning">43%</span>
                    </div>
                </a>
            </div>

            <div class="col-sm-6 col-md-3 col-lg-3">
                <a href="description.php?ID=NEW-3&category=new">
                    <div class="book-block">
                        <div class="tag">New</div>
                        <div class="tag-side"><img src="img/tag.png"></div>
                        <img class="block-center img-responsive" src="img/new/3.png">
                        <hr>
                        Indian Family Bussiness Mantras <br>
                        Rs 400 &nbsp
                        <span style="text-decoration:line-through;color:#828282;"> 595 </span>
                        <span class="label label-warning">33%</span>
                    </div>
                </a>
            </div>

            <div class="col-sm-6 col-md-3 col-lg-3">
                <a href="description.php?ID=NEW-4&category=new">
                    <div class="book-block">
                        <div class="tag">New</div>
                        <div class="tag-side"><img src="img/tag.png"></div>
                        <img class="block-center img-responsive" src="img/new/4.jpg">
                        <hr>
                        Kiran s SSC Mathematics Chapterwise Solutions <br>
                        Rs 289 &nbsp
                        <span style="text-decoration:line-through;color:#828282;"> 435 </span>
                        <span class="label label-warning">33%</span>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- ============== BXH  ==============-->
    <div class="container-fluid" id="bestseller-section">
        <div class="bestseller-container">

            <div class="bestseller-header">
                <h3>Weekly Bestsellers Chart</h3>
                <ul class="nav nav-tabs bestseller-tabs">
                    <li class="active"><a href="#tab-van-hoc" data-toggle="tab">Literature</a></li>
                    <li><a href="#tab-kinh-te" data-toggle="tab">Economics</a></li>
                    <li><a href="#tab-thieu-nhi" data-toggle="tab">Children</a></li>
                </ul>
            </div>

            <div class="tab-content">
                <div class="tab-pane active" id="tab-van-hoc">
                    <div class="row">
                        <div class="col-md-5">
                            <ul class="bestseller-list">
                                <li class="bestseller-item active" data-target="vh-book-1">
                                    <div class="rank-number">01</div>
                                    <div class="rank-arrow">
                                        <span class="glyphicon glyphicon-arrow-up" style="color: green;"></span>
                                    </div>
                                    <div class="media">
                                        <div class="media-left">
                                            <img class="media-object" src="img/new/1.jpg" alt="Mưa Đỏ">
                                        </div>
                                        <div class="media-body">
                                            <h4 class="media-heading">Red Rain</h4>
                                            <p>Chu Lai</p>
                                            <p><strong>5841 points</strong></p>
                                        </div>
                                    </div>
                                </li>

                                <li class="bestseller-item" data-target="vh-book-2">
                                    <div class="rank-number">02</div>
                                    <div class="rank-arrow">
                                        <span class="glyphicon glyphicon-arrow-up" style="color: green;"></span>
                                    </div>
                                    <div class="media">
                                        <div class="media-left">
                                            <img class="media-object" src="img/new/2.jpg" alt="Hồ Điệp">
                                        </div>
                                        <div class="media-body">
                                            <h4 class="media-heading">Butterfly and Whale</h4>
                                            <p>Tuệ Kiên</p>
                                            <p><strong>1082 points</strong></p>
                                        </div>
                                    </div>
                                </li>

                                <li class="bestseller-item" data-target="vh-book-3">
                                    <div class="rank-number">03</div>
                                    <div class="rank-arrow">
                                        <span class="glyphicon glyphicon-arrow-up" style="color: green;"></span>
                                    </div>
                                    <div class="media">
                                        <div class="media-left">
                                            <img class="media-object" src="img/new/3.png" alt="Nhà Giả Kim">
                                        </div>
                                        <div class="media-body">
                                            <h4 class="media-heading">The Alchemist (2020 Reprint)</h4>
                                            <p>Paulo Coelho</p>
                                            <p><strong>867 points</strong></p>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <div class="col-md-7">
                            <div class="bestseller-detail-content">

                                <div class="bestseller-pane active" id="vh-book-1">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <img src="img/new/1.jpg" class="detail-cover img-responsive" alt="Mưa Đỏ">
                                        </div>
                                        <div class="col-md-7">
                                            <h3>Red Rain</h3>
                                            <p class="detail-meta">Author: <strong>Chu Lai</strong></p>
                                            <p class="detail-meta">Publisher: <strong>People's Army</strong></p>

                                            <div class="detail-price">
                                                <span class="new-price">184.500 đ</span>
                                                <span class="discount-label">-10%</span>
                                            </div>
                                            <div class="old-price-wrapper">
                                                <span class="old-price">205.000 đ</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p class="detail-description">
                                                Intertwined emotional arrays between smiles - tears, pain - joy, life - death, sublimation - loss, the sacrifice of fathers, husbands, sons, soldiers, comrades.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bestseller-pane" id="vh-book-2">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <img src="img/new/2.jpg" class="detail-cover img-responsive" alt="Hồ Điệp">
                                        </div>
                                        <div class="col-md-7">
                                            <h3>Butterfly and Whale</h3>
                                            <p class="detail-meta">Author: <strong>Tuệ Kiên</strong></p>
                                            <p class="detail-meta">Publisher: <strong>...</strong></p>

                                            <div class="detail-price">
                                                <span class="new-price">99.000 đ</span>
                                                <span class="discount-label">-15%</span>
                                            </div>
                                            <div class="old-price-wrapper">
                                                <span class="old-price">116.000 đ</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p class="detail-description">
                                                A captivating story from the very first pages - When love becomes a fragile thread between life and death, betrayal and hope. When a small butterfly encounters a mighty whale, is it destiny or just a fleeting dream?
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bestseller-pane" id="vh-book-3">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <img src="img/new/3.png" class="detail-cover img-responsive"
                                                alt="Nhà Giả Kim">
                                        </div>
                                        <div class="col-md-7">
                                            <h3>The Alchemist (2020 Reprint)</h3>
                                            <p class="detail-meta">Author: <strong>Paulo Coelho</strong></p>
                                            <p class="detail-meta">Publisher: <strong>...</strong></p>

                                            <div class="detail-price">
                                                <span class="new-price">79.000 đ</span>
                                                <span class="discount-label">-20%</span>
                                            </div>
                                            <div class="old-price-wrapper">
                                                <span class="old-price">99.000 đ</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p class="detail-description">
                                                Description for The Alchemist...
                                            </p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="tab-kinh-te">
                    <div class="row">
                        <div class="col-md-5">
                            <ul class="bestseller-list">
                                <li class="bestseller-item active" data-target="kt-book-1">
                                    <div class="rank-number">01</div>
                                    <div class="rank-arrow">
                                        <span class="glyphicon glyphicon-arrow-up" style="color: green;"></span>
                                    </div>
                                    <div class="media">
                                        <div class="media-left">
                                            <img class="media-object" src="img/new/1.jpg" alt="Mưa Đỏ">
                                        </div>
                                        <div class="media-body">
                                            <h4 class="media-heading">Red Rain</h4>
                                            <p>Chu Lai</p>
                                            <p><strong>5841 points</strong></p>
                                        </div>
                                    </div>
                                </li>

                                <li class="bestseller-item" data-target="kt-book-2">
                                    <div class="rank-number">02</div>
                                    <div class="rank-arrow">
                                        <span class="glyphicon glyphicon-arrow-up" style="color: green;"></span>
                                    </div>
                                    <div class="media">
                                        <div class="media-left">
                                            <img class="media-object" src="img/new/2.jpg" alt="Hồ Điệp">
                                        </div>
                                        <div class="media-body">
                                            <h4 class="media-heading">Butterfly and Whale</h4>
                                            <p>Tuệ Kiên</p>
                                            <p><strong>1082 points</strong></p>
                                        </div>
                                    </div>
                                </li>

                                <li class="bestseller-item" data-target="kt-book-3">
                                    <div class="rank-number">03</div>
                                    <div class="rank-arrow">
                                        <span class="glyphicon glyphicon-arrow-up" style="color: green;"></span>
                                    </div>
                                    <div class="media">
                                        <div class="media-left">
                                            <img class="media-object" src="img/new/3.png" alt="Nhà Giả Kim">
                                        </div>
                                        <div class="media-body">
                                            <h4 class="media-heading">The Alchemist (2020 Reprint)</h4>
                                            <p>Paulo Coelho</p>
                                            <p><strong>867 points</strong></p>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <div class="col-md-7">
                            <div class="bestseller-detail-content">
                                <div class="bestseller-pane active" id="kt-book-1">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <img src="img/new/1.jpg" class="detail-cover img-responsive" alt="Mưa Đỏ">
                                        </div>
                                        <div class="col-md-7">
                                            <h3>Red Rain</h3>
                                            <p class="detail-meta">Author: <strong>Chu Lai</strong></p>
                                            <p class="detail-meta">Publisher: <strong>People's Army</strong></p>

                                            <div class="detail-price">
                                                <span class="new-price">184.500 đ</span>
                                                <span class="discount-label">-10%</span>
                                            </div>
                                            <div class="old-price-wrapper">
                                                <span class="old-price">205.000 đ</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p class="detail-description">
                                                Intertwined emotional arrays between smiles - tears, pain - joy, life - death, sublimation - loss, the sacrifice of fathers, husbands, sons, soldiers, comrades.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bestseller-pane" id="kt-book-2">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <img src="img/new/2.jpg" class="detail-cover img-responsive" alt="Hồ Điệp">
                                        </div>
                                        <div class="col-md-7">
                                            <h3>Butterfly and Whale</h3>
                                            <p class="detail-meta">Author: <strong>Tuệ Kiên</strong></p>
                                            <p class="detail-meta">Publisher: <strong>...</strong></p>

                                            <div class="detail-price">
                                                <span class="new-price">99.000 đ</span>
                                                <span class="discount-label">-15%</span>
                                            </div>
                                            <div class="old-price-wrapper">
                                                <span class="old-price">116.000 đ</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p class="detail-description">
                                                A captivating story from the very first pages - When love becomes a fragile thread between life and death, betrayal and hope. When a small butterfly encounters a mighty whale, is it destiny or just a fleeting dream?
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bestseller-pane" id="kt-book-3">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <img src="img/new/3.png" class="detail-cover img-responsive"
                                                alt="Nhà Giả Kim">
                                        </div>
                                        <div class="col-md-7">
                                            <h3>The Alchemist (2020 Reprint)</h3>
                                            <p class="detail-meta">Author: <strong>Paulo Coelho</strong></p>
                                            <p class="detail-meta">Publisher: <strong>...</strong></p>

                                            <div class="detail-price">
                                                <span class="new-price">79.000 đ</span>
                                                <span class="discount-label">-20%</span>
                                            </div>
                                            <div class="old-price-wrapper">
                                                <span class="old-price">99.000 đ</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p class="detail-description">
                                                Description for The Alchemist...
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="tab-thieu-nhi">
                    <div class="row">
                        <div class="col-md-5">
                            <ul class="bestseller-list">
                                <li class="bestseller-item active" data-target="tn-book-1">
                                    <div class="rank-number">01</div>
                                    <div class="rank-arrow">
                                        <span class="glyphicon glyphicon-arrow-up" style="color: green;"></span>
                                    </div>
                                    <div class="media">
                                        <div class="media-left">
                                            <img class="media-object" src="img/new/1.jpg" alt="Mưa Đỏ">
                                        </div>
                                        <div class="media-body">
                                            <h4 class="media-heading">Red Rain</h4>
                                            <p>Chu Lai</p>
                                            <p><strong>5841 points</strong></p>
                                        </div>
                                    </div>
                                </li>

                                <li class="bestseller-item" data-target="tn-book-2">
                                    <div class="rank-number">02</div>
                                    <div class="rank-arrow">
                                        <span class="glyphicon glyphicon-arrow-up" style="color: green;"></span>
                                    </div>
                                    <div class="media">
                                        <div class="media-left">
                                            <img class="media-object" src="img/new/2.jpg" alt="Hồ Điệp">
                                        </div>
                                        <div class="media-body">
                                            <h4 class="media-heading">Butterfly and Whale</h4>
                                            <p>Tuệ Kiên</p>
                                            <p><strong>1082 points</strong></p>
                                        </div>
                                    </div>
                                </li>

                                <li class="bestseller-item" data-target="tn-book-3">
                                    <div class="rank-number">03</div>
                                    <div class="rank-arrow">
                                        <span class="glyphicon glyphicon-arrow-up" style="color: green;"></span>
                                    </div>
                                    <div class="media">
                                        <div class="media-left">
                                            <img class="media-object" src="img/new/3.png" alt="Nhà Giả Kim">
                                        </div>
                                        <div class="media-body">
                                            <h4 class="media-heading">The Alchemist (2020 Reprint)</h4>
                                            <p>Paulo Coelho</p>
                                            <p><strong>867 points</strong></p>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <div class="col-md-7">
                            <div class="bestseller-detail-content">
                                <div class="bestseller-pane active" id="tn-book-1">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <img src="img/new/1.jpg" class="detail-cover img-responsive" alt="Mưa Đỏ">
                                        </div>
                                        <div class="col-md-7">
                                            <h3>Red Rain</h3>
                                            <p class="detail-meta">Author: <strong>Chu Lai</strong></p>
                                            <p class="detail-meta">Publisher: <strong>People's Army</strong></p>

                                            <div class="detail-price">
                                                <span class="new-price">184.500 đ</span>
                                                <span class="discount-label">-10%</span>
                                            </div>
                                            <div class="old-price-wrapper">
                                                <span class="old-price">205.000 đ</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p class="detail-description">
                                                Intertwined emotional arrays between smiles - tears, pain - joy, life - death, sublimation - loss, the sacrifice of fathers, husbands, sons, soldiers, comrades.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bestseller-pane" id="tn-book-2">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <img src="img/new/2.jpg" class="detail-cover img-responsive" alt="Hồ Điệp">
                                        </div>
                                        <div class="col-md-7">
                                            <h3>Butterfly and Whale</h3>
                                            <p class="detail-meta">Author: <strong>Tuệ Kiên</strong></p>
                                            <p class="detail-meta">Publisher: <strong>...</strong></p>

                                            <div class="detail-price">
                                                <span class="new-price">99.000 đ</span>
                                                <span class="discount-label">-15%</span>
                                            </div>
                                            <div class="old-price-wrapper">
                                                <span class="old-price">116.000 đ</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p class="detail-description">
                                                A captivating story from the very first pages - When love becomes a fragile thread between life and death, betrayal and hope. When a small butterfly encounters a mighty whale, is it destiny or just a fleeting dream?
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bestseller-pane" id="tn-book-3">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <img src="img/new/3.png" class="detail-cover img-responsive"
                                                alt="Nhà Giả Kim">
                                        </div>
                                        <div class="col-md-7">
                                            <h3>The Alchemist (2020 Reprint)</h3>
                                            <p class="detail-meta">Author: <strong>Paulo Coelho</strong></p>
                                            <p class="detail-meta">Publisher: <strong>...</strong></p>

                                            <div class="detail-price">
                                                <span class="new-price">79.000 đ</span>
                                                <span class="discount-label">-20%</span>
                                            </div>
                                            <div class="old-price-wrapper">
                                                <span class="old-price">99.000 đ</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p class="detail-description">
                                                Description for The Alchemist...
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>


    <div class="container-fluid" id="author">
        <h3 style="color:#D67B22;"> POPULAR AUTHORS </h3>
        <div class="row">
            <div class="col-sm-5 col-md-3 col-lg-3">
                <a href="Author.php?value=Durjoy%20Datta"><img class="img-responsive center-block"
                        src="img/popular-author/0.jpg"></a>
            </div>
            <div class="col-sm-6 col-md-3 col-lg-3">
                <a href="Author.php?value=Chetan%20Bhagat"><img class="img-responsive center-block"
                        src="img/popular-author/1.jpg"></a>
            </div>
            <div class="col-sm-6 col-md-3 col-lg-3">
                <a href="Author.php?value=Dan%20Brown"><img class="img-responsive center-block"
                        src="img/popular-author/2.jpg"></a>
            </div>
            <div class="col-sm-6 col-md-3 col-lg-3">
                <a href="Author.php?value=Ravinder%20Singh"><img class="img-responsive center-block"
                        src="img/popular-author/3.jpg"></a>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-5 col-md-3 col-lg-3">
                <a href="Author.php?value=Jeffrey%20Archer"><img class="img-responsive center-block"
                        src="img/popular-author/4.jpg"></a>
            </div>
            <div class="col-sm-6 col-md-3 col-lg-3">
                <a href="Author.php?value=Salman%20Rushdie"><img class="img-responsive center-block"
                        src="img/popular-author/5.jpg"><a>
            </div>
            <div class="col-sm-6 col-md-3 col-lg-3">
                <a href="Author.php?value=J%20K%20Rowling"><img class="img-responsive center-block"
                        src="img/popular-author/6.jpg"></a>
            </div>
            <div class="col-sm-6 col-md-3 col-lg-3">
                <a href="Author.php?value=Subrata%20Roy"><img class="img-responsive center-block"
                        src="img/popular-author/7.jpg"></a>
            </div>
        </div>
    </div>

    <footer>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-1 col-md-1 col-lg-1">
                </div>
                <div class="col-sm-7 col-md-5 col-lg-5">
                    <div class="row text-center">
                        <h2>Let's Get In Touch!</h2>
                        <hr class="primary">
                        <p>Still Confused? Give us a call or send us an email and we will get back to you as
                            soon as
                            possible!</p>
                    </div>
                    <div class="row">
                        <div class="col-md-6 text-center">
                            <span class="glyphicon glyphicon-earphone"></span>
                            <p>123-456-6789</p>
                        </div>
                        <div class="col-md-6 text-center">
                            <span class="glyphicon glyphicon-envelope"></span>
                            <p>BookStore@gmail.com</p>
                        </div>
                    </div>
                </div>
                <div class="hidden-sm-down col-md-2 col-lg-2">
                </div>
                <div class="col-sm-4 col-md-3 col-lg-3 text-center">
                    <h2 style="color:#D67B22;">Follow Us At</h2>
                    <div>
                        <a href="https://twitter.com/strandbookstore">
                            <img title="Twitter" alt="Twitter" src="img/social/twitter.png" width="35" height="35" />
                        </a>
                        <a href="https://www.linkedin.com/company/strand-book-store">
                            <img title="LinkedIn" alt="LinkedIn" src="img/social/linkedin.png" width="35" height="35" />
                        </a>
                        <a href="https://www.facebook.com/strandbookstore/">
                            <img title="Facebook" alt="Facebook" src="img/social/facebook.png" width="35" height="35" />
                        </a>
                        <a href="https://plus.google.com/111917722383378485041">
                            <img title="google+" alt="google+" src="img/social/google.jpg" width="35" height="35" />
                        </a>
                        <a href="https://www.pinterest.com/strandbookstore/">
                            <img title="Pinterest" alt="Pinterest" src="img/social/pinterest.jpg" width="35"
                                height="35" />
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <div class="container">
        <!-- Trigger the modal with a button -->
        <button type="button" id="query_button" class="btn btn-lg" data-toggle="modal" data-target="#query">Ask
            query</button>
        <!-- Modal -->
        <div class="modal fade" id="query" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header text-center">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Ask your query here</h4>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="query.php" class="form" role="form">
                            <div class="form-group">
                                <label class="sr-only" for="name">Name</label>
                                <input type="text" class="form-control" placeholder="Your Name" name="sender" required>
                            </div>
                            <div class="form-group">
                                <label class="sr-only" for="email">Email</label>
                                <input type="email" class="form-control" placeholder="abc@gmail.com" name="senderEmail"
                                    required>
                            </div>
                            <div class="form-group">
                                <label class="sr-only" for="query">Message</label>
                                <textarea class="form-control" rows="5" cols="30" name="message"
                                    placeholder="Your Query" required></textarea>
                            </div>
                            <div class="form-group">
                                <button type="submit" name="submit" value="query" class="btn btn-block">
                                    Send Query
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
</body>

</html>
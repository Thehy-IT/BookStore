<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Premium Books Store">
    <meta name="author" content="Shivangi Gupta">
    <title>BookZ | Premium Online Bookstore</title>

    <!-- Fonts: Playfair Display (Sang trọng) & Plus Jakarta Sans (Hiện đại) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <!-- SweetAlert2 (Thông báo đẹp) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary-color: #0f172a; /* Dark Navy */
            --accent-color: #c5a47e; /* Gold Luxury */
            --text-color: #334155;
            --bg-light: #f8fafc;
            --white: #ffffff;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-color);
            background-color: var(--bg-light);
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, h6, .navbar-brand {
            font-family: 'Playfair Display', serif;
            color: var(--primary-color);
        }

        /* --- Navbar --- */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.03);
            padding: 15px 0;
        }
        
        .navbar-brand img {
            height: 45px;
        }

        .nav-link {
            font-weight: 600;
            color: var(--primary-color) !important;
            font-size: 0.95rem;
            transition: color 0.3s;
        }

        .nav-link:hover {
            color: var(--accent-color) !important;
        }

        .btn-search {
            background-color: var(--primary-color);
            color: white;
            border: none;
        }
        
        .btn-search:hover {
            background-color: var(--accent-color);
            color: white;
        }

        /* --- Hero Slider --- */
        .hero-section {
            margin-top: 80px; /* Offset fixed navbar */
            padding: 20px 0;
        }

        .hero-slider .swiper-slide img {
            width: 100%;
            height: auto;
            border-radius: 12px;
            object-fit: cover;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        /* --- Product Card --- */
        .book-card {
            background: var(--white);
            border: none;
            border-radius: 12px;
            padding: 15px;
            transition: all 0.3s ease;
            height: 100%;
            position: relative;
            box-shadow: 0 5px 15px rgba(0,0,0,0.03);
        }

        .book-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
        }

        .book-card .tag {
            position: absolute;
            top: 10px;
            left: 10px;
            background: var(--accent-color);
            color: white;
            padding: 4px 12px;
            font-size: 0.75rem;
            font-weight: 700;
            border-radius: 20px;
            z-index: 2;
        }

        .book-card img {
            border-radius: 8px;
            margin-bottom: 15px;
            width: 100%;
            height: 280px; /* Cố định chiều cao ảnh */
            object-fit: contain;
        }

        .book-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 5px;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 2.8em;
        }

        .book-price {
            color: var(--accent-color);
            font-weight: 700;
            font-size: 1.1rem;
        }

        .old-price {
            text-decoration: line-through;
            color: #94a3b8;
            font-size: 0.9rem;
            margin-left: 8px;
        }

        /* --- Section Titles --- */
        .section-title {
            text-align: center;
            margin: 60px 0 40px;
            position: relative;
        }

        .section-title h3 {
            font-size: 2.5rem;
            font-weight: 700;
        }
        
        .section-title::after {
            content: '';
            display: block;
            width: 60px;
            height: 3px;
            background: var(--accent-color);
            margin: 15px auto 0;
        }

        /* --- Footer --- */
        footer {
            background-color: var(--primary-color);
            color: #e2e8f0;
            padding: 60px 0 30px;
            margin-top: 80px;
        }

        footer h4 {
            color: var(--white);
            margin-bottom: 25px;
        }

        footer a {
            color: #94a3b8;
            text-decoration: none;
            transition: 0.3s;
        }

        footer a:hover {
            color: var(--accent-color);
        }

        /* --- Modal Custom --- */
        .modal-content {
            border-radius: 16px;
            border: none;
        }
        .modal-header {
            background: var(--primary-color);
            color: white;
            border-radius: 16px 16px 0 0;
        }
        .btn-primary-custom {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            width: 100%;
            font-weight: 600;
        }
        .btn-primary-custom:hover {
            background: var(--accent-color);
        }

        /* Floating Button */
        #query_button {
            position: fixed;
            right: 20px;
            bottom: 20px;
            background: var(--accent-color);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 15px 30px;
            box-shadow: 0 10px 20px rgba(197, 164, 126, 0.4);
            font-weight: 700;
            z-index: 1000;
            transition: 0.3s;
        }
        #query_button:hover {
            transform: scale(1.05);
            background: var(--primary-color);
        }
    </style>
</head>

<body>
    <?php
    session_start();
    include "dbconnect.php";

    // Biến để chứa script thông báo SweetAlert
    $swal_script = "";

    // Hàm xử lý thông báo
    function set_swal($icon, $title, $text = "") {
        return "
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: '$icon',
                    title: '$title',
                    text: '$text',
                    confirmButtonColor: '#0f172a'
                });
            });
        </script>";
    }

    if (isset($_GET['Message'])) {
        $swal_script = set_swal('info', $_GET['Message']);
    }

    if (isset($_GET['response'])) {
        $swal_script = set_swal('info', $_GET['response']);
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
                    $swal_script = set_swal('success', 'Welcome back!', 'Successfully logged in.');
                } else {
                    $swal_script = set_swal('error', 'Login Failed', 'Incorrect Username Or Password.');
                }
            } else {
                $swal_script = set_swal('error', 'Login Failed', 'Incorrect Username Or Password.');
            }
            $stmt->close();
        } else if ($_POST['submit'] == "register") {
            $username = $_POST['register_username'];
            $password = $_POST['register_password'];
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $con->prepare("INSERT INTO users (UserName, Password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hashed_password);
            
            if ($stmt->execute()) {
                $swal_script = set_swal('success', 'Registered!', 'You can now login.');
            } else {
                $swal_script = set_swal('warning', 'Registration Failed', 'Username is already taken.');
            }
        }
    }
    
    // In ra script thông báo nếu có
    echo $swal_script;
    ?>

    <!-- ============== Navbar Modern ==============-->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <!-- Thay logo bằng text sang trọng nếu chưa có ảnh đẹp -->
                <span style="font-weight: 900; font-size: 1.5rem;">BOOK<span style="color: var(--accent-color)">Z</span>.</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarScroll">
                <form class="d-flex me-auto ms-lg-4" role="search" method="POST" action="Result.php" style="width: 100%; max-width: 400px;">
                    <div class="input-group">
                        <input type="text" class="form-control border-0 bg-light" name="keyword" placeholder="Search books, authors...">
                        <button class="btn btn-search" type="submit"><i class="fas fa-search"></i></button>
                    </div>
                </form>

                <ul class="navbar-nav ms-auto my-2 my-lg-0 navbar-nav-scroll align-items-center">
                    <li class="nav-item"><a class="nav-link" href="#new">New Arrivals</a></li>
                    <li class="nav-item"><a class="nav-link" href="#bestseller-section">Bestsellers</a></li>
                    
                    <?php if (!isset($_SESSION['user'])): ?>
                        <li class="nav-item dropdown ms-lg-3">
                            <a class="nav-link dropdown-toggle btn btn-outline-dark px-3 rounded-pill" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i> Account
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#loginModal">Login</button></li>
                                <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#registerModal">Sign Up</button></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item ms-lg-3">
                            <a class="nav-link position-relative" href="cart.php">
                                <i class="fas fa-shopping-bag fa-lg"></i>
                                <span class="position-absolute top-10 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                            </a>
                        </li>
                        <li class="nav-item dropdown ms-3">
                            <a class="nav-link dropdown-toggle fw-bold" href="#" role="button" data-bs-toggle="dropdown">
                                Hello, <?php echo htmlspecialchars($_SESSION['user']); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                <li><a class="dropdown-item" href="destroy.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- ============== Login Modal ==============-->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Welcome Back</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form method="post" action="index.php">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="loginUser" name="login_username" placeholder="Username" required>
                            <label for="loginUser">Username</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="loginPass" name="login_password" placeholder="Password" required>
                            <label for="loginPass">Password</label>
                        </div>
                        <button type="submit" name="submit" value="login" class="btn btn-primary-custom btn-lg">Sign In</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ============== Register Modal ==============-->
    <div class="modal fade" id="registerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Account</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form method="post" action="index.php">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="regUser" name="register_username" placeholder="Username" required>
                            <label for="regUser">Username</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="regPass" name="register_password" placeholder="Password" required>
                            <label for="regPass">Password</label>
                        </div>
                        <button type="submit" name="submit" value="register" class="btn btn-primary-custom btn-lg">Sign Up</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ============== Hero & Categories ==============-->
    <div class="container-fluid hero-section">
        <div class="container">
            <div class="row g-4">
                <!-- Categories List -->
                <div class="col-lg-3 d-none d-lg-block">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header text-white fw-bold py-3" style="background: var(--primary-color);">
                            <i class="fas fa-bars me-2"></i> Browse Categories
                        </div>
                        <div class="list-group list-group-flush">
                            <a href="Product.php?value=entrance%20exam" class="list-group-item list-group-item-action py-3">Entrance Exam</a>
                            <a href="Product.php?value=Literature%20and%20Fiction" class="list-group-item list-group-item-action py-3">Literature & Fiction</a>
                            <a href="Product.php?value=Academic%20and%20Professional" class="list-group-item list-group-item-action py-3">Academic & Professional</a>
                            <a href="Product.php?value=Biographies%20and%20Auto%20Biographies" class="list-group-item list-group-item-action py-3">Biographies</a>
                            <a href="Product.php?value=Children%20and%20Teens" class="list-group-item list-group-item-action py-3">Children & Teens</a>
                            <a href="Product.php?value=Business%20and%20Management" class="list-group-item list-group-item-action py-3">Business</a>
                        </div>
                    </div>
                </div>

                <!-- Main Slider -->
                <div class="col-lg-9">
                    <div class="swiper hero-slider h-100">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide"><img src="img/carousel/1.jpg" alt="Slide 1"></div>
                            <div class="swiper-slide"><img src="img/carousel/2.jpg" alt="Slide 2"></div>
                            <div class="swiper-slide"><img src="img/carousel/3.jpg" alt="Slide 3"></div>
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============== New Arrivals Section ==============-->
    <div class="container py-5" id="new">
        <div class="section-title">
            <h3>New Arrivals</h3>
            <p class="text-muted">Discover the latest additions to our collection</p>
        </div>

        <div class="row g-4">
            <!-- Book 1 -->
            <div class="col-6 col-md-3">
                <a href="description.php?ID=NEW-1&category=new" class="text-decoration-none text-dark">
                    <div class="book-card">
                        <div class="tag">NEW</div>
                        <img src="img/new/1.jpg" alt="Book 1" class="img-fluid">
                        <div class="book-title">Like A Love Song</div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="book-price">
                                113 đ <span class="old-price">175 đ</span>
                            </div>
                            <span class="badge bg-danger">-35%</span>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Book 2 -->
            <div class="col-6 col-md-3">
                <a href="description.php?ID=NEW-2&category=new" class="text-decoration-none text-dark">
                    <div class="book-card">
                        <div class="tag">HOT</div>
                        <img src="img/new/2.jpg" alt="Book 2" class="img-fluid">
                        <div class="book-title">General Knowledge 2017</div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="book-price">
                                68 đ <span class="old-price">120 đ</span>
                            </div>
                            <span class="badge bg-danger">-43%</span>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Book 3 -->
            <div class="col-6 col-md-3">
                <a href="description.php?ID=NEW-3&category=new" class="text-decoration-none text-dark">
                    <div class="book-card">
                        <div class="tag">SALE</div>
                        <img src="img/new/3.png" alt="Book 3" class="img-fluid">
                        <div class="book-title">Indian Family Business Mantras</div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="book-price">
                                400 đ <span class="old-price">595 đ</span>
                            </div>
                            <span class="badge bg-danger">-33%</span>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Book 4 -->
            <div class="col-6 col-md-3">
                <a href="description.php?ID=NEW-4&category=new" class="text-decoration-none text-dark">
                    <div class="book-card">
                        <div class="tag">BEST</div>
                        <img src="img/new/4.jpg" alt="Book 4" class="img-fluid">
                        <div class="book-title">SSC Mathematics Chapterwise</div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="book-price">
                                289 đ <span class="old-price">435 đ</span>
                            </div>
                            <span class="badge bg-danger">-33%</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- ============== Popular Authors ==============-->
    <div class="container py-5 bg-white rounded-4 shadow-sm mt-4">
        <div class="section-title mt-2">
            <h3>Popular Authors</h3>
        </div>
        
        <div class="swiper author-slider pb-5">
            <div class="swiper-wrapper align-items-center text-center">
                <!-- Duplicate these slides dynamically if needed -->
                <div class="swiper-slide">
                    <a href="Author.php?value=Durjoy%20Datta"><img src="img/popular-author/0.jpg" class="rounded-circle shadow-sm" style="width:150px; height:150px; object-fit:cover;"></a>
                    <h5 class="mt-3">Durjoy Datta</h5>
                </div>
                <div class="swiper-slide">
                    <a href="Author.php?value=Chetan%20Bhagat"><img src="img/popular-author/1.jpg" class="rounded-circle shadow-sm" style="width:150px; height:150px; object-fit:cover;"></a>
                    <h5 class="mt-3">Chetan Bhagat</h5>
                </div>
                <div class="swiper-slide">
                    <a href="Author.php?value=Dan%20Brown"><img src="img/popular-author/2.jpg" class="rounded-circle shadow-sm" style="width:150px; height:150px; object-fit:cover;"></a>
                    <h5 class="mt-3">Dan Brown</h5>
                </div>
                <div class="swiper-slide">
                    <a href="Author.php?value=J%20K%20Rowling"><img src="img/popular-author/6.jpg" class="rounded-circle shadow-sm" style="width:150px; height:150px; object-fit:cover;"></a>
                    <h5 class="mt-3">J.K. Rowling</h5>
                </div>
                <div class="swiper-slide">
                    <a href="Author.php?value=Jeffrey%20Archer"><img src="img/popular-author/4.jpg" class="rounded-circle shadow-sm" style="width:150px; height:150px; object-fit:cover;"></a>
                    <h5 class="mt-3">Jeffrey Archer</h5>
                </div>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>

    <!-- ============== Footer ==============-->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h4>About BookZ</h4>
                    <p class="text-white-50">We are a premium bookstore dedicated to providing the best quality literature for book lovers around the world. Join our community today.</p>
                    <div class="mt-4">
                        <a href="#" class="me-3"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#" class="me-3"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="#" class="me-3"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" class="me-3"><i class="fab fa-linkedin fa-lg"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-4 mb-4">
                    <h4>Contact Us</h4>
                    <ul class="list-unstyled text-white-50">
                        <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i> 123 Book Street, Literature City</li>
                        <li class="mb-2"><i class="fas fa-phone me-2"></i> +84 123 456 789</li>
                        <li class="mb-2"><i class="fas fa-envelope me-2"></i> support@bookz.com</li>
                    </ul>
                </div>

                <div class="col-lg-4">
                    <h4>Newsletter</h4>
                    <p class="text-white-50">Subscribe to get updates on new arrivals and special offers.</p>
                    <form class="input-group">
                        <input type="email" class="form-control border-0" placeholder="Your Email">
                        <button class="btn btn-primary-custom rounded-end" style="border-radius: 0;" type="button">Subscribe</button>
                    </form>
                </div>
            </div>
            <div class="text-center border-top border-secondary pt-4 mt-5">
                <p class="text-white-50 mb-0">&copy; 2023 BookZ Store. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Floating Query Button -->
    <button type="button" id="query_button" data-bs-toggle="modal" data-bs-target="#queryModal">
        <i class="fas fa-comment-dots me-2"></i> Support
    </button>

    <!-- Query Modal -->
    <div class="modal fade" id="queryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">How can we help?</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form method="post" action="query.php">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="qName" name="sender" placeholder="Name" required>
                            <label for="qName">Your Name</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="qEmail" name="senderEmail" placeholder="Email" required>
                            <label for="qEmail">Email Address</label>
                        </div>
                        <div class="form-floating mb-3">
                            <textarea class="form-control" id="qMessage" name="message" placeholder="Message" style="height: 100px" required></textarea>
                            <label for="qMessage">Your Message</label>
                        </div>
                        <button type="submit" name="submit" value="query" class="btn btn-primary-custom">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JS Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <!-- Init Sliders -->
    <script>
        var heroSwiper = new Swiper(".hero-slider", {
            spaceBetween: 30,
            effect: "fade",
            loop: true,
            autoplay: {
                delay: 3500,
                disableOnInteraction: false,
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
        });

        var authorSwiper = new Swiper(".author-slider", {
            slidesPerView: 2,
            spaceBetween: 20,
            breakpoints: {
                640: { slidesPerView: 3 },
                1024: { slidesPerView: 4 },
            },
            loop: true,
            autoplay: {
                delay: 3000,
            },
        });
    </script>
</body>
</html>
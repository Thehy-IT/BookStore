<?php
include 'header.php'; // Sử dụng header chung

$pid = isset($_GET['ID']) ? $_GET['ID'] : '';

// Query DB (Prepared Statement)
$stmt = $con->prepare("SELECT * FROM products WHERE PID = ?");
$stmt->bind_param("s", $pid);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Nếu không tìm thấy sách
if (!$row) {
    echo "<script>alert('Book not found!'); window.location.href='index.php';</script>";
    exit();
}
?>
<style>
        /* Các style này đặc thù cho trang chi tiết sản phẩm */
        
        /* --- Main Product Card --- */
        .product-glass-card {
            padding-top: 40px; /* Thêm khoảng đệm thay cho margin-top cũ */
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: var(--glass-border);
            border-radius: 24px;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
        }

        .book-cover-wrapper {
            position: relative;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
            transition: 0.3s;
        }

        .book-cover-wrapper:hover {
            transform: scale(1.02);
        }

        .book-cover-wrapper img {
            width: 100%;
            height: auto;
            object-fit: cover;
        }

        .discount-badge {
            position: absolute;
            top: 20px;
            left: 20px;
            background: #ef4444;
            color: white;
            font-weight: 800;
            padding: 8px 16px;
            border-radius: 30px;
            z-index: 2;
            box-shadow: 0 5px 15px rgba(239, 68, 68, 0.4);
        }

        .book-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .meta-tags span {
            background: rgba(15, 23, 42, 0.05);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-right: 10px;
            color: #64748b;
        }

        .price-area {
            margin: 25px 0;
            padding: 20px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        .price-current {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary);
        }

        .price-old {
            text-decoration: line-through;
            color: #94a3b8;
            font-size: 1.2rem;
            margin-left: 10px;
        }

        .btn-add-cart {
            background: linear-gradient(45deg, var(--primary), #1e293b);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 12px;
            font-weight: 600;
            width: 100%;
            transition: 0.3s;
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.2);
        }

        .btn-add-cart:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(15, 23, 42, 0.3);
            background: linear-gradient(45deg, var(--accent), #b39065);
            color: white;
        }

        /* --- Detail Table --- */
        .details-table td {
            padding: 12px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .details-table tr:last-child td {
            border-bottom: none;
        }

        .details-label {
            font-weight: 600;
            color: #64748b;
            width: 150px;
        }

        .details-value {
            font-weight: 600;
            color: var(--primary);
        }

        /* --- Service Cards --- */
        .service-card {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(10px);
            padding: 25px;
            border-radius: 16px;
            text-align: center;
            height: 100%;
            border: 1px solid rgba(255, 255, 255, 0.5);
            transition: 0.3s;
        }

        .service-card:hover {
            background: white;
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
        }

        .service-icon {
            font-size: 2rem;
            color: var(--accent);
            margin-bottom: 15px;
        }
    </style>

    <!-- ============== Product Details ==============-->
    <div class="container pb-5">
        <div class="product-glass-card">
            <div class="row g-5">

                <!-- Left Column: Image -->
                <div class="col-lg-5">
                    <div class="book-cover-wrapper">
                        <?php if ($row['Discount'] > 0): ?>
                            <div class="discount-badge">-<?php echo $row['Discount']; ?>%</div>
                        <?php endif; ?>
                        <img src="img/books/<?php echo $row['PID']; ?>.jpg" alt="<?php echo $row['Title']; ?>" onerror="this.src='https://placehold.co/600x900?text=Book+Cover'">
                    </div>
                </div>

                <!-- Right Column: Info -->
                <div class="col-lg-7">
                    <h1 class="book-title"><?php echo $row['Title']; ?></h1>

                    <div class="meta-tags mb-4">
                        <span><i class="fas fa-pen-nib me-1"></i> <?php echo $row['Author']; ?></span>
                        <span><i class="fas fa-building me-1"></i> <?php echo $row['Publisher']; ?></span>
                    </div>

                    <div class="price-area d-flex align-items-center justify-content-between">
                        <div>
                            <span class="price-current"><?php echo number_format($row['Price']); ?> đ</span>
                            <?php if ($row['MRP'] > $row['Price']): ?>
                                <span class="price-old"><?php echo number_format($row['MRP']); ?> đ</span>
                            <?php endif; ?>
                        </div>
                        <div class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i> In Stock</div>
                    </div>

                    <p class="text-muted mb-4" style="line-height: 1.8;">
                        <?php echo $row['Description']; ?>
                    </p>

                    <!-- Quantity & Add to Cart Form -->
                    <div class="row align-items-end mb-5">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <label class="form-label fw-bold small text-uppercase">Quantity</label>
                            <select class="form-select form-select-lg border-0 bg-light rounded-3" id="qtySelect">
                                <?php
                                $max_qty = ($row['Available'] > 10) ? 10 : $row['Available'];
                                for ($i = 1; $i <= $max_qty; $i++) {
                                    echo "<option value='$i'>$i</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <button onclick="addToCart('<?php echo $row['PID']; ?>')" class="btn-add-cart">
                                <i class="fas fa-shopping-bag me-2"></i> Add to Cart
                            </button>
                        </div>
                    </div>

                    <!-- Technical Specs -->
                    <h5 class="fw-bold mb-3 border-bottom pb-2">Product Details</h5>
                    <table class="details-table w-100">
                        <tr>
                            <td class="details-label">Product Code</td>
                            <td class="details-value"><?php echo $row['PID']; ?></td>
                        </tr>
                        <tr>
                            <td class="details-label">Edition</td>
                            <td class="details-value"><?php echo $row['Edition']; ?></td>
                        </tr>
                        <tr>
                            <td class="details-label">Language</td>
                            <td class="details-value"><?php echo $row['Language']; ?></td>
                        </tr>
                        <tr>
                            <td class="details-label">Pages</td>
                            <td class="details-value"><?php echo $row['page']; ?></td>
                        </tr>
                        <tr>
                            <td class="details-label">Weight</td>
                            <td class="details-value"><?php echo $row['weight']; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ============== Features Section (Services) ==============-->
    <div class="container mb-5">
        <div class="row g-4">
            <div class="col-md-3 col-6">
                <div class="service-card">
                    <i class="fas fa-headset service-icon"></i>
                    <h6 class="fw-bold">24/7 Support</h6>
                    <p class="small text-muted mb-0">Call us anytime</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="service-card">
                    <i class="fas fa-shield-alt service-icon"></i>
                    <h6 class="fw-bold">Secure Payment</h6>
                    <p class="small text-muted mb-0">100% Secure</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="service-card">
                    <i class="fas fa-undo service-icon"></i>
                    <h6 class="fw-bold">Easy Returns</h6>
                    <p class="small text-muted mb-0">No questions asked</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="service-card">
                    <i class="fas fa-truck service-icon"></i>
                    <h6 class="fw-bold">Fast Delivery</h6>
                    <p class="small text-muted mb-0">Nationwide shipping</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Logic Add to Cart (đã có trong footer.php) -->
    <script>
        function addToCart(pid) {
            var qty = document.getElementById('qtySelect').value;
            // Chuyển hướng đến trang xử lý giỏ hàng
            window.location.href = "cart.php?ID=" + pid + "&quantity=" + qty;
        }
    </script>
<?php
include 'footer.php'; // Sử dụng footer chung
?>
<?php
session_start();
include "dbconnect.php";
// CHẶN KHÔNG CHO USER THƯỜNG VÀO
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Lấy tất cả người dùng
$users_res = mysqli_query($con, "SELECT UserID, UserName, Role FROM users ORDER BY UserID DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý Người dùng | BookZ</title>
    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Dùng lại CSS từ admin.php -->
    <link rel="stylesheet" href="admin_style.css">
    <style>
        :root {
            --primary: #0f172a;
            --accent: #d4af37;
            --glass-bg: rgba(255, 255, 255, 0.8);
        }

        body {
            background: #f0f4f8;
            font-family: sans-serif;
        }

        .bg-blobs {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: radial-gradient(circle at 90% 10%, rgba(212, 175, 55, 0.15), transparent 40%),
                radial-gradient(circle at 10% 90%, rgba(15, 23, 42, 0.15), transparent 40%);
        }

        .sidebar {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            height: 100vh;
            position: fixed;
            width: 250px;
            padding-top: 20px;
            border-right: 1px solid rgba(255, 255, 255, 0.5);
        }

        .main-content {
            margin-left: 250px;
            padding: 40px;
        }

        .nav-link {
            color: var(--primary);
            padding: 15px 20px;
            font-weight: 600;
        }

        .nav-link:hover {
            background: rgba(212, 175, 55, 0.1);
            color: var(--accent);
        }

        .nav-link.active {
            background: var(--primary);
            color: white;
        }

        .text-accent {
            color: var(--accent);
        }
    </style>
</head>

<body>
    <div class="bg-blobs"></div>

    <!-- Sidebar -->
    <div class="sidebar">
        <h3 class="text-center fw-bold mb-4">BOOK<span class="text-accent">Z</span> ADMIN</h3>
        <ul class="nav flex-column">
            <li class="nav-item"><a href="admin.php" class="nav-link"><i class="fas fa-tachometer-alt me-2"></i> Bảng điều khiển</a></li>
            <li class="nav-item"><a href="manage_products.php" class="nav-link"><i class="fas fa-book me-2"></i> Quản lý sản phẩm</a></li>
            <li class="nav-item"><a href="manage_news.php" class="nav-link"><i class="fas fa-newspaper me-2"></i> Quản lý Tin tức</a></li>
            <li class="nav-item"><a href="manage_users.php" class="nav-link active"><i class="fas fa-users me-2"></i> Quản lý người dùng</a></li>
            <li class="nav-item"><a href="index.php" class="nav-link"><i class="fas fa-home me-2"></i> Xem website</a></li>
            <li class="nav-item"><a href="destroy.php" class="nav-link text-danger"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">Quản lý người dùng</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal" onclick="prepareAddModal()">
                <i class="fas fa-plus me-2"></i> Thêm người dùng mới
            </button>
        </div>

        <!-- Hiển thị thông báo (nếu có) -->
        <?php if (isset($_SESSION['message'])) : ?>
            <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
        endif;
        ?>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>UserID</th>
                        <th>Tên đăng nhập</th>
                        <th>Quyền</th>
                        <th class="text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($users_res)) : ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['UserID']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['UserName']); ?></td>
                            <td><span class="badge bg-<?php echo $row['Role'] == 'admin' ? 'danger' : 'secondary'; ?>"><?php echo htmlspecialchars($row['Role']); ?></span></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary" onclick='prepareEditModal(<?php echo json_encode($row); ?>)' data-bs-toggle="modal" data-bs-target="#userModal">
                                    <i class="fas fa-edit me-1"></i> Sửa
                                </button>
                                <?php if ($row['UserID'] == $_SESSION['user_id']) : ?>
                                    <button class="btn btn-sm btn-outline-danger" disabled title="Bạn không thể tự xóa tài khoản của mình">
                                        <i class="fas fa-trash me-1"></i> Xóa
                                    </button>
                                <?php else : ?>
                                    <a href="user_action.php?action=delete&id=<?php echo $row['UserID']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng này?')">
                                        <i class="fas fa-trash me-1"></i> Xóa
                                    </a>
                                <?php endif; ?>

                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- User Modal (Add/Edit) -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="user_action.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="userModalLabel">Thêm người dùng mới</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" id="formAction" value="add">
                        <input type="hidden" name="userid" id="formUserid">

                        <div class="mb-3">
                            <label for="username" class="form-label">Tên đăng nhập</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control" id="password" name="password">
                            <small id="passwordHelp" class="form-text text-muted">Để trống nếu không muốn thay đổi mật khẩu.</small>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Quyền</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="user">user</option>
                                <option value="admin">admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const modalTitle = document.getElementById('userModalLabel');
        const formAction = document.getElementById('formAction');
        const formUserid = document.getElementById('formUserid');
        const formUsername = document.getElementById('username');
        const formPassword = document.getElementById('password');
        const formRole = document.getElementById('role');
        const passwordHelp = document.getElementById('passwordHelp');

        function prepareAddModal() {
            document.querySelector('#userModal form').reset();
            modalTitle.textContent = 'Thêm người dùng mới';
            formAction.value = 'add';
            formUserid.value = '';
            formPassword.required = true;
            passwordHelp.style.display = 'none';
        }

        function prepareEditModal(user) {
            document.querySelector('#userModal form').reset();
            modalTitle.textContent = 'Chỉnh sửa người dùng';
            formAction.value = 'edit';
            formUserid.value = user.UserID;
            formUsername.value = user.UserName;
            formRole.value = user.Role;
            formPassword.required = false;
            passwordHelp.style.display = 'block';
        }
    </script>
</body>

</html>
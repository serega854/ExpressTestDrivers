<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Авторизация администратора
                    </div>
                    <div class="card-body">
                        <!-- Место для вывода сообщения об ошибке -->
                        <?php session_start(); ?>

                        <?php 
                            if (isset($_SESSION['admin_username'])) {
                                header("Location: ../admin/admin_index.php");
                                exit;
                            }
                        ?>

                        <?php if (isset($_SESSION['login_error'])) : ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $_SESSION['login_error']; ?>
                            </div>
                            <?php unset($_SESSION['login_error']); ?>
                        <?php endif; ?>
                        <form action="../../vendor/admin_login_process.php" method="POST">
                            <div class="form-group">
                                <label for="username">Логин</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Пароль</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Войти</button>
                        </form>
                        <div class="mt-3">
                            <a href="../driver/driver_login.php">Войти как водитель</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

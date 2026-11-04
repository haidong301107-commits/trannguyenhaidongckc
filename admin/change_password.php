<?php
session_start();
require_once("../connect_db.php");
if (!isset($_SESSION['admin'])) {
    header("Location: " . INDEX_URL . "admin/login.php");
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_SESSION['admin'])) {
        $ten_dang_nhap = $_SESSION['admin'];
    }
}
if (isset($_POST["btnSubmit"])) {
    if (isset($_REQUEST["ten_dang_nhap"])) {
        $ten_dang_nhap = $_REQUEST["ten_dang_nhap"];
    }
    if (isset($_REQUEST["passwordcu"])) {
        $passwordcu = $_REQUEST["passwordcu"];
    }
    if (isset($_REQUEST["passwordmoi"])) {
        $passwordmoi = $_REQUEST["passwordmoi"];
    }
    if (isset($_REQUEST["passwordmoixn"])) {
        $passwordmoixn = $_REQUEST["passwordmoixn"];
    }
    $conn = connect_db();
    if ($conn) {
        $sql = "SELECT * FROM quan_tri_vien WHERE ten_dang_nhap='" . $ten_dang_nhap . "'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            $sv = mysqli_fetch_assoc($result);
            $matkhau = $sv["mat_khau"];
            if ($matkhau != md5($passwordcu)) {
                $_SESSION['message_changepass'] = "Mật khẩu hiện tại không đúng!";
                header("Location: " . INDEX_URL . "admin/changepass.php");
                exit();
            }
        }
        if ($passwordmoi == "") {
            $_SESSION['message_changepass'] = "Vui lòng nhập mật khẩu mới!";
            header("Location: " . INDEX_URL . "admin/changepass.php");
            exit();
        }
        if ($passwordmoixn == "") {
            $_SESSION['message_changepass'] = "Vui lòng nhập xác nhận mật khẩu mới!";
            header("Location: " . INDEX_URL . "admin/changepass.php");
            exit();
        }
        if ($passwordmoi != $passwordmoixn) {
            $_SESSION['message_changepass'] = "Mật khẩu mới và xác nhận mật khẩu mới không khớp!";
            header("Location: " . INDEX_URL . "admin/changepass.php");
            exit();
        }
        $sql = "UPDATE quan_tri_vien SET mat_khau='" . md5($passwordmoi) . "'";
        if (mysqli_query($conn, $sql)) {
            $_SESSION['err_admin'] = "*Đổi mật khẩu thành công. Vui lòng đăng nhập lại!";
            unset($_SESSION['admin']);
            header("Location: " . INDEX_URL . "admin/login.php");
            exit();
        } else {
            $_SESSION['message_changepass'] = "*Lỗi cập nhật: " . mysqli_error($conn);
            header("Location: " . INDEX_URL . "admin/changepass.php");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đổi mật khẩu quản trị viên</title>
    <style>
        input[name="ten_dang_nhap"] {
            background-color: lemonchiffon;
            pointer-events: none;
        }

        body {
            font-family: "Open Sans", sans-serif;
        }

        form {
            max-width: 300px;
            border: 2px solid dodgerblue;
            padding: 10px;
            align-items: center;
            margin: auto;
        }

        input {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            border: 1px solid #ccc;
            border-radius: .1875rem;
            box-sizing: border-box;
            display: block;
            font-size: .875rem;
            margin-bottom: 1rem;
            padding: .275rem;
            width: 100%;
        }

        input[type="password"] {
            margin-bottom: .5rem;
        }

        input[type="submit"] {
            background-color: #015294;
            border: none;
            color: #fff;
            font-size: 1rem;
            padding: .5rem 1rem;
        }

        label {
            color: #666;
            font-size: .875rem;
        }

        .error {
            color: red;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <form action="changepass.php" method="post">
        <h2>Đổi mật khẩu quản trị viên</h2>
        <label>Tên đăng nhập</label>
        <input type="text" name="ten_dang_nhap" value="<?= $ten_dang_nhap ?>">
        <label>Mật khẩu hiện tại</label>
        <input type="password" name="passwordcu">
        <label>Mật khẩu mới</label>
        <input type="password" name="passwordmoi">
        <label>Xác nhận mật khẩu mới</label>
        <input type="password" name="passwordmoixn">
        <input type="submit" value="Đổi mật khẩu" name="btnSubmit">
    </form>
    <div class="block" style="text-align: center;">
        <span class="error" id="message_changepass">
            <?php
            if (isset($_SESSION['message_changepass'])) {
                echo $_SESSION['message_changepass'];
                unset($_SESSION['message_changepass']);
            }
            ?>
        </span>
    </div>
</body>

</html>
<?php
// delete.php
//删除文件必须输入密码防止误删
// 设置删除确认密码
define('DELETE_PASSWORD', '6'); // Replace 'your_password_here' with your actual password

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $filename = $_POST['filename'];
    $password = $_POST['password'];

    if ($password !== DELETE_PASSWORD) {
        echo '密码错误，无法删除文件。';
        exit;
    }

    $filePath = 'up/' . basename($filename);

    if (is_file($filePath)) {
        if (unlink($filePath)) {
            echo '文件删除成功。';
        } else {
            echo '删除文件时出错。';
        }
    } else {
        echo '文件不存在。';
    }
} else {
    echo '无效的请求方法。';
}
?>

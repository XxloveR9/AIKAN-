<?php
// index.php

// 指定图片存储的文件夹
$imageDir = 'up/';

// 获取文件夹中的所有文件，并过滤出图片和视频文件
$imageFiles = [];
$allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'ico'];
foreach (scandir($imageDir) as $file) {
    $filePath = $imageDir . $file;
    if (is_file($filePath) && in_array(strtolower(pathinfo($filePath, PATHINFO_EXTENSION)), $allowedTypes)) {
        $imageFiles[$file] = filemtime($filePath); // 使用文件名作为键，文件修改时间作为值
    }
}

// 按照文件修改时间进行排序，最新的在前
arsort($imageFiles);

// 按照文件修改时间进行排序，最旧的在前
//asort($imageFiles);

// 获取分页参数
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPageOptions = [10, 24, 30, 50, 100, 200]; // 添加24作为默认选项
$perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 24; // 默认每页24个

if (!in_array($perPage, $perPageOptions)) {
    $perPage = 24; // 如果不是有效选项，则使用默认值
}

$totalFiles = count($imageFiles);
$totalPages = ceil($totalFiles / $perPage);

// 计算当前页的文件偏移量
$offset = ($page - 1) * $perPage;

// 获取当前页的文件
$currentFiles = array_slice($imageFiles, $offset, $perPage, true);

// 格式化文件大小函数
function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= (1 << (10 * $pow));

    return round($bytes, $precision) . ($pow > 1 ? ' MB' : ' ' . $units[$pow]);
}

// 生成随机颜色函数
function randomColor() {
    return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AIKAN图床</title>
    <meta name="keywords" content="AIKAN, AIKAN图库, JIAYIN科技,无数据库图床,免费图床,PHP多图长传程序,自适应页面,一键复制链接" />
    <meta name="description" content="爱看图床AIKAN是一款支持多文件上传的无数据库图床,可以完美替代PHP多图上传程序,最新html5自适应页面兼容手机电脑,上传后返回图片直链,简单方便支持一键复制,支持多域名" />
    <link rel="icon" href="https://up.jyku.cn/up/24053110.png" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=0.72, maximum-scale=1, user-scalable=no">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        .gallery {
            display: flex;
            flex-wrap: wrap;
        }
        .gallery img, .gallery video {
            width: 150px; /* 图片和视频缩略图宽度 */
            height: 150px;
            margin: 12px;
            object-fit: cover; /* 保持图片和视频缩略图的比例，裁剪溢出部分 */
            transition: transform 0.2s ease-in-out;
            cursor: pointer;
        }
        .gallery img.enlarged, .gallery video.enlarged {
            transform: scale(4); /* 放大比例增加 */
        }
        .image-info {
            text-align: center;
            font-size: 12px;
            margin-top: -8px;
        }
        .info-text2 {
            text-align: center;
            margin-top: 20px;
            font-size: 30px;
            color: #FFC0CB;
        }
        .info-text1 {
            text-align: center;
            margin-top: 12px;
            font-size: 16px;
            margin-bottom: 8px;
            color: #999;
        }
        .info-text1 a {
            color: #999;
            text-decoration: none;
        }
        .random-color {
            color: <?= randomColor() ?>;
        }
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .overlay img, .overlay video {
            max-width: 90%;
            max-height: 90%;
            transition: transform 0.2s ease-in-out;
        }
        .pagination {
            text-align: center;
            margin: 10px 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .pagination a {
            margin: 0 5px;
            text-decoration: none;
            color: #333;
            padding: 5px 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }
        .pagination a.active {
            background-color: #007BFF;
            color: white;
        }
        .pagination select {
            width: 90px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }
        .info-text {
            text-align: center;
            margin-top: 10px;
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>
    <h1></h1>
   <div class="info-text2" onclick="refresh()">
    AIKAN图床<br>
</div>
    
    <div class="info-text1">
        <a href="./">点击上传</a>
    </div>
    <div class="gallery">
        <?php foreach ($currentFiles as $file => $modifiedTime): ?>
            <?php
            $filePath = $imageDir . $file;
            $fileInfo = pathinfo($filePath);
            $modifiedTime = date("Y-n-j G:i", $modifiedTime);
            $fileSize = filesize($filePath); // 获取文件大小
            $fileSizeFormatted = formatBytes($fileSize); // 格式化文件大小
            $fileExt = strtolower($fileInfo['extension']);
            ?>
            <div>
                <?php if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif', 'ico'])): ?>
                    <img src="<?= $filePath ?>" alt="<?= $file ?>" onclick="enlargeMedia(this)">
                <?php elseif ($fileExt == 'mp4'): ?>
                    <video src="<?= $filePath ?>" onclick="enlargeMedia(this)"></video>
                <?php endif; ?>
                <div class="image-info">
                    <span class="random-color"><b>编号：<?= $fileInfo['filename'] ?></b></span><br>
                    上传日期: <?= $modifiedTime ?><br>
                    大小: <?= $fileSizeFormatted ?>
                    
                    
                    <!--复制文件-->
                    <span style="cursor: pointer; color: #32CD32;" onclick="copyToClipboard('http://<?= $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) ?>/<?=$imageDir. $fileInfo['basename'] ?>')"><i class="fas fa-copy"></i></span>
                    
                    <!--下载文件-->
                    <a href="http://<?= $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) ?>/<?=$imageDir. $fileInfo['basename'] ?>" download style="color: #00BFFF; text-decoration: none;"><i class="fas fa-download"></i></a>
                    
                    
                    
                    <!--删除文件-->
                    <span style="cursor: pointer; color: #FF4500;" onclick="confirmDelete('<?= $fileInfo['basename'] ?>')"><i class="fas fa-trash-alt"></i>
                    </span>
                    

                    


                </div>
            </div>
        <?php endforeach; ?>
    </div>
<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?>&per_page=<?= $perPage ?>"><i class="fas fa-chevron-left"></i></a>
    <?php endif; ?>
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?= $i ?>&per_page=<?= $perPage ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
    <?php if ($page < $totalPages): ?>
        <a href="?page=<?= $page + 1 ?>&per_page=<?= $perPage ?>"><i class="fas fa-chevron-right"></i></a>
    <?php endif; ?>
    <form method="get" action="" style="display:inline-block; margin-left:10px;">
        <select id="per_page" name="per_page" onchange="this.form.submit()">
            <?php foreach ($perPageOptions as $option): ?>
                <option value="<?= $option ?>" <?= $perPage == $option ? 'selected' : '' ?>><?= $option ?><span class="per-page-label"> 张/页</span></option>
            <?php endforeach; ?>
        </select>
        <input type="hidden" name="page" value="1">
    </form>
</div>


    <div class="overlay" onclick="shrinkMedia()">
        <img id="overlay-img" src="" style="display:none;">
        <video id="overlay-video" controls style="display:none;"></video>
    </div>
    <script>
    function refresh() {
        location.reload(); // 刷新页面
    }
    function confirmDelete(fileName) {
        var password = prompt("请输入删除确认密码:");
        if (password) {
            deleteFile(fileName, password);
        }
    }

    function deleteFile(fileName, password) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'delete.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                alert(xhr.responseText);
                location.reload();
            }
        };
        xhr.send('filename=' + fileName + '&password=' + password);
    }

    function enlargeMedia(media) {
        var overlay = document.querySelector('.overlay');
        var overlayImg = document.getElementById('overlay-img');
        var overlayVideo = document.getElementById('overlay-video');

        if (media.tagName.toLowerCase() === 'img') {
            overlayImg.src = media.src;
            overlayImg.style.display = 'block';
            overlayVideo.style.display = 'none';
        } else if (media.tagName.toLowerCase() === 'video') {
            overlayVideo.src = media.src;
            overlayVideo.style.display = 'block';
            overlayImg.style.display = 'none';
        }

        overlay.style.display = 'flex';
    }

    function shrinkMedia() {
        var overlay = document.querySelector('.overlay');
        var overlayImg = document.getElementById('overlay-img');
        var overlayVideo = document.getElementById('overlay-video');

        overlayImg.style.display = 'none';
        overlayVideo.style.display = 'none';
        overlay.style.display = 'none';
    }

    function copyToClipboard(text) {
        var input = document.createElement('input');
        input.setAttribute('value', text);
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        document.body.removeChild(input);
        alert('已复制图片链接');
    }
    </script>
</body>
</html>
<div class="info-text">
            &copy; 2023
            <!--技术支持-->
            <a href="http://jyku.cn" style="text-decoration: none; color: #999;">JYKU.CN</a>
            提供图像和视频支持  
            <!--版本号-->
            <a href="http://doic.cn" style="text-decoration: none; color: #999;">v1.4.0</a>
        </div>

<!--统计代码请勿删除-->
<script charset="UTF-8" id="LA_COLLECT" src="//sdk.51.la/js-sdk-pro.min.js"></script>
<script>LA.init({id:"KFunBsxDl2bThYnS",ck:"KFunBsxDl2bThYnS"})</script>

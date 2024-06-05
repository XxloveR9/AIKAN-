<!DOCTYPE html>
<html>
<head>
    <title>AIKAN图床</title>
    <meta name="keywords" content="AIKAN, AIKAN图库, JIAYIN科技,无数据库图床,免费图床,PHP多图长传程序,自适应页面,一键复制链接" />
    <meta name="description" content="爱看图床AIKAN是一款支持多文件上传的无数据库图床,可以完美替代PHP多图上传程序,最新html5自适应页面兼容手机电脑,上传后返回图片直链,简单方便支持一键复制,支持多域名" />
    <link rel="icon" href="https://up.jyku.cn/up/24053110.png" type="image/x-icon">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="qrcode.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
        }

        .container {
            max-width: 400px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .upload-success {
            text-align: center;
            margin-top: 20px;
        }

        .upload-success a {
            display: inline-block;
            color: #000;
            font-size: 14px;
            text-decoration: none;
            margin: 10px 0;
        }

        .upload-success .copy-btn{
            background-color: #66CDAA;
            color: white;
            border: none;
            padding: 4px 6px;
            cursor: pointer;
            border-radius: 3px;
            margin: 5px 0;
            display: inline-block;
        }
        
        .upload-success .open-btn {
            background-color: #66CDAA;
            color: white;
            border: none;
            padding: 4px 6px;
            cursor: pointer;
            border-radius: 3px;
            margin: 5px 0;
            display: inline-block;
        }

        .file-preview {
            text-align: center;
            margin-top: 20px;
        }

        .file-preview img,
        .file-preview video {
            max-width: 100%;
            max-height: 100px;
            border-radius: 5px;
        }


        .file-input-label .custom-file-input {
            background-color: #CC99FF;
            color: white;
            border: none;
            padding: 8px 15px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }
        .submit-btn {
            background-color: #FFB6C1;
            color: white;
            border: none;
            padding: 8px 15px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }
        .info-text {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #999;
        }

        /* 新增样式 */
        .file-link {
            color: #000;
            font-size: 14px;
            text-decoration: none;
            display: block;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>AIKAN图床😎</h1>
        <div class="upload-success">
            <div>
                <label class="file-input-label">
                    <button onclick="window.location.href='./'" class="custom-file-input"><i class="fas fa-redo-alt"></i> 点击返回</button> &nbsp;&nbsp;&nbsp;&nbsp;
                    <button onclick="window.open('photo.php', '_blank')" class="submit-btn"><i class="fas fa-copy"></i> 查询图库</button>

                </label>
            </div>
            <br>

            <?php
                $targetDir = "up/";
                $allowTypes = array('jpg', 'png', 'jpeg', 'gif', 'mp4', 'mov', 'avi');

                // 检查目标目录是否存在，如果不存在则创建
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }

                if (!empty($_FILES['files']['name'][0])) {
                    $files = $_FILES['files'];
                    foreach ($files['name'] as $key => $val) {
                        $fileType = pathinfo($val, PATHINFO_EXTENSION);
                        if (in_array($fileType, $allowTypes)) {
                            // 获取上传时间作为文件名基础
                            $uploadTime = date('ymdH');
                            $fileIndex = 1;
                            $newFileName = $uploadTime . "_$fileIndex";

                            // 如果是图片文件，改为 .jpg 格式
                            if (in_array($fileType, array('jpg', 'jpeg', 'png', 'gif'))) {
                                $newFileName .= ".jpg";
                            } else {
                                $newFileName .= ".$fileType";
                            }

                            // 处理重复文件名
                            $newFilePath = $targetDir . $newFileName;
                            while (file_exists($newFilePath)) {
                                $fileIndex++;
                                $newFileName = $uploadTime . "_$fileIndex";
                                if (in_array($fileType, array('jpg', 'jpeg', 'png', 'gif'))) {
                                    $newFileName .= ".jpg";
                                } else {
                                    $newFileName .= ".$fileType";
                                }
                                $newFilePath = $targetDir . $newFileName;
                            }

                            if (move_uploaded_file($files['tmp_name'][$key], $newFilePath)) {
                                // 动态生成链接
                                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
                                $domain = $_SERVER['HTTP_HOST'];
                                $path = rtrim(dirname($_SERVER['REQUEST_URI']), '/\\');
                                $url = $protocol . $domain . $path . "/$newFilePath";

                                echo "<div class='file-preview'>";
                                if (in_array($fileType, array('mp4', 'mov', 'avi'))) {
                                    echo "<video controls><source src='$newFilePath' type='video/$fileType'></video>";
                                } else {
                                    echo "<img src='$newFilePath'>";
                                }
                                echo "</div>";
                                echo "<div>";
                                echo "<span class='file-link' id='fileUrl$key'>$url</span>";
                           
                                
                                echo "<button class='open-btn' onclick='window.open(\"$url\", \"_blank\")'><i class='fas fa-external-link-alt'></i> 打开图片</button>";
                                echo " <button class='copy-btn' onclick='copyToClipboard($key)'><i class='fas fa-copy'></i> 复制链接</button>";

                                echo "</div>";
                            } else {
                                echo "文件上传失败: $val";
                            }
                        } else {
                            echo "不支持的文件类型: $val";
                        }
                    }
                } else {
                    echo "请先选择文件再上传。";
                }
            ?>
        </div>
        <div class="info-text">
            &copy; 2023
            <!--技术支持-->
            <a href="http://jyku.cn" style="text-decoration: none; color: #999;">JYKU.CN</a>
            提供图像和视频支持  
            <!--版本号-->
            <a href="http://doic.cn" style="text-decoration: none; color: #999;">v1.4.0</a>
        </div>
    </div>
    <script>
        function copyToClipboard(id) {
            var copyText = document.getElementById('fileUrl' + id).textContent;
            var textArea = document.createElement("textarea");
            textArea.value = copyText;
            document.body.appendChild(textArea);
            textArea.select();
            textArea.setSelectionRange(0, 99999);
            document.execCommand('copy');
            document.body.removeChild(textArea);
            alert('链接已复制: ' + copyText);
        }
    </script>
</body>
</html>




<!--统计代码请勿删除-->
<script charset="UTF-8" id="LA_COLLECT" src="//sdk.51.la/js-sdk-pro.min.js"></script>
<script>LA.init({id:"KFunBsxDl2bThYnS",ck:"KFunBsxDl2bThYnS"})</script>

<!-- 右下角SSL安全认证 https://up.jyku.cn/up/24052208.png-->
<div id="cc-myssl-id" style="position: fixed;right: 0;bottom: 0;width: 65px;height: 65px;z-index: 99;">
    <a href="https://myssl.com/up.jyku.cn?from=mysslid"><img src="https://static.myssl.com/res/images/myssl-id.png" alt="" style="width:100%;height:100%"></a>
</div>

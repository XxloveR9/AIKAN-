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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 20px;
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

        form {
            text-align: center;
            margin-top: 20px;
        }

        .file-input {
            margin-bottom: 10px;
        }

        .submit-btn {
            background-color: #66CDAA;
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

        .file-preview {
            margin-top: 20px;
            text-align: center;
            display: none;
        }

        .file-preview img,
        .file-preview video {
            max-width: 100%;
            max-height: 200px;
            margin-top: 10px;
            border-radius: 5px;
        }

        .info-text {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #999;
        }

        .file-input-label {
            display: block;
            text-align: center;
            margin-bottom: 20px;
            font-size: 16px;
            color: #333;
        }

        .file-input-label input[type="file"] {
            display: none;
        }

        .file-input-label .custom-file-input {
            background-color: #6495ED;
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

        .file-preview-container {
            margin-top: 20px;
            text-align: center;
            display: none;
        }

        .file-preview-container img,
        .file-preview-container video {
            max-width: 100%;
            max-height: 200px;
            margin-top: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>AIKAN图床<h1>
        <form action="upload.php" method="post" enctype="multipart/form-data" >
            <!--加入后上传必须输入密码：onsubmit="return confirmPassword()"-->
            <label for="file" class="file-input-label">
                <span class="custom-file-input"><i class="fas fa-images"></i> 选择相册</span> &nbsp;&nbsp;
                <input type="file" name="files[]" id="file" class="file-input" accept="image/*,video/*" multiple>
                &nbsp;&nbsp;
                <button type="submit" name="submit" class="submit-btn"><i class="fas fa-upload"></i> 确认上传</button>
            </label>
        </form>
        <div class="file-preview-container" id="preview-container">
            <div id="preview-content"></div>
        </div>
        <div class="info-text">
            &copy; 2023
            <!--技术支持-->
            <a href="http://jyku.cn" style="text-decoration: none; color: #999;">JYKU.CN</a>
            提供图像和视频支持  
            <!--版本号-->
            <a href="http://doic.cn" style="text-decoration: none; color: #999;">v1.4.0</a>
            <!--
            <a href="photo.php" style="text-decoration: none; color: red;">&copy;查询图库</a>
            -->
         
          <!-- 备案相关 
          <br><br>
          <a href="http://beian.miit.gov.cn" style="text-decoration: none; color: #999;" target="_blank">渝ICP备xxxxxxx号</a>
          
	      <img src="https://p3-pc.douyinpic.com/tos-cn-i-tsj2vxp0zn/emblem.png~tplv-tsj2vxp0zn-image.image" style="width:16px;"/>
	      
	      <a href="http://www.beian.gov.cn/portal/registerSystemInfo?recordcode=50010802002803" target="_blank" style="text-decoration: none; color: #999;">渝公网安备xxxxxxxxx号</a>
          <!-- 备案相关END -->
            
            
            
            
        </div>
    </div>

    <script>
        function confirmPassword() {
            const password = prompt('请输入确认密码:');
            //设置你的上传密码，默认6
            if (password === '6') {
                return true;
            } else {
                alert('密码错误！');
                return false;
            }
        }

        const fileInput = document.getElementById('file');
        const previewContainer = document.getElementById('preview-container');
        const previewContent = document.getElementById('preview-content');
        const MAX_FILE_SIZE = 30;

        fileInput.addEventListener('change', (e) => {
            previewContent.innerHTML = ''; // Clear previous content
            const files = e.target.files;
            let totalFileSize = 0;

            Array.from(files).forEach(file => {
                const fileSizeMB = file.size / (1024 * 1024);
                totalFileSize += fileSizeMB;
                if (totalFileSize > MAX_FILE_SIZE) {
                    alert('总文件大小不能超过 ' + MAX_FILE_SIZE + 'MB');
                    fileInput.value = '';
                    previewContainer.style.display = 'none';
                    return;
                }

                const reader = new FileReader();
                reader.onload = (event) => {
                    const fileType = file.type;
                    const previewItem = document.createElement('div');

                    if (fileType.startsWith('image/')) {
                        const img = document.createElement('img');
                        img.src = event.target.result;
                        img.style.maxWidth = '100%';
                        img.style.maxHeight = '200px';
                        img.style.borderRadius = '5px';
                        previewItem.appendChild(img);
                    } else if (fileType.startsWith('video/')) {
                        const video = document.createElement('video');
                        video.src = event.target.result;
                        video.controls = true;
                        video.style.maxWidth = '100%';
                        video.style.maxHeight = '200px';
                        video.style.borderRadius = '5px';
                        previewItem.appendChild(video);
                    }

                    const fileSizeText = document.createElement('div');
                    fileSizeText.textContent = `文件大小: ${fileSizeMB.toFixed(2)}MB`;
                    fileSizeText.style.fontSize = '12px';

                    const fileNameText = document.createElement('div');
                    fileNameText.textContent = `文件名称: ${file.name}`;
                    fileNameText.style.fontSize = '12px';

                    const fileInfoContainer = document.createElement('div');
                    fileInfoContainer.style.display = 'flex';
                    fileInfoContainer.style.alignItems = 'center';
                    fileInfoContainer.style.justifyContent = 'center';
                    const space = document.createTextNode('\u00A0\u00A0');
                    fileInfoContainer.appendChild(fileNameText);
                    fileInfoContainer.appendChild(space);
                    fileInfoContainer.appendChild(fileSizeText);

                    previewItem.appendChild(fileInfoContainer);
                    previewContent.appendChild(previewItem);
                };

                reader.readAsDataURL(file);
            });

            previewContainer.style.display = totalFileSize > 0 ? 'block' : 'none';
        });
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

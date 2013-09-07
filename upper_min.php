<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Upper</title>
</head>
<body>

	<?php
		//アップロードされた画像を捕捉する。
		capture_uploaded_image();
	?>


	<div class=uploadform>
	<form action="<?php echo basename($_SERVER['PHP_SELF']);?>?upload=true" method="post" enctype="multipart/form-data">
		<h2>画像ファイルをアップロード：</h2><br>
		<input type="file" name="upfile" size="30" /><br>
		ユーザ名<font size="-1" color="red"> (オプション)*</font>
		<input type="text" name="username" size="30" /><br>
		パスワード<font size="-1" color="red"> (オプション)*</font>
		<input type="text" name="passwd" size="30" /><br>
		<input type="submit" value="アップロード" />
	</form>
	</div>


<?php
//以下はPHPモジュール
	
	//ファイルアップロードを捕捉して、画像ファイルを保存する。
	function capture_uploaded_image(){
		$up_dir = "imgs/";	//画像ファイルを保存するディレクトリ
		
		//アップロードの検出
		if (!isset($_GET["upload"])){
			//アップロードフラグが立っていない=通常のページロード
			return true;
		}
		
		//アップロードエラーのチェック
		if (UPLOAD_ERR_OK != $_FILES['upfile']['error']){
			//アップロードエラーのエラーコード一覧
			// http://php.net/manual/ja/features.file-upload.errors.php
			print ( "アップロードエラー:(" .$_FILES['upfile']['error'] .")<br>\n");
			return false;
		}
		
		//アップロードファイルの存在チェック
		if (! is_uploaded_file($_FILES["upfile"]["tmp_name"])) {
			//ファイルが選択されていない状態でアップロードボタンをクリックした場合
			print ( "アップロードエラー：ファイルが選択されていません。<br>\n");
			return false;
		}
		
		//ファイルが画像か否かを判定
		if (false == $ext = is_img($_FILES["upfile"]["tmp_name"])){
			print ( "アップロードエラー：対応する形式の画像ファイルではありませんでした<br>\n");
			return false;
		}
		
		//保存先ディレクトリのチェック
		if (!is_dir($up_dir)){
			print ("アップロードエラー：保存先ディレクトリが存在しませんでした<br>\n");
			return false;
		}
		if(!is_writable($up_dir)){
			print ("アップロードエラー：保存先ディレクトリに書き込む権限がありませんでした<br>\n");
			return false;
		}

			
		//画像ファイルを保存(注意：同名ファイルがあると上書きされる)
		$filepath = $up_dir . $_FILES["upfile"]["name"];	
		if (move_uploaded_file($_FILES["upfile"]["tmp_name"], $filepath)) {
			chmod($filepath, 0644);
			print ( "ファイル\"". $_FILES["upfile"]["name"] . "\"をアップロードしました。<br><br>\n");
			return true;
		} else {
			print ( "アップロードエラー：ファイルのアップロードに失敗しました<br>\n");
			return false;
		}
	}
	
	
	
	//画像ファイルか否かを判定する
	function is_img($img_path=""){
			if (!(file_exists($img_path) and $type=exif_imagetype($img_path))){
				return false;
			}
			if (IMAGETYPE_GIF == $type){
				return 'gif';
			}else if (IMAGETYPE_JPEG == $type){
				return 'jpg';
			}else if (IMAGETYPE_PNG == $type){
				return 'png';
			}else{
				return false;
			}
	}

?>

</body>
</html>

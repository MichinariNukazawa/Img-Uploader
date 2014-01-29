<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Upper</title>


	<!--ファイルアップロード欄に選択済みファイルの名前を表示する-->
	<script type="text/javascript">
		// ファイル名を切り出す
		function basename( path ) {
				return path.replace(/\\/g,'/').replace( /.*\//, '' );
		}
		$("document").ready(function(){
			$("#fileupload").change(function(){
				var name = basename( this.value );
				//alert('changed!' + name );
				var filenameArea = document.getElementById( 'fileupload_name' );
				filenameArea.innerText = name;
			});
		});

		// アップロード前の選択画像をサムネイル
		function selectImage(_this, ev) {
			var reader = new FileReader();
			reader.onload = function(e) {
				var img = document.getElementById("thumbnail_upselect");
				img.src = reader.result;
			};
			reader.readAsDataURL(_this.files[0]);
		}
	</script>

</head>
<body>

	<?php
		// アップロードされた画像を捕捉する。
		capture_uploaded_image();
	?>


	<div class=uploadform>
	<form action="<?php echo basename($_SERVER['PHP_SELF']);?>?upload=true"
	method="post" enctype="multipart/form-data">
		<h2>画像ファイルをアップロード：</h2><br>
		<!-- ここの画像がサムネイルになる-->
		<img id="thumbnail_upselect" src="img/upload_img.jpg"
		class="image_frame" height="100">
		<br />
		<!-- ファイル選択により、サムネイルを表示するjsが呼び出される -->
		<input type="file" name="upfile" size="30"
		id="fileupload" onchange="selectImage(this,event);"/>
		<br />
		<input type="submit" value="アップロード" />
	</form>
	</div>


<?php
//以下はPHPモジュール
	
	//ファイルアップロードを捕捉して、画像ファイルを保存する。
	function capture_uploaded_image(){
		$up_dir = "uploads/";	//画像ファイルを保存するディレクトリ
		
		//アップロードの検出
		if (!isset($_GET["upload"])){
			//アップロードフラグが立っていない=通常のページロード
			return true;
		}
		
		//アップロードエラーのチェック
		if (UPLOAD_ERR_OK != $_FILES['upfile']['error']){
			//アップロードエラーのエラーコード一覧
			// http://php.net/manual/ja/features.file-upload.errors.php
			switch($_FILES['upfile']['error']){
			case UPLOAD_ERR_INI_SIZE:
				// php.ini設定ファイルに設定された upload_max_filesize 上限。
				echo "アップロード可能なサイズ("
					. ini_get('upload_max_filesize') . ")を超えています";
				break;
			case UPLOAD_ERR_FORM_SIZE:
				// ブラウザに知らせた上限。本サンプルではこれを指定していない
				print("アップロード可能なサイズ(" . $_POST['MAX_FILE_SIZE'] . ")を超えています");
				break;
			/*
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				echo "アップロード可能な最大サイズ(" . getMaxUploadSize(). ")を超えています";
			break;
			*/
			default:
				// その他のエラーはユーザからすれば同じもの(サーバ側の問題)なのでまとめて表示。
				print ( "アップロードエラーです:(" .$_FILES['upfile']['error'] .")<br>\n");
				break;
			}
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

		// 同名の画像ファイルが上書きされるのを防ぐ
		// (注意：ページリロード時に、同名ファイルが連続で投稿されることになるので、要対策)
		// (注意：不正なファイル名により攻撃されるので、要対策)
		$i = 0;
		do {
			$localFilename = $i. "_" .htmlspecialchars( $_FILES["upfile"]["name"],
				ENT_QUOTES, "UTF-8");
			$i++;
			$localFilePath = conv_str_to_local( $up_dir .$localFilename);
		}while ( file_exists( $localFilePath ) );

		// 画像ファイルを保存
		if (move_uploaded_file($_FILES["upfile"]["tmp_name"], $localFilePath )) {
			chmod( $localFilePath, 0644);
			echo "ファイル\"". htmlspecialchars( $_FILES["upfile"]["name"] , ENT_QUOTES, "UTF-8") 
				. "\"をアップロードしました。<br><br>\n";
		} else {
			print ( "アップロードエラー：ファイルのアップロードに失敗しました<br>\n");
			return false;
		}
	}
	
	
	
	//画像ファイルか否かを判定する関数
	function is_img($img_path=""){
		if (!(file_exists($img_path) and $type = exif_imagetype($img_path))){
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

	// アップロードファイルサイズの上限を取得する
	function getMaxUploadSize(){
		/*アップロードファイルサイズの上限は以下の3項目のうちの最小値。
		memory_limit > post_max_size > upload_max_filesize
		*/
		$limit_max = (ini_get_bytes('post_max_size') < ini_get_bytes('upload_max_filesize') )?
					'post_max_size' : 'upload_max_filesize';
		$limit_max = (ini_get_bytes($limit_max) < ini_get_bytes('memory_limit'))?
					$limit_max : 'memory_limit';
		return ini_get($limit_max);
	}

	// ファイルサイズを整数表現に変換して返す。	
	function ini_get_bytes($varname) {
		$val = ini_get($varname);
		$val = trim($val);
		$last = strtolower($val[strlen($val)-1]);
		switch($last) {
		    // 'G' は PHP 5.1.0 以降で使用可能です
		    case 'g':
		        $val *= 1024;
		    case 'm':
		        $val *= 1024;
		    case 'k':
		        $val *= 1024;
		}

		return $val;
	}

	// 環境を判定して文字コード変換(内部コード->環境の文字コード)
	// (Windows(XAMPP)対応)
	function conv_str_to_local ($inString){
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ){
			//xampp対応。Windows系の場合、文字コードを変換。
			$localString = mb_convert_encoding($inString,"CP932", "UTF-8");
		}else{
			$localString = $inString;
		}
		
		return $localString;
	}

?>

</body>
</html>

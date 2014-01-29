php+HTMLによる画像アップローダ・プログラム(Webページ)です。


phpを使った画像アップローダです。


特徴として、アップローダ機能が一枚のWebページで完結していることが挙げられます。

アップローダのサンプルの多くは、アップロードフォームと、アップロード後のページが別ページになっていますが、本サンプルならば、メインページのアップロードフォームから遷移したくない場合などに対応できます。

本ソースはブログ記事(http://blog.michinari-nukazawa.com/2013/09/php-image-file-uploader-web-page-source.html)に書いたものです。


##使い方
upper_min.php, upper.php ともに、配置したのと同じ階層に、Apacheが書き込み可能な権限を持つuploads/ディレクトリが必要です。

(*nixおよびmac環境の方は、"chmod A+w uploads/"コマンドなどで、適切な権限設定を行なってください。


##upper_min.php
記述量の少ない画像アップローダです。

画像ファイルの判定を中心として、最低限のエラーチェックを含みます。

 * 再アップロードの対策をしていません(画像アップロードの後でページをリロードすると、画像が再度アップロードされます)。
 * 同名ファイルがアップロードされると、前のファイルが上書きされて消えます。
 * PHPのデフォルト設定では、2MBより大きな画像ファイルをアップロードできません。アップロードエラーになります。

##upper.php
同名の画像ファイルがアップロードされても、前のファイルが上書きされなくなりました。

(その代わりに、ページリロードなどで同じ画像ファイルが連続投稿されても、別ファイルとして扱われ、ディスクスペースを消費します。)

 * 再アップロードの対策をしていません(画像アップロードの後でページをリロードすると、画像が再度アップロードされます)。

 * アップロード対象ファイルをサムネイル表示するJavaScriptを含みます
 (画像を選択する前は、画像はリンク切れ表示になっています。これは本プログラムの仕様です。)
 * アップロード可能なファイルの最大サイズを計算する関数を含みます。
 * Windows(xampp)環境でファイル名が文字化けする問題に対処する関数を含みます。
 * アップロードエラーが発生した際、原因を分類し、表示します。


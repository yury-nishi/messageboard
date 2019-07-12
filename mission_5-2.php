<html>
<head>
<meta charset="utf-8">

<title>Mission5-1</title>
</head>

<body>

<?php

//データベース接続
//MySQL=データベース管理システム。大量アクセスに対応可能。
	$dsn = 'データベース名';
	$user = 'ユーザー名';
	$password = 'パスワード';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
	
	/*array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING)とは、データベース操作で発生したエラーを
	警告として表示してくれる設定をするための要素です。*/

	$name = '';   //投稿者名
	$comment = '';     //投稿コメント
	$pass = '';
	$error = array();  //エラーメッセージは配列$errorに入れる
	$data = "";        //名前,ひとこと、日時をここに入れる



//テーブル作成
$sql = "CREATE TABLE IF NOT EXISTS messageboard"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY," //1ずつ数値が追加されていく
	. "name char(32)," //charは格納される列が32バイトになる。（長さ調整）
	. "comment TEXT,"
	. "date TEXT,"
	. "pass TEXT"
	.");";
	$stmt = $pdo->query($sql); //queryは$sqlをデータベースへ届ける

//->は左辺から右辺を取り出す
	

	/*IF NOT EXISTSを入れないと２回目以降にこのプログラムを呼び出した際に、
	SQLSTATE[42S01]: Base table or view already exists: 1050 Table 'tbtest' already exists
	という警告が発生します。これは、既に存在するテーブルを作成しようとした際に発生するエラーです。*/




//投稿フォーム条件分岐
if ( isset( $_POST['submit'] ) === true ) {

$name = $_REQUEST['name'];
$comment = $_REQUEST['comment'];
$date = date('Y/m/d H:i:s');
$pass = $_REQUEST["password"];
$hide = $_REQUEST["hide"];


     if ( $name !== '' && $comment !== '' && $pass !=='') {
	if(empty($hide)){

//データ入力
	$sql = $pdo -> prepare("INSERT INTO messageboard (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)");
	$sql -> bindParam(':name', $name, PDO::PARAM_STR);
	$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
	$sql -> bindParam(':date', $date, PDO::PARAM_STR);
	$sql -> bindParam(':pass', $pass, PDO::PARAM_STR);

	$sql -> execute();  //クエリ（問い合わせ）の実行

	}else{
//編集フォーム条件分岐
	$id = $_POST["hide"];
	$name = $_POST["name"];
	$comment = $_POST["comment"];
	$date = date('Y/m/d H:i:s')."(編集済み)";
	$pass = $_POST["password"];

	$sql = 'update messageboard set name=:name, comment=:comment, date=:date, pass=:pass where id=:id';
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':name', $name, PDO::PARAM_STR);
	$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
	$stmt->bindParam(':date', $date, PDO::PARAM_STR);
	$stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
	$stmt->bindParam(':id', $id, PDO::PARAM_INT);		

	$stmt->execute();

	}

    }


}

//削除フォーム条件分岐____________________________________

if(isset($_POST['delete_submit']) &&!empty($_POST["delete_number"]) && !empty($_POST["delete_pass"])){

$delete_number = $_POST["delete_number"];
$del_select = "SELECT * FROM messageboard WHERE id = $delete_number";
$stmt_del = $pdo -> query($del_select);
$del_res = $stmt_del -> fetchAll();


foreach($del_res as $div_del){

	if($div_del["pass"] == $_POST["delete_pass"]){
		$id = $_POST["delete_number"];
		$name = "";
		$comment = "---このコメントは削除されました---";
		$date = "";
		$pass = "";

		$sql = 'update messageboard set name=:name, comment=:comment, date=:date, pass=:pass where id=:id';
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':name', $name, PDO::PARAM_STR);
		$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
		$stmt->bindParam(':date', $date, PDO::PARAM_STR);
		$stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);		

		$stmt->execute();
	  }
}
}

//編集フォーム条件分岐_______________________________________

if(isset($_POST['edit_submit']) &&!empty($_POST["edit_number"]) && !empty($_POST["edit_pass"])){

$edit_number = $_POST["edit_number"];
$edit_select = "SELECT * FROM messageboard WHERE id = $edit_number";
$stmt_edi = $pdo -> query($edit_select);
$edit_res = $stmt_edi -> fetchAll();

foreach($edit_res as $div_edi){
	if($div_edi["pass"] == $_POST["edit_pass"]){
		$id = $_POST["edit_number"];

	}	
}
}


echo "<hr>";

//4-6データをブラウザ表示
	$sql = 'SELECT * FROM messageboard';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
		//$rowの中にはテーブルのカラム名が入る
		echo $row['id'].',';
		echo $row['name'].',';
		echo $row['comment'].',';
		echo $row['date'].',';
		echo $row['pass'].'<br>';

	echo "<hr>";
	}


?>



<!-- 投稿フォーム -->
<form action="mission_5-1.php" method="POST" >
<p>
 名前：<br/>
<input type="text" name="name" value="<?php if(!empty($_POST["edit_pass"]) && $div_edi["pass"] == $_POST["edit_pass"]) : echo $div_edi["name"];?><?php endif;?>"/><br/>
コメント：<br/>
<input type ="text" name="comment" value="<?php if(!empty($_POST["edit_pass"]) && $div_edi["pass"] == $_POST["edit_pass"]) : echo $div_edi["comment"];?><?php endif;?>"/><br/>
<input type="text" name="password" placeholder="パスワード" value="<?php if(!empty($_POST["edit_pass"]) && $div_edi["pass"] == $_POST["edit_pass"]) : echo $div_edi["pass"];?><?php endif;?>"/><br/>
<input type = "hidden" name="hide" value="<?php if(!empty($_POST["edit_pass"])&& $div_edi["pass"] == $_POST["edit_pass"]) : echo $_POST["edit_number"];?><?php endif; ?>"/><br/>
 <input type="submit" name="submit" value="送信" />
</p>
</form>

<?php
if(isset($_POST["submit"])){

     if(empty($name)){
	echo "※名前が入力されていません。"."<br>";}
     if(empty($comment)){
	echo "※コメントが入力されていません。"."<br>";}
     if(empty($pass)){
	echo "※パスワードが入力されていません。"."<br>";}

     if(!empty($hide)){
	echo "編集が完了しました。"."<br>";}
}
?>

<hr>

<!-- 削除フォーム -->
<form action="mission_5-1.php" method="POST">
<p>
削除番号：<br/>
<input type="text" name="delete_number" placeholder="例：1 (半角入力）" /><br/>
<input type="text" name="delete_pass" placeholder="パスワード" />
<input type = "submit" name="delete_submit" value="削除" />
</p>
</form>

<?php
if(isset($_POST["delete_submit"])){

     if(!empty($_POST["delete_number"]) && !empty($_POST["delete_pass"])){

	if($div_del["pass"] == $_POST["delete_pass"]){
	echo "コメントは削除されました。"."<br>";

	}else{
	echo "※削除番号かパスワードが一致しません。"."<br>";}


     }if(empty($_POST["delete_number"])){
	echo "※削除番号が入力されていません。"."<br>";

     }if(empty($_POST["delete_pass"])){
	echo "※パスワードが入力されていません。"."<br>";}
}
?>

<!-- 編集フォーム -->
<form action="mission_5-1.php" method="POST">
<p>
編集番号：<br/>
<input type="text" name="edit_number" placeholder="例：1(半角入力)"/><br/>
<input type="text" name="edit_pass" placeholder="パスワード" />
<input type="submit" name="edit_submit" value="編集"/>
</p>
</form>

<?php
if(isset($_POST["edit_submit"])){
	if(empty($_POST["edit_number"])){
	echo "※編集番号が入力されていません。"."<br>";}

	if(empty($_POST["edit_pass"])){
	echo "※パスワードが入力されていません。"."<br>";}

	if(!empty($_POST["edit_pass"]) && !empty($_POST["edit_number"])){

	    if ($div_edi["pass"] !== $_POST["edit_pass"]){
	     echo "※パスワードが一致しません。"."<br>";}}

}

?>


</body>
</html>

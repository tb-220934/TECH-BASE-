<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>
<body>
<?php 
// DB接続設定
	$dsn = 'データベース名';
	$user = 'ユーザー名';
	$password = 'パスワード';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
//テーブル作成	
    $sql = "CREATE TABLE IF NOT EXISTS m5_1"
        ." ("
	    . "id INT AUTO_INCREMENT PRIMARY KEY,"
        . "name char(32),"
        . "comment TEXT,"
        . "date datetime,"
        . "password char(14)"
        .");";
        $stmt = $pdo->query($sql);
?>

<?php
//編集ボタンが押されて
    if(isset($_POST["hensyunum"])){
//編集フォームに数字とパスワードのデータがあって
        if(!empty($_POST["hensyu"])&&!empty($_POST["hensyupass"])){
//編集番号の定義
            $hensyu = $_POST["hensyu"];
//パスワードの定義
            $hensyupass = $_POST["hensyupass"];
            
            $sql = 'SELECT * FROM m5_1 WHERE id=:id';
	        $stmt = $pdo->prepare($sql);
	        $stmt->bindParam(':id', $hensyu, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch();

            if($hensyupass==$result['password']){
                $name = $result['name'];
                $comment = $result['comment'];}
            else{
                unset($hensyu);}
            }    
        }
    ?>
    
    <form action="" method="post">
<!-- 投稿のフォーム -->
    【投稿】<br>
       <input type="text" name="nam" placeholder="名前" 
              value="<?php if(isset($name)){echo $name;}?>">
       <input type="text" name="str" placeholder="コメント"
              value="<?php if(isset($comment)){echo $comment;}?>">
       <input type="text" name="pass" placeholder="パスワード" 
              value="<?php if(isset($hensyupass)){echo $hensyupass;}?>">
       <input type="hidden" name="editnum" placeholder="投稿番号"
              value="<?php if(isset($hensyu)){echo $hensyu;}?>">
       <input type="submit" name="submit" value="投稿"><br>
<!-- 編集のフォーム -->
    【編集】※半角数字のみ<br>
       <input type = "text" name = "hensyu" placeholder = "編集したい番号">番  
       <input type = "text" name = "hensyupass" placeholder = "パスワード">
       <input type = "submit" name="hensyunum" value = "編集" ><br>
<!-- 削除のフォーム -->
    【削除】※半角数字のみ<br>
       <input type = "text" name = "delete" placeholder = "削除したい番号">番   
       <input type = "text" name = "deletepass" placeholder = "パスワード">
       <input type = "submit" name="deletenum" value = "削除" ><br>
    </form>
    
<?php

//投稿の受け取り
//投稿ボタンが押されて
    if(isset($_POST["submit"])){
//投稿フォームに名前とコメントとパスワードのデータがあって
        if(!empty($_POST["nam"])&&!empty($_POST["str"])&&!empty($_POST["pass"])){
//入力フォームのデータを受け取る
            $nam = $_POST["nam"];
            $str = $_POST["str"];
            $pass = $_POST["pass"];
            $date = date("Y/m/d H:i:s");
//「いま送信された場合は編集か、新規投稿か」を判断する情報を追加する

//編集ver
            if(!empty($_POST["editnum"])){
                $editnum=$_POST["editnum"];
                
                if(empty($pass)){
                    echo "パスワードをいれてね！<br>";}
                else{
                    $sql = 'SELECT * FROM m5_1 WHERE id=:id';
                    $stmt = $pdo->prepare($sql);
	                $stmt->bindParam(':id', $editnum, PDO::PARAM_INT);
                    $stmt->execute();    
                
                    $result = $stmt->fetch();
//パスワード一致してれば                
                    if($pass==$result['password']){ 
                    
	                    $date = date("Y/m/d H:i:s");
	              	    $sql = 'UPDATE m5_1 SET name=:name,comment=:comment,date=:date WHERE id=:id';
    	                $stmt = $pdo->prepare($sql);
	                    $stmt->bindParam(':name', $nam, PDO::PARAM_STR);
                        $stmt->bindParam(':comment', $str, PDO::PARAM_STR);
                        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
                        $stmt->bindParam(':id', $editnum, PDO::PARAM_INT);
                        $stmt->execute();}
                    else{
                        echo "パスワードが違うよ！<br>";}
                    }
                }
            else{
//編集番号が無いときは新規投稿 

            $sql = "INSERT INTO m5_1 (name, comment,date,password) VALUES (:name, :comment,:date,:password)";
            $stmt = $pdo -> prepare($sql);
	        $stmt -> bindParam(':name', $nam, PDO::PARAM_STR);
	        $stmt -> bindParam(':comment', $str, PDO::PARAM_STR);
            $stmt -> bindParam(':date', $date, PDO::PARAM_STR);
            $stmt -> bindParam(':password', $pass, PDO::PARAM_STR);
            $stmt -> execute();}
            }
        }
        
//削除の受け取り
//削除ボタンが押されて
    if(isset($_POST["deletenum"])){
//削除フォームに数字とパスワードのデータがあって
        if(!empty($_POST["delete"])&&!empty($_POST["deletepass"])){
//削除番号の定義
            $delete = $_POST["delete"];
//パスワードがあったら
            if(!empty($_POST["deletepass"])){
//パスワードの定義
                $deletepass = $_POST["deletepass"];
            
                $sql = 'SELECT * FROM m5_1 WHERE id=:id';
                $stmt = $pdo->prepare($sql);
	            $stmt->bindParam(':id', $delete, PDO::PARAM_INT);
                $stmt->execute();
                
                $result = $stmt->fetch();
                
                if($deletepass==$result['password']){
    	            $sql = 'delete from m5_1 where id=:id';
	                $stmt = $pdo->prepare($sql);
	                $stmt->bindParam(':id', $delete, PDO::PARAM_INT);
	                $stmt->execute();}
	            else{
                    echo "パスワードが違います！<br>";}
                }
            else{
                echo "パスワードをいれてください！<br>";}
            }
        }
        
// 表示
    echo "-----------------------<br>";
    echo "【投稿一覧】<br>";
    
    $sql = 'SELECT * FROM m5_1';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
		//$rowの中にはテーブルのカラム名が入る
		echo $row['id'].',';
		echo $row['name'].',';
		echo $row['comment'].',';
        echo $row['date'].'<br>';
	    echo "<hr>";}
	
?>
    
</body>
</html>
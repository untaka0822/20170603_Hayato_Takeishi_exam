<?php
session_start();
require('dbconnect.php');

$email='';
$password='';

$errors = array();

if (isset($_COOKIE['email']) && $_COOKIE['email'] == '') {

    $_POST['email'] = $_COOKIE['email'];
    $_POST['password'] = $_COOKIE['password'];
    $_POST['save'] = 'on';

}

// ログインボタンが押された時
if (!empty($_POST)) {
	$email = $_POST['email'];
	$password = $_POST['password'];

// 入力されたメールアドレスとパスワードの組み合わせがデータベースに登録されているかチェック
	if ($email != '' && $password != '') {

		$sql = 'SELECT * FROM `members` WHERE `email`=? AND `password`=?';
		$data = array($email, sha1($password));
		$stmt = $dbh->prepare($sql);
		$stmt->execute($data);
		$record = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($record == false) {
        $errors['login'] = 'failed';
		} else {

		    $_SESSION['login_member_id'] = $record['member_id'];
        $_SESSION['time'] = time();

        // 自動ログイン設定
        if ($_POST['save'] == 'on') {
          // クッキーにログイン情報を保存
          setcookie('email', $email, time() + 60*60*24*30); // 保存期間
          setcookie('password', $password, time() + 60*60*24*30);
          // 使い方 setcookie(キー, 値, 保存期間);
        }

				header('Location: top.php');
				exit();
		}

  } else {
    $errors['login'] = 'blank';
  }
}

?>
<br>
<br>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
	<title>NexSeed Book</title>
	<link href="../assets/css/bootstrap.css" rel="stylesheet">
    <link href="../assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="../assets/css/form.css" rel="stylesheet">
    <link href="../assets/css/timeline.css" rel="stylesheet">
    <link href="../assets/css/main.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
          <!-- Brand and toggle get grouped for better mobile display -->
          <div class="navbar-header page-scroll">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="login.php" style="font-family: serif;"><span class="strong-title"><i class="fa fa-facebook"></i> NexSeed Book</span></a>
          </div>
          <!-- Collect the nav links, forms, and other content for toggling -->
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="nav navbar-nav navbar-right">
              </ul>
          </div>
          <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
</nav>

  <div class="container" style="text-align: center">
  	<h1 style="border-bottom: 1px solid #e5e5e5; padding: 5px; font-family: serif;">ログイン</h1>
      <div class="row">
        <form method="POST" action="">
          <div class="col-sm-12">
    			<label style="font-family: serif;">メールアドレス</label><br>
    			<input type="email" name="email" value="<?php echo $email; ?>">
      			<?php if(isset($errors['login']) && $errors['login'] == 'blank'): ?>
      				<p style="color: red; font-size: 10px; margin-top: 2px; font-family: serif;">
      					メールアドレスとパスワードを入力してください
      				</p>
      			<?php endif; ?>

            <?php if(isset($errors['login']) && $errors['login'] == 'failed'): ?>
              <p style="color: red; font-size: 10px; margin-top: 2px; font-family: serif;"">
                ログインに失敗しました。再度正しい情報でログインしてください
              </p>
            <?php endif; ?>
          </div>
          <div class="col-sm-12">
      			<label style="font-family: serif;">パスワード</label><br>
      			<input type="password" name="password" value="<?php echo $password; ?>">
          </div>
          <div class="col-sm-12" style="margin-top: 10px; margin-left: 60px; margin-bottom: 10px">
        		<input type="submit" value="ログイン" class="btn btn-default" style="font-family: serif;">
            <input type="checkbox" name="save" value="on" style="font-family: serif;">自動ログイン機能
          </div>
          <div class="col-sm-12">
            <a href="../join/index.php" class="btn btn-default" style="font-family: serif;">会員登録に戻る</a>
          </div>
        </form> 
    </div>
   </div>

   <nav class="navbar navbar-default navbar-fixed-bottom">
      <div class="container">
          <!-- Brand and toggle get grouped for better mobile display -->
          <div class="navbar-header page-scroll">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="login.php" style="font-family: serif; margin-left: 860px"><span class="strong-title"><i class="fa fa-facebook"></i> NexSeed Book</span></a>
          </div>
          <!-- Collect the nav links, forms, and other content for toggling -->
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="nav navbar-nav navbar-right">
              </ul>
          </div>
          <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
   </nav>

</body>
</html>

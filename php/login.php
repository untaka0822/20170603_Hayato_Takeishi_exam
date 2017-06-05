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

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>NexSeed Book</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/login.css">
    <link rel="stylesheet" type="text/css" href="../assets/js/login.js">
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="pr-wrap">
                <div class="pass-reset">
                    <label>
                        Enter the email you signed up with</label>
                    <input type="email" placeholder="Email" />
                    <input type="submit" value="Submit" class="pass-reset-submit btn btn-success btn-sm" />
                </div>
            </div>
            <div class="wrap">
                <p class="form-title">
                    Log In</p>
                <form class="login" method="POST" action="">
                  <input type="text" name="email" value="<?php echo $email; ?>" placeholder="Email" />

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

                  <input type="password" name="password" value="<?php echo $password; ?>" placeholder="Password" />

                  <input type="submit" value="Log In" class="btn btn-success btn-xs" />
                <div class="remember-forgot">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="save" value="on" />
                                    Remember me
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 forgot-pass-content">
                            <a href="../join/index.php" class="forgot-pass">Forgot Password</a>
                        </div>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    <div class="posted-by">Hayato Takeishi</div>
</div>

</body>
</html>
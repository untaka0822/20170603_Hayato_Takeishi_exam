<?php
session_start();
require('../php/dbconnect.php');

$nick_name = '';
$email     = '';
$password  = '';
$picture_path = '';

$errors = array();

if (!empty($_POST)) {

    $nick_name = $_POST['nick_name'];
    $email     = $_POST['email'];
    $password  = $_POST['password'];

    // エラーバリデーション
    if ($nick_name == '') {
        $errors['nick_name'] = 'blank';
    }

    if ($email == '') {
        $errors['email'] = 'blank';
    }

    if ($password == '') {
        $errors['password'] = 'blank';
    } elseif (strlen($password) < 4) {
        $errors['password'] = 'length';
    } elseif (strlen($password) > 17) {
        $errors['password'] = 'over'; 
    }  

    if (empty($errors)) {
      $file_name = $_FILES['picture_path']['name'];

      if (!empty($file_name)) {
        $ext = substr($file_name, -3);
        $ext = strtolower($ext);

        if ($ext != 'jpg' && $ext != 'png' && $ext != 'gif') {
            $errors['picture_path'] = 'type';
        }

      } else {
        $errors['picture_path'] = 'blank';
      }
    }

    // メールアドレス重複チェック
    if (empty($errors)) {

        try {
            $sql = 'SELECT COUNT(*) AS `cnt` FROM `members` WHERE `email`=?';
            $data = array($email);
            $stmt = $dbh->prepare($sql);
            $stmt->execute($data);
            $record = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($record['cnt'] > 0) {
              $errors['email'] = 'duplicate';
            }

        } catch (PDOException $e) {
          echo 'SQL文実行時エラー : ' . $e->message();
        }
     } 

    // エラーがなかった場合の処理
    if (empty($errors)) {
      // 画像をアップロード処理
       $picture_name = date('YmdHis') . $file_name;
       move_uploaded_file($_FILES['picture_path']['tmp_name'], '../member_picture/' . $picture_name);

      $_SESSION['join'] = $_POST;
      $_SESSION['join']['picture_path'] = $picture_name;
      
      header('Location: check.php');
      exit();
     }
  }

?>


<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>BookBookBook</title>

    <!-- Bootstrap -->
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
              <a class="navbar-brand" href="index.php" style="font-family: serif;"><span class="strong-title"><i class="fa fa-facebook"></i>BookBookBook</span></a><a href="../php/login.php" class="btn btn-default" style="margin-top: 10px; margin-left: 600px; font-family: serif;""> 会員の方はこちら</a>
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

  <div class="container">
    <div class="row">
      <div class="col-md-6 col-md-offset-3 content-margin-top">
        <legend style="font-family: serif;">新規会員登録</legend>
        <form method="POST" action="" class="form-horizontal" role="form" enctype="multipart/form-data">

          <!-- ニックネーム -->
          <div class="form-group">
            <label class="col-sm-4 control-label" style="font-family: serif;">ニックネーム</label>
            <div class="col-sm-8">
              <input type="text" name="nick_name" class="form-control" value="<?php echo $nick_name; ?>" placeholder="例： 川辺 今人" style="font-family: serif;">
              <?php if(isset($errors['nick_name']) && $errors['nick_name'] == 'blank'): ?>
              <p style="color: red; font-size: 10px; margin-top: 2px;">
                ニックネームを入力してください
              </p>
               <?php endif; ?>
            </div>
          </div>

          <!-- メールアドレス -->
          <div class="form-group">
            <label class="col-sm-4 control-label" style="font-family: serif;">メールアドレス</label>
            <div class="col-sm-8">
              <input type="email" name="email" class="form-control" value="<?php echo $email; ?>" placeholder="例： tyokusoubin@gmail.com" style="font-family: serif;"> 
              <?php if(isset($errors['email']) && $errors['email'] == 'blank'): ?>
              <p style="color: red; font-size: 10px; margin-top: 2px;">
                メールアドレスを入力してください
              </p>
              <?php endif; ?>

              <?php if(isset($errors['email']) && $errors['email'] == 'duplicate'): ?>
                <p style="color: red; font-size: 10px; margin-top: 2px;">
                  指定したメールアドレスは既に登録されています。
                </p>
              <?php endif; ?>
            </div>
          </div>

          <!-- パスワード -->
          <div class="form-group">
            <label class="col-sm-4 control-label" style="font-family: serif;">パスワード</label>
            <div class="col-sm-8">
              <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
              <?php if(isset($errors['password']) && $errors['password'] == 'blank'): ?>
                <p style="color: red; font-size: 10px; margin-top: 2px;">
                  パスワードを入力してください
                </p>
              <?php endif; ?>

              <?php if(isset($errors['password']) && $errors['password'] == 'length'): ?>
                <p style="color: red; font-size: 10px; margin-top: 2px;">
                  パスワードは4文字以上16文字以内で入力してください
                </p>
              <?php endif; ?>

              <?php if(isset($errors['password']) && $errors['password'] == 'over'): ?>
                <p style="color: red; font-size: 10px; margin-top: 2px;">
                  パスワードは4文字以上16文字以内で入力してください
                </p>
              <?php endif ?>
            </div>
          </div>

          <!-- プロフィール写真 -->
          <div class="form-group">
            <label class="col-sm-4 control-label" style="font-family: serif;">プロフィール写真</label>
            <div class="col-sm-8">
              <input type="file" name="picture_path" class="form-control" value="<?php echo $picture_path; ?>" style="font-family: serif;">
              <?php if(isset($errors['picture_path']) && $errors['picture_path'] == 'blank'): ?>
                <p style="color: red; font-size: 10px; margin-top: 2px;">
                 プロフィール画像を選択してください
                </p>
              <?php endif; ?>

              <?php if(isset($errors['picture_path']) && $errors['picture_path'] == 'type'): ?>
                <p style="color: red; font-size: 10px; margin-top: 2px;">
                  プロフィール画像は「.jpg」「.png」「.gif」の画像を選択してください
                </p>
              <?php endif; ?>

              <?php if(!empty($errors)): ?>
                <p style="color: red; font-size: 10px; margin-top: 2px;">
                  再度、プロフィール画像を指定してください
                </p>
              <?php endif; ?>
            </div>
          </div>
          <div style="text-align: center;">
          <input type="submit" class="btn btn-default" value="確認画面へ" style="margin-top: 10px; font-family: serif;"">
          </div>
        </form>
      </div>
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
              <a class="navbar-brand" href="index.php" style="font-family: serif; margin-left: 860px"><span class="strong-title"><i class="fa fa-facebook"></i>BookBookBook</span></a>
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

    <script src="../assets/js/jquery-3.1.1.js"></script>
    <script src="../assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="../assets/js/bootstrap.js"></script>
  </body>
</html>



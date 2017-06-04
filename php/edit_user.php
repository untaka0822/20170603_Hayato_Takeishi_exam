<?php
  session_start();
  require('dbconnect.php');

  if (!isset($_SESSION['login_member_id'])) {
  	header('Location: login.php');
  }

  if (isset($_SESSION['login_member_id']) && $_SESSION['time'] + 3600 > time()) {
    $_SESSION['time'] = time();
    $sql = 'SELECT * FROM `members` WHERE `member_id`=?';
    $data = array($_SESSION['login_member_id']);
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);
    $members = $stmt->fetch(PDO::FETCH_ASSOC);

  } else {
    header('Location: login.php');
    exit();
  }

  $errors = array();

  if (!empty($_POST)) {

    $nick_name = $_POST['nick_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password = sha1($password);

    // ページ内バリデーション
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
      }

      if (empty($errors)) {
      // 画像のバリエーション
      $file_name = $_FILES['picture_path']['name'];
        if (!empty($file_name)) {
         // 画像が選択されていた場合
        $ext = substr($file_name, -3);
        $ext = strtolower($ext);

          if ($ext != 'jpg' && $ext != 'png' && $ext != 'gif') {
            $errors['picture_path'] = 'type';
          }
        } else {
        $errors['picture_path'] = 'blank';
        }
      }

      // メールアドレスの重複チェック
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

      if (empty($errors)) {

      // 画像アップロード処理
      $picture_name = date('YmdHis') . $file_name;
      move_uploaded_file($_FILES['picture_path']['tmp_name'], '../member_picture/' . $picture_name);

      $_SESSION['join'] = $_POST;
      $_SESSION['join']['picture_path'] = $picture_name;
      
      // UPDATE文
      $sql = 'UPDATE `members` SET `nick_name`=?, `email`=?, `password`=?, `picture_path`=? WHERE `member_id`=?';
      $data = array($_POST['nick_name'], $_POST['email'], sha1($_POST['password']), $_SESSION['join']['picture_path'], $_SESSION['login_member_id']);
      $stmt1 = $dbh->prepare($sql);
      $stmt1->execute($data);

      header('Location: top.php');
      exit();

      }
    }


  
?>



<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>ユーザー編集画面</title>
  <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="../assets/css/timeline.css">
  <link rel="stylesheet" type="text/css" href="../assets/css/main.css">
  <link rel="stylesheet" type="text/css" href="../assets/css/form.css">
  <link href="../assets/font-awesome/css/font-awesome.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="../resource/lightbox.css" media="screen,tv" />
  <script type="text/javascript" src="../resource/lightbox_plus.js"></script>
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
              <a class="navbar-brand" href="top.php" style="font-family: serif";><span class="strong-title"><i class="fa fa-facebook"></i> NexSeed Book</span></a>
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
      <div class="col-md-5 col-md-offset-3 content-margin-top">
      
       <legend style="font-family: serif;">ユーザー編集画面</legend>
        <form method="POST" action="" class="form-horizontal" role="form" enctype="multipart/form-data">
          <div class="form-group">
            <label class="col-sm-4 control-label" style="font-family: serif;">ニックネーム</label>
            <div class="col-sm-8">
              <input type="text" name="nick_name" class="form-control" placeholder="<?php echo $members['nick_name']; ?>" style="font-family: serif;">
              <?php if(isset($errors['nick_name']) && $errors['nick_name'] == 'blank'): ?>
              <p style="color: red; font-size: 10px; margin-top: 2px; font-family: serif">
                ニックネームを入力してください
              </p>
               <?php endif; ?>
            </div>
          </div>
          
          <!-- メールアドレス -->
          <div class="form-group">
            <label class="col-sm-4 control-label" style="font-family: serif;">メールアドレス</label>
            <div class="col-sm-8">
              <input type="email" name="email" class="form-control" placeholder="<?php echo $members['email']; ?>" style="font-family: serif;">
              <?php if(isset($errors['email']) && $errors['email'] == 'blank'): ?>
              <p style="color: red; font-size: 10px; margin-top: 2px; font-family: serif">
                メールアドレスを入力してください
              </p>
              <?php endif; ?>

              <?php if(isset($errors['email']) && $errors['email'] == 'duplicate'): ?>
                <p style="color: red; font-size: 10px; margin-top: 2px; font-family: serif">
                  指定したメールアドレスは既に登録されています。
                </p>
              <?php endif; ?>
            </div>
          </div>

          <!-- パスワード -->
          <div class="form-group">
            <label class="col-sm-4 control-label" style="font-family: serif;">パスワード</label>
            <div class="col-sm-8">
              <input type="password" name="password" class="form-control" placeholder="4文字以上で入力してください" style="font-family: serif;">
              <?php if(isset($errors['password']) && $errors['password'] == 'blank'): ?>
                <p style="color: red; font-size: 10px; margin-top: 2px; font-family: serif">
                  パスワードを入力してください
                </p>
              <?php endif; ?>

              <?php if(isset($errors['password']) && $errors['password'] == 'length'): ?>
                <p style="color: red; font-size: 10px; margin-top: 2px; font-family: serif">
                  パスワードは4文字以上で入力してください
                </p>
              <?php endif; ?>
            </div>
          </div>

          <!-- プロフィール写真 -->
          <div class="form-group">
            <label class="col-sm-4 control-label" style="font-family: serif;">プロフィール写真</label>
            <div class="col-sm-7">
                <input type="file" name="picture_path" class="form-control" style="font-family: serif;">
              <div class="preview" /><a href="../member_picture/<?php echo $members['picture_path']; ?>" rel="lightbox"><img src="../member_picture/<?php echo $members['picture_path']; ?>" style="width: 100%; height: 40%; margin-top: 10px;" class="effectable"></a></div>
                <?php if(isset($errors['picture_path']) && $errors['picture_path'] == 'blank'): ?>
                  <p style="color: red; font-size: 10px; margin-top: 2px; font-family: serif">
                   プロフィール画像を選択してください
                  </p>
                <?php endif; ?>

                <?php if(isset($errors['picture_path']) && $errors['picture_path'] == 'type'): ?>
                  <p style="color: red; font-size: 10px; margin-top: 2px; font-family: serif">
                    プロフィール画像は「.jpg」「.png」「.gif」の画像を選択してください
                  </p>
                <?php endif; ?>

                <?php if(!empty($errors)): ?>
                  <p style="color: red; font-size: 10px; margin-top: 2px; font-family: serif">
                    再度、プロフィール画像を指定してください
                  </p>
                <?php endif; ?>
            </div>
          </div>
          <div style="text-align: center;">
            <input type="submit" class="btn btn-warning" name="update" value="更新" style="margin-top: -12px; font-family: serif">
            <a href="top.php" class="btn btn-default" style="margin-top: -12px; font-family: serif">トップへ戻る</a>
          </div>
        </form>
      </div>

      <!-- 下のバー -->
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
              <a class="navbar-brand" href="top.php"><span class="strong-title" style="margin-left: 860px; font-family: serif"><i class="fa fa-facebook"></i> NexSeed Book</span></a>
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
  </div>
  </div>

<script>
  //画像ファイルプレビュー表示
    var $ = document; 
    var $form = $.querySelector('form');

    $.addEventListener('DOMContentLoaded', function() {
        
        $.querySelector('input[type="file"]').addEventListener('change', function(e) {
            var file = e.target.files[0],
                   reader = new FileReader(),
                   $preview =  $.querySelector(".preview"),
                   t = this;
            
            if(file.type.indexOf("image") < 0){
              return false;
            }
            
            reader.onload = (function(file) {
              return function(e) {
                 while ($preview.firstChild) $preview.removeChild($preview.firstChild);

                var img = document.createElement( 'img' );
                img.setAttribute('src',  e.target.result);
                img.setAttribute('width', '150px');
                img.setAttribute('title',  file.name);

                $preview.appendChild(img);
              }; 
            })(file);
            reader.readAsDataURL(file);
        }); 
    });

</script>
</body>
</html>
<?php
session_start();
require('dbconnect.php');

if (isset($_SESSION['login_member_id']) && $_SESSION['time'] + 3600 > time()) {
  $_SESSION['time'] = time();
  $sql = 'SELECT * FROM `members` WHERE `member_id`=?';
  $data = array($_SESSION['login_member_id']);
  $stmt1 = $dbh->prepare($sql);
  $stmt1->execute($data); 
  $members = $stmt1->fetch(PDO::FETCH_ASSOC);

  } else {
    header('Location: login.php');
    exit();
  }

  $sql = 'SELECT * FROM `books` WHERE `user_id`=?';
  $data = array($_SESSION['login_member_id']);
  $stmt = $dbh->prepare($sql);
  $stmt->execute($data);
  $books = $stmt->fetch(PDO::FETCH_ASSOC);

  $errors = array();

  if (!empty($_POST)) {

    $title = $_POST['title'];
    $reasons = $_POST['reasons'];

      if ($title == '') {
          $errors['title'] = 'blank';
      }

      if ($reasons == '') {
          $errors['reasons'] = 'blank';
      }

      if (empty($errors)) {
      
       $file_name = $_FILES['book_picture']['name'];
    
        if (!empty($file_name)) {
          $ext = substr($file_name, -3);
          $ext = strtolower($ext);

        if ($ext != 'jpg' && $ext != 'png' && $ext != 'gif') {
          $errors['book_picture'] = 'type';
        }

        } else {
        $errors['book_picture'] = 'blank';

        }
      }

      if (empty($errors)) {

      // 画像アップロード処理
      $picture_name = date('YmdHis') . $file_name;
      move_uploaded_file($_FILES['book_picture']['tmp_name'], '../book_picture/' . $picture_name);

      $_SESSION['join'] = $_POST;
      $_SESSION['join']['book_picture'] = $picture_name;

      $sql = 'UPDATE `books` SET `title`=?, `reasons`=?, `book_picture`=?, `created`=NOW() WHERE `book_id`=?';
      $data = array($_SESSION['join']['title'], $_SESSION['join']['reasons'], $_SESSION['join']['book_picture'], $_SESSION['join']['book_id']);
      $stmt2 = $dbh->prepare($sql);
      $stmt2->execute($data);

      header('Location: top.php');
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

    <title>NexSeed Book</title>

    <!-- Bootstrap -->
    <link href="../assets/css/bootstrap.css" rel="stylesheet">
    <link href="../assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="../assets/css/form.css" rel="stylesheet">
    <link href="../assets/css/timeline.css" rel="stylesheet">
    <link href="../assets/css/main.css" rel="stylesheet">
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
              <a class="navbar-brand" href="top.php" style="font-family: serif;"><span class="strong-title"><i class="fa fa-linux"></i> NexSeed Book</span></a>
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
      <div class="col-md-4 col-md-offset-4 content-margin-top">
        <form method="POST" action="" class="form-horizontal" role="form" enctype="multipart/form-data">
          <h3 style="font-family: serif;">本の編集</h3>
          <div class="msg" style="font-family: serif;">
            本のタイトル : <input type="text" name="title" value="<?php echo $_SESSION['join']['title']; ?>">
            <?php if(isset($errors['title']) && $errors['title'] == 'blank'): ?>
              <p style="color: red; font-size: 10px; margin-top: 2px;">
                タイトルを入力してください
              </p>
            <?php endif; ?>
             <p>
             おすすめな理由 : <br>
              <textarea name="reasons" cols="100" rows="4" class="form-control"><?php echo $_SESSION['join']['reasons']; ?></textarea>
              <?php if(isset($errors['reasons']) && $errors['reasons'] == 'blank'): ?>
              <p style="color: red; font-size: 10px; margin-top: 2px;">
                おすすめな理由を入力してください
              </p>
               <?php endif; ?>
            </p>
            <p class="day">
              <?php echo $_SESSION['join']['created']; ?>
              <input type="hidden" name="created" value="<?php echo $_SESSION['join']['created']; ?>">
            </p>
            <input type="file" name="book_picture" class="form-control">
              <div class="preview" /><a href="../book_picture/<?php echo $_SESSION['join']['book_picture']; ?>" rel="lightbox"><img src="../book_picture/<?php echo $_SESSION['join']['book_picture']; ?>" style="width: 60%; height: 24%; margin-top: 10px;" class="effectable"></a>
                <?php if(isset($errors['book_picture']) && $errors['book_picture'] == 'blank'): ?>
                  <p style="color: red; font-size: 10px; margin-top: 2px;">
                  本の画像を選択してください
                  </p>
                <?php endif; ?>

                <?php if(isset($errors['book_picture']) && $errors['book_picture'] == 'type'): ?>
                  <p style="color: red; font-size: 10px; margin-top: 2px;">
                   本の画像は「.jpg」「.png」「.gif」の画像を選択してください
                  </p>
                <?php endif; ?>

                <?php if(!empty($errors)): ?>
                  <p style="color: red; font-size: 10px; margin-top: 2px;">
                    再度、本の画像を指定してください
                  </p>  
                <?php endif; ?>
              </div>
          </div> 
          <input type="hidden" name="book_id" value="<?php echo $_SESSION['join']['book_id']; ?>"> 
            <input type="submit" name="update" value="更新" class="btn btn-warning" style="text-align: center; margin-top: 10px;font-family: serif;"">
            <a href="top.php" class="btn btn-default" style="margin-top: 10px; font-family: serif;">一覧へ戻る</a>
        </form>
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
              <a class="navbar-brand" href="top.php"><span class="strong-title" style="text-align: right; font-family: serif; padding-left: 860px;"><i class="fa fa-linux"></i> NexSeed Book</span></a>
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

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="assets/js/jquery-3.1.1.js"></script>
    <script src="assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script>

        // 画像プレビュー
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

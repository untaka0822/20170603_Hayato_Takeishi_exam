<?php

echo '<br>';
echo '<br>';
echo '<br>';
echo '<br>';
echo '<br>';


session_start();
require('dbconnect.php');

if (!isset($_SESSION['login_member_id'])) {
    header('Location: login.php');
    exit();
}

// 選択したリスト一件取得
$sql = 'SELECT * FROM `members`  WHERE `member_id`=?';
$data = array($_SESSION['login_member_id']);
$stmt = $dbh->prepare($sql);
$stmt->execute($data);
$detail = $stmt->fetch(PDO::FETCH_ASSOC);

// ボタンが押されたとき
if (!empty($_POST)) {
  
  $_SESSION['join'] = $_POST;
  header('Location: edit_user.php');
  exit();
}

?>

<br>
<br>
<br>

<!DOCTYPE html>
<html lang="ja">
<head>

  <meta charset="utf-8">
  <title>NexSeed Book</title>
  <link rel="stylesheet" type="text/css" href="../assets/css/all.css">
  <link rel="stylesheet" type="text/css" href="../assets/css/timeline.css">
  <link rel="stylesheet" type="text/css" href="../assets/css/main.css">
  <link rel="stylesheet" type="text/css" href="../assets/css/form.css">
  <link rel="stylesheet" type="text/css" href="../assets/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" type="text/css" href="../assets/css/detail_book.css">
  <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="../resource/lightbox.css" media="screen,tv" />
  <script type="text/javascript" src="../resource/lightbox_plus.js"></script>

</head>
<body style="background-image: url(../book_picture/book_background10.jpg);"> 
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
              <a class="navbar-brand" href="top.php" style="font-family: serif;"><span class="strong-title"><i class="fa fa-facebook"></i> NexSeed Book</span></a>
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

  <div id="all-box">
    <div id="a-box">
      <a href="../member_picture/<?php echo $detail['picture_path']; ?>" rel="lightbox"><img src="../member_picture/<?php echo $detail['picture_path']; ?>" style="width: 30%; height: 45%; text-align: center; margin-left: 60px" class="effectable"></a>

        <div class="individual" style="float: left; margin-left: 200px; border-bottom: 1px solid #e5e5e5; border-radius: 5px; background-color: white">
        <h2 style="border-bottom: 1px solid #e5e5e5; margin-bottom: 20px; margin-left: 10px; font-family: serif;">ユーザー情報</h2>  
          <h4 style="margin: 14px; font-family: serif;">ユーザー名 : <?php echo $detail['nick_name']; ?></h4>

          <h4 style="margin: 14px; font-family: serif;">メールアドレス : <?php echo $detail['email']; ?></h4>

          <h4 style="margin: 14px; font-family: serif;">アカウント作成日 : <?php echo $detail['created']; ?></h4>

          <form name="form3" method="POST" action="" style="margin-left: 80px;">
              <a href="top.php" class="btn btn-default" style="margin-bottom: 10px; font-family: serif;">トップに戻る</a> 
              <input class="btn btn-success" type="submit" value="編集する" style="margin-bottom: 10px; font-family: serif;">
              <input type="hidden" name="member_id" value="<?php echo $detail['member_id']; ?>">
              <input type="hidden" name="nick_name" value="<?php echo $detail['nick_name']; ?>">
              <input type="hidden" name="email" value="<?php echo $detail['email']; ?>">
              <input type="hidden" name="picture_path" value="<?php echo $detail['picture_path']; ?>">
              <input type="hidden" name="created" value="<?php echo $detail['created']; ?>">
          </form>
        </div>
    </div>

     <div id="d-box">
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
              <a class="navbar-brand" href="top.php" style="font-family: serif; padding-left: 860px;"><span class="strong-title"><i class="fa fa-facebook"></i> NexSeed Book</span></a>
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

<?php

echo '<br>';
echo '<br>';
echo '<br>';
echo '<br>';
echo '<br>';


session_start();
require('dbconnect.php');

if (!isset($_REQUEST['book_id'])) {
    header('Location: search.php');
    exit();
}

// 選択したリスト一件取得
$sql = 'SELECT * FROM `books`  WHERE `book_id`=?';
$data = array($_REQUEST['book_id']);
$stmt = $dbh->prepare($sql);
$stmt->execute($data);
$detail = $stmt->fetch(PDO::FETCH_ASSOC);

// ボタンが押されたとき
if (!empty($_POST)) {
  
  $_SESSION['join'] = $_POST;
  header('Location: edit_book.php');
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
<body style="background-image: url(../book_picture/book_background9.jpg);">
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
      <a href="../book_picture/<?php echo $detail['book_picture']; ?>" rel="lightbox"><img src="../book_picture/<?php echo $detail['book_picture']; ?>" style="width: 24%; height: 55%; margin-left: 40px; border-radius: 5px;" class="effectable"></a>

        <div class="individual" style="float: left; margin-left: 200px; border-bottom: 1px solid #e5e5e5; background-color: white; border-radius: 5px; padding: 10px">
        <h2 style="border-bottom: 1px solid #e5e5e5; margin-bottom: 20px; font-family: serif;">本の情報</h2>  
          <h4 style="margin: 14px; font-family: serif;">タイトル : <?php echo $detail['title']; ?></h4>

          <h4 style="margin: 14px; font-family: serif;">おすすめな理由 : <?php echo $detail['reasons']; ?></h4>

          <h4 style="margin: 14px; font-family: serif;">作成日 : <?php echo $detail['created']; ?></h4>

          <form name="form3" method="POST" action="" style="center; margin-left: 60px">
              <a href="top.php" class="btn btn-default" style="margin-bottom: 10px; font-family: serif;">トップに戻る</a> 
              <input class="btn btn-success" type="submit" value="編集する" style="margin-bottom: 10px; font-family: serif;">
              <input type="hidden" name="book_id" value="<?php echo $detail['book_id']; ?>">
              <input type="hidden" name="title" value="<?php echo $detail['title']; ?>">
              <input type="hidden" name="reasons" value="<?php echo $detail['reasons']; ?>">
              <input type="hidden" name="book_picture" value="<?php echo $detail['book_picture']; ?>">
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
              <a class="navbar-brand" href="top.php" style="font-family: serif; padding-left: 900px;"><span class="strong-title"><i class="fa fa-facebook"></i> NexSeed book</span></a>
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

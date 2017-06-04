<?php
session_start();
require('dbconnect.php');

if (!isset($_SESSION['join'])) { 
    header('Location: login.php');
    exit();
}

// ログイン判定
if (isset($_SESSION['login_member_id']) && $_SESSION['time']+ 3600 > time()) {
    $_SESSION['time'] = time();
    $sql = 'SELECT * FROM `members` WHERE `member_id`=? ';
    $data = array($_SESSION['login_member_id']);
    $stmt1 = $dbh->prepare($sql);
    $stmt1->execute($data);
    $login_member = $stmt1->fetch(PDO::FETCH_ASSOC);

  } else {
    header('Location: login.php');
    exit();
}

// 完了ボタンが押された時
if (!empty($_POST)) {
    $title = $_SESSION['join']['title'];
    $reasons = $_SESSION['join']['reasons'];
    $book_picture = $_SESSION['join']['book_picture'];

  try { 
        // DBへの登録処理
        $sql = 'INSERT INTO `books` SET `book_id`=?, `user_id`=?, `title`=?, `reasons`=?, `book_picture`=?, `created`=NOW()';
        $data = array($book_id, $login_member['member_id'], $title, $reasons, $book_picture);
        $stmt2 = $dbh->prepare($sql);
        $stmt2->execute($data);

        // unset = SESSIONの情報を削除
        unset($_SESSION['join']);
        
        // top.phpへ遷移される
        header('Location: top.php');
        exit();
        // エラー時に表示
      } catch(PDOException $e){
              // 例外が発生した場合の処理
        echo 'SQL文実行時のエラー: ' . $e->getMessage();
        exit();
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
<body style="background-image: url(../book_picture/book_background8.jpg);">
  <nav class="navbar navbar-default navbar-fixed-top" style="background-color: rgba(0, 0, 0, 0.66);">
      <div class="container">
          <!-- Brand and toggle get grouped for better mobile display -->
          <div class="navbar-header page-scroll">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="top.php" style="font-family: serif;"><span class="strong-title" style="color: white"><i class="fa fa-facebook"></i> BookBookBook</span></a>
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

 <div class="container" style="text-align: center; margin-top: 10px">
  <div class="row">
    <div style="font-size: 20px; font-family: serif;">
      <p> 本のタイトル : <?php echo $_SESSION['join']['title']; ?></p>
    </div>
    <div style="font-size: 20px; font-family: serif;">
      <p> おすすめな理由 : <?php echo $_SESSION['join']['reasons']; ?></p>
    </div>
    <div style="font-size: 20px; font-family: serif;">
      本の画像 <br>
       <img src="../book_picture/<?php echo $_SESSION['join']['book_picture']; ?>" style="width: 24%; height: 32%; border-radius: 5px;">
    </div>
    <br>
    <form method="POST" action="">
      <a href="new_book.php" class="btn btn-default" style="font-family: serif;">戻る</a>
      <input type="hidden" name="hoge" value="fuga" style="font-family: serif;"> <!-- 値を表示せずにDBに保存するときはhidden -->
      <input type="submit" value="完了" class="btn btn-warning" style="font-family: serif;">
    </form>
  </div>
  
  <nav class="navbar navbar-default navbar-fixed-bottom" style="background-color: rgba(0, 0, 0, 0.66);">
      <div class="container">
          <!-- Brand and toggle get grouped for better mobile display -->
          <div class="navbar-header page-scroll">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="top.php" style="font-family: serif; padding-left: 850px;"><span class="strong-title" style="color: white"><i class="fa fa-facebook"></i> BookBookBook</span></a>
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
</body>
</html>

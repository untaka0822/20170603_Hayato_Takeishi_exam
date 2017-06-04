  <?php
	session_start();
  require('dbconnect.php');

  if (!isset($_SESSION['login_member_id'])) {
    header('Location: login.php');
    exit();
  }

  $sql = 'SELECT * FROM `members` WHERE `member_id`=?';
  $data = array($_SESSION['login_member_id']);
  $stmt = $dbh->prepare($sql);
  $stmt->execute($data);
  $login_member = $stmt->fetch(PDO::FETCH_ASSOC);

  // 退会するボタンが押された時
  if (!empty($_POST)) {

    $sql = 'DELETE FROM `members` WHERE `member_id`=?';
    $data = array($_SESSION['login_member_id']);
    $stmt1 = $dbh->prepare($sql);
    $stmt1->execute($data);

    $sql = 'DELETE FROM `books` WHERE `user_id`=?';
    $data = array($_SESSION['login_member_id']);
    $stmt2 = $dbh->prepare($sql);
    $stmt2->execute($data);

    $sql = 'DELETE FROM `likes` WHERE `member_id`=?';  
    $data = array($_SESSION['login_member_id']);
    $like_stmt = $dbh->prepare($sql);
    $like_stmt->execute($data);

    session_destroy(); // $_SESSIONの情報を削除
      
    // COOKIE情報も削除
    setcookie('email', '', time() - 3000);
    setcookie('password', '', time() - 3000);

    header('Location: thanks_leave.php');
    exit();


  }
?>

<!DOCTYPE html>
<html lang="ja">
<head>

  <meta charset="utf-8">
  <title>退会画面</title>
    <link href="../assets/css/bootstrap.css" rel="stylesheet">
    <link href="../assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="../assets/css/form.css" rel="stylesheet">
    <link href="../assets/css/timeline.css" rel="stylesheet">
    <link href="../assets/css/main.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../css/leave.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/all.css">

</head>
<body>
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
              <a class="navbar-brand" href="top.php" style="font-family: serif"><span class="strong-title" style="color: white"><i class="fa fa-facebook"></i> BookBookBook</span></a>
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
    <div id="a-box" style="text-align: center;">
      <img src="../member_picture/<?php echo $login_member['picture_path']; ?>" style="width: 27%; height: 36%; margin-top: 70px;">
    </div>
    <div id="b-box" style="text-align: center">
      <p style="font-family: serif">会員番号 : <?php echo $login_member['member_id']; ?></p>
      <p style="font-family: serif">会員名 : <?php echo $login_member['nick_name']; ?>様</p>
      <p style="font-family: serif">メールアドレス : <?php echo $login_member['email']; ?></p>
      <form method="POST" action="" onsubmit="return submitChk()">
        <input type="hidden" name="member_id" value="<?php echo $_SESSION['login_member_id']; ?>">
        <input type="hidden" name="like_book_id" value="<?php echo $like_book_id; ?>">
        <input type="submit" value="退会する" class="btn btn-default" style="font-family: serif"> 
        <a href="top.php" class="btn btn-info" style="font-family: serif">トップへ戻る</a>
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
              <a class="navbar-brand" href="top.php" style="font-family: serif; padding-left: 860px"><span class="strong-title" style="color: white"><i class="fa fa-facebook"></i> BookBookBook</span></a>
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
  <script>
    // 削除アラート
    function submitChk () {
        var flag = confirm ( "本当に退会しますか?\n\n退会されない方はキャンセルボタンを押してください");
        return flag;
    }

 </script>
</body>
</html>
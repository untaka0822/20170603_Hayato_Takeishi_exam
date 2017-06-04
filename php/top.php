<?php
	session_start();
  require('dbconnect.php');

  if (!isset($_SESSION['login_member_id'])) {
      header('Location: login.php');
      exit();
  }

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

  // ページング機能
  $page = '';

  if (isset($_REQUEST['page'])) {
      $page = $_REQUEST['page'];
  }

  if ($page == '') {
      $page = 1;
  }

  $page = max($page, 1);

  // 本の件数をカウントし最大ページ数を出す
  $sql = 'SELECT COUNT(*) AS `cnt` FROM `books`';
  $data = array();
  $book_stmt = $dbh->prepare($sql);
  $book_stmt->execute();
  $books = $book_stmt->fetch(PDO::FETCH_ASSOC);
  $max_page = ceil($books['cnt'] / 5);
  
  $page = min($page, $max_page);
  $page = max($page, 1);

  $page = ceil($page);

  $start = ($page - 1) * 5;

  // 検索
  $search_word = '';

  if (isset($_GET['search_word']) && !empty($_GET['search_word'])) {
    // 検索の場合の処理
    $search_word = $_GET['search_word'];

    $sql = sprintf('SELECT b.*, m.nick_name, m.picture_path FROM `books` b LEFT JOIN `members` m ON b.user_id=m.member_id WHERE b.title LIKE "%%%s%%" ORDER BY b.created DESC LIMIT %d, 5', $_GET['search_word'], $start);
  } else {
    // 通常の処理
    $sql = sprintf('SELECT b.*, m.nick_name, m.picture_path FROM `books` b LEFT JOIN `members` m ON b.user_id=m.member_id ORDER BY b.created DESC LIMIT %d, 5', $start);
  }

  $stmt2 = $dbh->prepare($sql);
  $stmt2->execute();

  // いいね機能
  if (!empty($_POST && $_POST['submit-type'] == 'like')) {
    if ($_POST['like'] == 'like') {
      // いいね！された時の処理
      $sql = 'INSERT INTO `likes` SET `member_id`=?, `book_id`=?';
      $data = array($_SESSION['login_member_id'], $_POST['like_book_id']);
      $like_stmt = $dbh->prepare($sql);
      $like_stmt->execute($data);

    } else {
      //いいね！取り消しされた時の処理
      $sql = 'DELETE FROM `likes` WHERE `member_id`=? AND `book_id`=?';
      $data = array($_SESSION['login_member_id'], $_POST['like_book_id']);
      $like_stmt = $dbh->prepare($sql);
      $like_stmt->execute($data);
    }
  }

  // 削除
  if (!empty($_POST) && $_POST['submit-type'] == 'delete') {
    $sql = 'DELETE FROM `books` WHERE `book_id`=?';
    $data = array($_REQUEST['book_id']);
    $delete_stmt = $dbh->prepare($sql);
    $delete_stmt->execute($data);
  }

  // 編集
  if (!empty($_POST) && $_POST['submit-type'] == 'edit') {
      $_SESSION['join'] = $_POST;
      header('Location: edit_book.php');
      exit();
  }
?>


<!DOCTYPE html>
<html lang="ja" style=>
<head>
  <meta charset="utf-8">
  <title>NexSeed Book</title>

  <link href="../assets/css/bootstrap.css" rel="stylesheet">
  <link href="../assets/font-awesome/css/font-awesome.css" rel="stylesheet">
  <link href="../assets/css/form.css" rel="stylesheet">
  <link href="../assets/css/timeline.css" rel="stylesheet">
  <link href="../assets/css/main.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="../assets/css/top.css">
  <link rel="stylesheet" type="text/css" href="../resource/lightbox.css" media="screen,tv" />
  <script type="text/javascript" src="../resource/lightbox_plus.js"></script>
  
</head>

<script language="JavaScript">
    i = 0;
    url = "../book_picture/";

    img = new Array("../book_picture/book_background.jpg","../book_picture/book_background1.jpg","../book_picture/book_background2.jpg","../book_picture/book_background3.png","../book_picture/book_background4.jpg", "../book_picture/book_background5.jpg", "../book_picture/book_background6.jpeg", "../book_picture/book_background7.jpg", "../book_picture/book_background8.jpg", "../book_picture/book_background9.jpg", "../book_picture/book_background10.jpg", "../book_picture/book_background11.jpg", "../book_picture/book_background12.jpg", "../book_picture/book_background13.jpg");

    function change(){
        i++;
        if(i >= img.length) {
            i = 0;
        }
        document.body.background = url + img[i];
    }
    function tm(){
        document.body.background = url + img[i];
        tm = setInterval("change()",10000);
    }
    </script>

<body onload="tm()">
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
              <a class="navbar-brand" href="top.php" style="font-family: serif;"><span class="strong-title"><i class="fa fa-facebook"></i> NexSeed Book</span></a><a class="btn btn-danger" href="logout.php" style="margin-top: 10px; margin-left: 700px">ログアウト</a>
          </div>
          <!-- Collect the nav links, forms, and other content for toggling -->
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="nav navbar-nav navbar-right">
                <a href="leave.php" class="btn btn-default" style="margin-top: 10px;">退会する</a>
              </ul>
          </div>
          <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
  </nav>
<div id="site-box">
  <div id="a-box"></div>
  <div id="b-box">
    <div class="content1">
      <div class="book">
        <form method="POST" action="" name="title" style="margin-left: 20px;">
          <select>
            <option>本のタイトル</option>
            <option>おすすめ理由</option>
            <option>ユーザー名</option>
          </select>
        </form> 
        <form method="GET" action="" class="form-horizontal" role="form" style="margin-left: 20px;">
          <input type="text" name="search_word" value="<?php echo $search_word; ?>">
          <input type="submit" value="検索" class="btn btn-success btn-xs">
        </form>
        <?php while($books = $stmt2->fetch(PDO::FETCH_ASSOC)): ?>        
            <div class="books">

                  <a href="detail_book.php?book_id=<?php echo $books['book_id']; ?>" style="font-size: 25px;"><?php echo $books['title']; ?></a><br>
                  <a href="../member_picture/<?php echo $books['picture_path']; ?>" rel="lightbox"><img src="../member_picture/<?php echo $books['picture_path']; ?>" class="effectable" style="width: 30%; height: 32%; border-radius: 100px; margin-left: 20px"></a>
                  <a href="../book_picture/<?php echo $books['book_picture']; ?>" rel="lightbox"><img src="../book_picture/<?php echo $books['book_picture']; ?>" class="effectable" style="width: 30%; border-radius: 5px; margin-left: 70px; margin-top: 20px"></a>
                  <p style="border-bottom: 1px solidx #e5e5e5; margin-top: 10px">ユーザー名 : <?php echo $books['nick_name']; ?></p>
                  <p class="date" style="font-size: 14px; margin-bottom: 5px; border-bottom: 1px solid #e5e5e5"><?php echo $books['created']; ?></p>

                  <!-- ログインしているユーザーの本だけ削除と編集を表示 -->
                  <?php if ($members['member_id'] == $books['user_id']): ?>
                    <form name="form2" method="POST" action="" onsubmit="return submitChk()">
                      <input class="btn-xs btn-info" type="submit" name="delete" value="削除" style="margin-bottom: 10px;">
                      <input type="hidden" name="book_id" value="<?php echo $books['book_id']; ?>">
                      <input type="hidden" name="submit-type" value="delete">
                    </form>

                    <form name="form3" method="POST" action="">
                      <input class="btn-xs btn-success" type="submit" name="edit" value="編集" style="margin-bottom: 10px;">
                      <input type="hidden" name="submit-type" value="edit">
                      <input type="hidden" name="book_id" value="<?php echo $books['book_id']; ?>">
                      <input type="hidden" name="title" value="<?php echo $books['title']; ?>">
                      <input type="hidden" name="reasons" value="<?php echo $books['reasons']; ?>">
                      <input type="hidden" name="book_picture" value="<?php echo $books['book_picture']; ?>">
                      <input type="hidden" name="created" value="<?php echo $books['created']; ?>">
                    </form>
                  <?php endif; ?>

                  <?php
                    //いいね！をしているかの判定処理
                    $sql = 'SELECT * FROM `likes` WHERE `member_id`=? AND `book_id`=?';
                    $data = array($_SESSION['login_member_id'], $books['book_id']);
                    $is_like_stmt = $dbh->prepare($sql);
                    $is_like_stmt->execute($data);

                    // いいね！数カウント処理
                    $sql = 'SELECT COUNT(*) AS total FROM `likes` WHERE `book_id`=?';
                    $data = array($books['book_id']);
                    $count_stmt = $dbh->prepare($sql);
                    $count_stmt->execute($data);
                    $count = $count_stmt->fetch(PDO::FETCH_ASSOC);

                  ?>
                  <form name="form1" method="POST" action="">
                    いいね！数 : <?php echo $count['total']; ?> 
                    <?php if($is_like_stmt = $is_like_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                      <!-- いいね！データが存在する(いいね！取り消しボタン表示) -->
                      <input type="hidden" name="like" value="unlike">
                      <input type="hidden" name="like_book_id" value="<?php echo $books['book_id']; ?>">
                      <input type="hidden" name="submit-type" value="like">
                      <input type="submit" value="いいね！取り消し" class="btn-xs btn-danger">
                    <?php else: ?>
                      <!-- いいね！データが存在しない(いいね！ボタン表示)  -->
                      <input type="hidden" name="like" value="like">
                      <input type="hidden" name="like_book_id" value="<?php echo $books['book_id']; ?>">
                      <input type="hidden" name="submit-type" value="like">
                      <input type="submit" value="いいね！" class="btn-xs btn-primary">
                    <?php endif; ?>
                  </form>
            </div>
            <?php endwhile; ?>

            <ul class="paging">
            <?php
              $word = '';
              if (isset($_GET['search_word'])) {
                  $word = '&search_word=' . $_GET['search_word'];
              }
            ?>
              <div class="col-xs-6 col-lg-offset-3" style="padding-bottom: 50px;"> 
              <p style="color: white;"><?php echo $page . 'ページ' . ' / ' . $max_page . 'ページ'; ?></p>
                <?php if($page > 1): ?>
                    <a href="top.php?page=<?php echo $page - 1; ?><?php echo $word; ?>" class="btn btn-warning" style="">前</a>
                <?php else: ?>
                    <a href="" class="btn btn-default">前</a>
                <?php endif; ?>

                &nbsp;&nbsp;|&nbsp;&nbsp;
                <?php if($page < $max_page): ?>
                    <a href="top.php?page=<?php echo $page + 1; ?><?php echo $word; ?>" class="btn btn-warning">次</a>
                <?php else: ?>
                    <a href="" class="btn btn-default">次</a>
                <?php endif; ?>
              </div>
            </ul>
      </div>
    </div>
  </div>
  
  <div id="c-box">
    <div class="content2">
    <a href="../member_picture/<?php echo $members['picture_path']; ?>" rel="lightbox"><img src="../member_picture/<?php echo $members['picture_path']; ?>" style="width: 100%; height: 72%; border-radius: 5px" class="effectable"></a>
      <?php
          date_default_timezone_set('Asia/Tokyo');
          $time = intval(date('H'));
          if (6 <= $time && $time <= 11) { ?>
          <p style="font-size: 20px; margin-top: 25px; text-align: center; background-color: white; color: black; border-radius: 15px;">Good morning!</p>
          <?php } elseif (11 <= $time && $time <= 17) { ?>
          <p style="font-size: 20px; margin-top: 25px; text-align: center; background-color: white; color: black; border-radius: 15px;">Hello!</p>
          <?php } else { ?>
          <p style="font-size: 20px; margin-top: 25px; text-align: center; background-color: white; color: black; border-radius: 15px;"> Welcome Home!</p>
      <?php } ?>
    <a href="detail_user.php?member_id=<?php echo $members['member_id']; ?>"><p style="font-size: 20px; margin-top: 25px; text-align: center; background-color: white; color: black; border-radius: 15px;"><?php echo $members['nick_name']; ?>さん</p></a>
      
    </div>
    <a href="new_book.php" class="btn btn-warning" style="margin-right: 10px">本を新しく追加する</a>
    <a href="edit_user.php" class="btn btn-success" style="margin-top: 10px; margin-bottom: 10px; margin-right: 10px">ユーザー情報を編集する</a>
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
              <a class="navbar-brand" href="top.php" style="font-family: serif; padding-left: 860px"><span class="strong-title"><i class="fa fa-facebook"></i> NexSeed Book</span></a>
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
  <script>
  // 確認ダイアログ表示
    function submitChk () {
        var flag = confirm ( "本当に削除してもいいですか?\n\n削除されない方はキャンセルボタンを押してください");
        return flag;
    }

 </script>
</body>
</html>




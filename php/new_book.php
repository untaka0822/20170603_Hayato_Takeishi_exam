<?php
session_start();
require('dbconnect.php');

$title = '';
$category_id = '';
$reasons = '';
$book_picture = '';

$errors = array();

// 本のカテゴリー
$sql = 'SELECT * FROM `categories`';
$stmt = $dbh->prepare($sql);
$stmt->execute();

// 確認画面ボタンが押された時
if (!empty($_POST)) {

    $title = $_POST['title'];
    $reasons = $_POST['reasons'];
    $category_id = $_POST['category_id'];

    // ページ内バリデーション
    if ($title == '') {
        $errors['title'] = 'blank';
    }

    if ($reasons == '') {
        $errors['reasons'] = 'blank';
    }

    if (empty($errors)) {

      // 画像のバリデーション
      $file_name = $_FILES['book_picture']['name'];
      // name部分は固定、book_picture部分はinputタグのtype="file"のname部分
      if (!empty($file_name)) {
         // 画像が選択されていた場合
        $ext = substr($file_name, -3);
        $ext = strtolower($ext);
        if ($ext != 'jpg' && $ext != 'png' && $ext != 'gif') {
            $errors['book_picture'] = 'type';
        }
      } else {
        $errors['book_picture'] = 'blank';
      }
    }

    // エラーがなかった場合の処理
    if (empty($errors)) {
      // 画像をアップロード処理
       $picture_name = date('YmdHis') . $file_name;
       move_uploaded_file($_FILES['book_picture']['tmp_name'], '../book_picture/' . $picture_name);

      $_SESSION['join'] = $_POST;
      $_SESSION['join']['book_picture'] = $picture_name;
      

      var_dump($category_id);
      // header('Location: check_book.php');
      // exit();
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

  <div class="container">
    <div class="row">
      <div class="col-md-6 col-md-offset-3 content-margin-top">
        <form method="POST" action="" class="form-horizontal" role="form" enctype="multipart/form-data">
        <legend style="font-family: serif;">新しい本の登録</legend>
          <div class="form-group">
            <label class="col-sm-4 control-label" style="font-family: serif;">本のタイトル</label>
            <div class="col-sm-8">
              <input type="text" name="title" class="form-control" value="<?php echo $title; ?>" placeholder="本のタイトルを入力してください" style="font-family: serif;">
              <?php if(isset($errors['title']) && $errors['title'] == 'blank'): ?>
              <p style="color: red; font-size: 10px; margin-top: 2px;">
                本のタイトルを入力してください
              </p>
               <?php endif; ?>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-4 control-label" style="font-family: serif;;">本のカテゴリー</label>
            <div class="col-sm-8">
            <select style="width: 120px; text-align: center">
              <?php while($categorys = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                  <option name="category_id"><?php echo $categorys['name']; ?></option>
              <?php endwhile; ?>
            </select>
              <input type="hidden" name="category_id" class="form-control" value="<?php echo $categorys['category_id']; ?>" style="font-family: serif;">
            </div>
          </div>


          
          <div class="form-group">
            <label class="col-sm-4 control-label" style="font-family: serif;">おすすめな理由</label>
            <div class="col-sm-8">
              <textarea type="reasons" cols="100" rows="4" name="reasons" class="form-control" value="<?php echo $reasons; ?>" placeholder="オススメの理由" style="font-family: serif;"></textarea> 
              <?php if(isset($errors['reasons']) && $errors['reasons'] == 'blank'): ?>
              <p style="color: red; font-size: 10px; margin-top: 2px; font-family: serif;"">
                おすすめな理由を入力してください
              </p>
              <?php endif; ?>
            </div>
          </div>
          
          <div class="form-group">
            <label class="col-sm-4 control-label" style="font-family: serif;">新しい本の画像</label>
            <div class="col-sm-8">
              <input type="file" name="book_picture" class="form-control" value="<?php echo $book_picture; ?>" style="font-family: serif;">
              <?php if(isset($errors['book_picture']) && $errors['book_picture'] == 'blank'): ?>
                <p style="color: red; font-size: 10px; margin-top: 2px; font-family: serif;">
                 本の画像を選択してください
                </p>
              <?php endif; ?>

              <?php if(isset($errors['book_picture']) && $errors['book_picture'] == 'type'): ?>
                <p style="color: red; font-size: 10px; margin-top: 2px; font-family: serif;">
                  本の画像は「.jpg」「.png」「.gif」の画像を選択してください
                </p>
              <?php endif; ?>

              <?php if(!empty($errors)): ?>
                <p style="color: red; font-size: 10px; margin-top: 2px; font-family: serif;">
                  再度、本の画像を指定してください
                </p>
              <?php endif; ?>
            </div>
          </div>
          <div class="row" style="text-align: center;">
          <a class="btn btn-info" href="top.php" style="margin-right: 20px; font-family: serif;">一覧へ戻る</a>
          <input type="submit" class="btn btn-success" value="確認画面へ" style="font-family: serif;">
          </div>
        </form>
      </div>
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

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="../assets/js/jquery-3.1.1.js"></script>
    <script src="../assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="../assets/js/bootstrap.js"></script>
  </body>
</html>



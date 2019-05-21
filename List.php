<?php
include 'tdData.php';
include 'selectListType.php';

// DBへの接続情報 
define("_DB_HOST_", "localhost");
define("_DB_USER_", "DBユーザ名");
define("_DB_PASS_", "DBユーザのパスワード");
define("_DB_NAME_", "DB名");

function Zabbix_List($listType, $listWhere) {
  $r        = selectListType($listType, $listWhere);
  $viewName = $r['viewName'];
  $columns  = $r['columns'];
  $tbFormat = $r['tbFormat'];
  $sql      = 'SELECT * FROM `' . $viewName . '` WHERE ' . $listWhere;

  try {
    // データベースに接続
    $pdo = new PDO(
      'mysql:dbname=' . _DB_NAME_ . ';host=' . _DB_HOST_ . ';charset=utf8',
      _DB_USER_, _DB_PASS_,
      [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      ]
    );
 
    $count  = 1;
    $tbData = NULL;
    $stmt   = $pdo->query($sql);
    while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
      if ( ! in_array($count, []) )
        $tbData = mk_tbData($tbData, $result, $tbFormat);
      $count = $count + 1;
    }

    $tbMatrix = mk_tbMatrix(NULL, $tbData, 0)[0];
    mk_tbHTML($tbMatrix, $columns);
  } catch (PDOException $e) {
    header('Content-Type: text/plain; charset=UTF-8', true, 500);
    exit($e->getMessage());
  }
}

if ( isset($_POST['listType']) && isset($_POST['listWhere']) ) {
  $listType  = $_POST['listType'];
  $listWhere = $_POST['listWhere'];
  Zabbix_List($listType, $listWhere);
}
?>

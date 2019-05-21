<?php
// リストタイプに応じたSQL文,表示項目,データ構造を返す
function selectListType($typeName, $where=1) {
  switch ( $typeName ) {
    case "ldap_op_history":
      $viewName = 'op_history';
      $columns  = array('exec_time' => ['DName' => '操作時刻'],
                        'exec_user' => ['DName' => '実行ユーザ'],
                        'target'    => ['DName' => '操作対象'],
                        'exec_type' => ['DName' => '操作種別']
      );
      $tbFormat = ['exec_time', 'exec_user', 'target', 'exec_type'];
      break;

    default:
      return;
  }

  return ['viewName' => $viewName, 'columns' => $columns, 'tbFormat' => $tbFormat];
}
?>

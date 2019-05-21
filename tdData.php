<?php
// HTMLテーブル表示
$uniq_id = uniqid();  // htmlタグidに割り当てるユニークid（テナント毎に異なるidを指定したいため）

function mk_tbHTML($tbMatrix, $columns) {
  echo "<table border=1>";
  echo "<thead>";
  echo "<tr bgcolor=gainsboro>";
  foreach ($columns as $colKey => $colVal) {
    $gid = $colKey . $GLOBALS['uniq_id'];
    echo "<th onclick=displaySwitch(\"${gid}\")>${colVal['DName']}";
    if ( isset($colVal['DisSw']) ) {
      echo "<div id=\"${gid}\" style=\"color: blue; text-decoration: underline\">詳細非表示</div>";
    }
    echo "</th>";
  }
  echo "</tr>";
  echo "</thead>";
  echo "<tbody>";

  $curr = current( $tbMatrix );
  $rowLen = count( $curr['val'] );

  $rowNum = 0;
  while ( $rowNum < $rowLen ) {
    echo "<tr valign=top>";
    foreach ($columns as $colKey => $colVal)
      if ( $tbMatrix[$colKey]['isFirst'][$rowNum] ) {
        $nextIsFirst = $rowNum + 1;
        while ( $nextIsFirst < $rowLen ) {
          if ( $tbMatrix[$colKey]['isFirst'][$nextIsFirst] )
            break;
          $nextIsFirst = $nextIsFirst + 1;
        }
        $rowspan = $nextIsFirst - $rowNum;
        if ( isset($colVal['Group']) ) {
          $groupVal = $colVal['Group'];
          $is_f = FALSE;
          $is_l = FALSE;
          // 要素の先頭かを確認
          if ( $tbMatrix[$groupVal]['isFirst'][$rowNum] )
            $is_f = TRUE;
          // 要素の最後かを確認
          $c = 1;
          do {
            if ( ! isset($tbMatrix[$colKey]['isFirst'][$rowNum + $c]) ) {
              $is_l = TRUE;
              break;
            }
            if ( $tbMatrix[$colKey]['isFirst'][$rowNum + $c] ) {
              if ( $tbMatrix[$groupVal]['isFirst'][$rowNum + $c] )
                $is_l = TRUE;
              break;
            }
            $c = $c + 1;
          } while ( ($rowNum+$c) < count($tbMatrix[$colKey]['isFirst']) );

          // 要素のタグ表示
          if( $is_f && !$is_l )
            $style = "style=\"border-style: inset solid none solid\"";
          if( !$is_f && $is_l )
            $style = "style=\"border-style: none solid inset solid\"";
          if( $is_f && $is_l )
            $style = "style=\"border-style: inset inset inset inset\"";
          if( !$is_f && !$is_l )
            $style = "style=\"border-style: none solid none solid\"";
        } else {
          $style = '';
        }
        $gid = $colKey . $GLOBALS['uniq_id'];
        $uid = $gid . "_" . $rowNum;
        echo "<td rowspan=${rowspan} ${style}>";
        echo "<div id=\"${uid}\">";
        echo $tbMatrix[$colKey]['val'][$rowNum];
        echo "</div>";
        echo "</td>";
      }
    echo "</tr>";

    $rowNum = $rowNum + 1;
  }

  echo "</tbody>";
  echo "</table>";
  return;
}


//$tbDataを元にテーブル表示用の配列を作成する
function mk_tbMatrix($tbMatrix, $tbData) {
  $rowNum = 0;
  $keys   = [];
  foreach ($tbData as $tbDataKey => $tbDataVal ) {
    $items     = $tbDataVal[0];
    $nextItems = $tbDataVal[1];

    foreach ($items as $key => $val) {
      $keys[]                      = $key;
      $tbMatrix[$key]['val'][]     = $val;
      $tbMatrix[$key]['isFirst'][] = TRUE;
    }

    if ( isset($nextItems) ) {
      $nextItemRowMax = 0;
      foreach ($nextItems as $nextItem) {
        $r        = mk_tbMatrix($tbMatrix, $nextItem);
        $rowNum_t = $r[1];
        $keys_t   = $r[2];
        $keys     = array_merge($keys, $keys_t);
        if ( $nextItemRowMax < $rowNum_t ) {
          $nextItemRowMax = $rowNum_t;
        }
      }
      $rowNum = $rowNum + $nextItemRowMax;
      foreach ($nextItems as $nextItem) {
        $r        = mk_tbMatrix($tbMatrix, $nextItem);
        $tbMatrix = $r[0];
        $rowNum_t = $r[1];
        $keys_t   = $r[2];

        foreach ($keys_t as $key) {
          $c = 0;
          while ( $c < ($nextItemRowMax-$rowNum_t) ) {
            $tbMatrix[$key]['val'][]     = NULL;
            $tbMatrix[$key]['isFirst'][] = FALSE;
            $c                           = $c + 1;
          }
        }
      }
      foreach ($items as $key => $val){
        $c = 1;
        while ( $c < $nextItemRowMax ) {
          $tbMatrix[$key]['val'][]     = NULL;
          $tbMatrix[$key]['isFirst'][] = FALSE;
          $c                           = $c + 1;
        }
      }
    } else {
      $rowNum = $rowNum + 1;
    }
  }

  $keys = array_values( array_unique($keys) );
  #echo "return: ${rowNum}<br>";
  return [$tbMatrix, $rowNum, $keys];
}

 
//$tbFormatに指定したフォーマットで$addDataのデータを$tbDataに挿入する
function mk_tbData($tbData, $addData, $tbFormat) {
  // keys => [[key1,key2 ...], [[val:keys], [val:keys], ...]]
  $keys            = [];
  $vals            = NULL;
  $tbFormatInsides = NULL;

  foreach ($tbFormat as $key) {
    if ( is_array($key) ) {
      $tbFormatInsides[] = $key;
    } else {
      $keys[$key]     = $addData[$key];
      $keyNames[]     = $key;
    }
  }
  $compoundKey             = implode(",", array_values($keys));
  $tdFcompoundKeyNames     = implode(",", $keyNames);

  if ( isset($tbData) )
    $keyNames = array_keys( current($tbData)[0] );
  $tdDcompoundKeyNames     = implode(",", $keyNames);

  if ( $tdFcompoundKeyNames != $tdDcompoundKeyNames )
    return;

  if ( isset($tbFormatInsides) ) {
    if ( isset($tbData) && array_key_exists($compoundKey, $tbData) ) {
      $vals = $tbData[$compoundKey][1];
    } else {
      $vals = NULL;
    }
    unset ( $updateVals );
    foreach ($tbFormatInsides as $tbFormatInside) {
      if ( isset($vals) ) {
        foreach ($vals as $val) {
          $updateVals_t = mk_tbData($val, $addData, $tbFormatInside);
          if ( isset($updateVals_t) )
            $updateVals[] = $updateVals_t;
        }
      } else {
        $updateVals[] = mk_tbData(NULL, $addData, $tbFormatInside);
      }
    }
    $vals = $updateVals;
  }

  $tbData[$compoundKey] = [$keys, $vals];
  return $tbData;
}
?>

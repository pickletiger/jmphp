<?php 
  require('../../conn.php');
  header("Access-Control-Allow-Origin: *");

  //接收前端收据
  $data = $_POST['data'];
  $Arr = []; // 需要更新的数据,key为字段名
  $sql = 'UPDATE plan_table SET ';
  $count = 1;
  
  // // 将字符串打散为数组
  foreach (explode('^_^',substr($data,0,-3)) as $key => $val) {
    $arr = explode('(*^▽^*)',$val);

    $Arr = array_merge($Arr, array($arr[0] => $arr[1]));
  }

  $num = count($Arr);
  // 拼接sql语句
  foreach ($Arr as $key => $val) {
    if($count == $num-1) {
      $sql = substr($sql,0,-1).' WHERE '.$key.'='.$val;
    }else if($count == $num) {
      $sql .= ' AND '.$key.'="'.$val.'"';
    }else {
      $sql.=$key.'='.$val.',';
    }
    $count++;
  }

  $result = $conn->query($sql);
  echo $result;
?>
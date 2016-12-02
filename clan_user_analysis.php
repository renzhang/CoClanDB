<?php

  // Insert the page header
  $page_title = "部落战";
  require_once('header.php');

  require_once('appvars.php');
  require_once('connectvars.php');

  // Show the navigation menu
  require_once('navmenu.php');

  // Connect to the database
  $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
  mysqli_set_charset($dbc, 'utf8');
  // Get battle amount during date period, or data period from battle amount
  if ( isset($_GET['from_date']) AND isset($_GET['to_date']) ) {
    mysqli_set_charset($dbc, 'utf8');
    $from_date = $_GET['from_date'];
    $to_date = $_GET['to_date'];
    $query_battle_amount = "SELECT battle_id FROM `clan_battle_info`" .
      " WHERE battle_date>='" . $from_date . "'" .
      " AND battle_date<='" . $to_date . "'";
    $data_battle_amount = mysqli_query($dbc, $query_battle_amount);
    $battle_show = 0;
    while($row_battle_amount = mysqli_fetch_array($data_battle_amount)){
      $battle_show++;
    }
  }
  else {
    # get how many battles are listed
    # 0: last battle
    # 1: last 5 battle
    # 2: last 10 battle
    $disp_id = (int) $_GET['id'];
    $battle_show = ($disp_id == 0) ? 1 : $disp_id * 5;

    // Grab the profile data from the database
    mysqli_set_charset($dbc, 'utf8');
    $query_battle_date = "SELECT battle_date FROM `clan_battle_info` ORDER BY battle_date DESC LIMIT " . $battle_show;
    $data_battle_date = mysqli_query($dbc, $query_battle_date);
    $battle_show = mysqli_num_rows($data_battle_date);
    $row_battle_date = mysqli_fetch_array($data_battle_date);
    $to_date = $row_battle_date['battle_date'];
    $from_date = $to_date;
    while ($row_battle_date = mysqli_fetch_array($data_battle_date)) {
      $from_date = $row_battle_date['battle_date'];
    }
  }
  echo '<br> FROM ' . $from_date . ' TO ' . $to_date;
  echo '<br> 帮战次数：' . $battle_show . '<br>';

  // Checking Period battle statistic
  $query_battle_fight = "SELECT bm.attack_pos" .
                        " FROM clan_battle_info as bi" .
                        " INNER JOIN clan_battle_map as bm USING (battle_id)" .
                        " INNER JOIN clan_battle_fight AS bf USING (fight_id)" .
                        " WHERE bi.battle_date>='" . $from_date . "' AND bi.battle_date<='" . $to_date . "' AND attack=1";
  $data_battle_fight = mysqli_query($dbc, $query_battle_fight);
  $total_fight = mysqli_num_rows($data_battle_fight);

  $query_battle_fight = "SELECT bm.attack_pos" .
                        " FROM clan_battle_info as bi" .
                        " INNER JOIN clan_battle_map as bm USING (battle_id)" .
                        " INNER JOIN clan_battle_fight AS bf USING (fight_id)" .
                        " WHERE bi.battle_date>='" . $from_date . "' AND bi.battle_date<='" . $to_date . "' AND attack=1" .
                        " AND win_star=3";
  $data_battle_fight = mysqli_query($dbc, $query_battle_fight);
  $total_3star = mysqli_num_rows($data_battle_fight);
  $query_battle_fight = "SELECT bm.attack_pos" .
                        " FROM clan_battle_info as bi" .
                        " INNER JOIN clan_battle_map as bm USING (battle_id)" .
                        " INNER JOIN clan_battle_fight AS bf USING (fight_id)" .
                        " WHERE bi.battle_date>='" . $from_date . "' AND bi.battle_date<='" . $to_date . "' AND attack=1" .
                        " AND crush_rate>=0.9";
  $data_battle_fight = mysqli_query($dbc, $query_battle_fight);
  $total_highcrush = mysqli_num_rows($data_battle_fight);

  // $query_battle_fight = "SELECT bm.attack_pos" .
  //                       " FROM clan_battle_info as bi" .
  //                       " INNER JOIN clan_battle_map as bm USING (battle_id)" .
  //                       " INNER JOIN clan_battle_fight AS bf USING (fight_id)" .
  //                       " WHERE bi.battle_date>='" . $from_date . "' AND bi.battle_date<='" . $to_date . "' AND attack=1" .
  //                       " AND attack_grade >= 10 AND win_star=3";
  // $data_battle_fight = mysqli_query($dbc, $query_battle_fight);
  // $high_3star = mysqli_num_rows($data_battle_fight);
  // $query_battle_fight = "SELECT bm.attack_pos" .
  //                       " FROM clan_battle_info as bi" .
  //                       " INNER JOIN clan_battle_map as bm USING (battle_id)" .
  //                       " INNER JOIN clan_battle_fight AS bf USING (fight_id)" .
  //                       " WHERE bi.battle_date>='" . $from_date . "' AND bi.battle_date<='" . $to_date . "' AND attack=1" .
  //                       " AND attack_grade >= 10 AND crush_rate>=0.9";
  // $data_battle_fight = mysqli_query($dbc, $query_battle_fight);
  // $high_highcrush = mysqli_num_rows($data_battle_fight);

  // $query_battle_fight = "SELECT bm.attack_pos" .
  //                       " FROM clan_battle_info as bi" .
  //                       " INNER JOIN clan_battle_map as bm USING (battle_id)" .
  //                       " INNER JOIN clan_battle_fight AS bf USING (fight_id)" .
  //                       " WHERE bi.battle_date>='" . $from_date . "' AND bi.battle_date<='" . $to_date . "' AND attack=1" .
  //                       " AND attack_grade=9 AND win_star=3";
  // $data_battle_fight = mysqli_query($dbc, $query_battle_fight);
  // $middle_3star = mysqli_num_rows($data_battle_fight);
  // $query_battle_fight = "SELECT bm.attack_pos" .
  //                       " FROM clan_battle_info as bi" .
  //                       " INNER JOIN clan_battle_map as bm USING (battle_id)" .
  //                       " INNER JOIN clan_battle_fight AS bf USING (fight_id)" .
  //                       " WHERE bi.battle_date>='" . $from_date . "' AND bi.battle_date<='" . $to_date . "' AND attack=1" .
  //                       " AND attack_grade=9 AND crush_rate>=0.9";
  // $data_battle_fight = mysqli_query($dbc, $query_battle_fight);
  // $middle_highcrush = mysqli_num_rows($data_battle_fight);

  // $query_battle_fight = "SELECT bm.attack_pos" .
  //                       " FROM clan_battle_info as bi" .
  //                       " INNER JOIN clan_battle_map as bm USING (battle_id)" .
  //                       " INNER JOIN clan_battle_fight AS bf USING (fight_id)" .
  //                       " WHERE bi.battle_date>='" . $from_date . "' AND bi.battle_date<='" . $to_date . "' AND attack=1" .
  //                       " AND attack_grade<=8 AND win_star=3";
  // $data_battle_fight = mysqli_query($dbc, $query_battle_fight);
  // $junior_3star = mysqli_num_rows($data_battle_fight);
  // $query_battle_fight = "SELECT bm.attack_pos" .
  //                       " FROM clan_battle_info as bi" .
  //                       " INNER JOIN clan_battle_map as bm USING (battle_id)" .
  //                       " INNER JOIN clan_battle_fight AS bf USING (fight_id)" .
  //                       " WHERE bi.battle_date>='" . $from_date . "' AND bi.battle_date<='" . $to_date . "' AND attack=1" .
  //                       " AND attack_grade<=8  AND crush_rate>=0.9";
  // $data_battle_fight = mysqli_query($dbc, $query_battle_fight);
  // $junior_highcrush = mysqli_num_rows($data_battle_fight);


  echo "战斗场数: " . $total_fight . ', ';
  echo "三星率: " . number_format( $total_3star / $total_fight * 100, 2) . '%, ';
  echo "高摧毁率: " . number_format( $total_highcrush / $total_fight * 100, 2) . '%, ';

  mysqli_set_charset($dbc, 'utf8');

  // Checking the battle fight missing members
  $query_fight_miss = "SELECT attack_pos FROM clan_battle_info as bi" .
                      " INNER JOIN clan_battle_map as bm USING (battle_id)" .
                      " WHERE battle_date>='" . $from_date . "' AND battle_date<='" . $to_date . "'" .
                      " AND attack=1 AND defense_pos=0";
  $data_fight_miss = mysqli_query($dbc, $query_fight_miss);
  $user_missing_IDs = array();
  $user_id = 0;
  while ($row_fight_miss = mysqli_fetch_array($data_fight_miss)) {
    if ($user_id == $row_fight_miss['attack_pos']) {
      $user_missing_IDs[$user_id] ++;
    }
    else {
      $user_id = $row_fight_miss['attack_pos'];
      $user_missing_IDs[$user_id] = 1;
    }
  }

  echo '<br><table border="1">';
  echo '<tr><td>未参战成员</td><td>未战斗次数</td></tr>';
  foreach ($user_missing_IDs as $key => $value) {
    echo '<tr>';
    $query_user_name = "SELECT user_name FROM `clan_user_info` WHERE user_id=" . $key;
    $data_user_name = mysqli_query($dbc, $query_user_name);
    $row_user_name = mysqli_fetch_array($data_user_name);
    echo '<td>' . $row_user_name['user_name'] . '</td>';
    echo '<td>' . $value . '</td>';
    echo '</tr>';
  }
  echo '</table>';


  // Checking the 3 star wining members on the same or higher grade
  $query_win_fight = "SELECT bm.attack_pos, bm.attack_grade, bm.defense_grade, bi.battle_date" .
                     " FROM clan_battle_info as bi" .
                     " INNER JOIN clan_battle_map AS bm USING (battle_id)" .
                     " INNER JOIN clan_battle_fight AS bf USING (fight_id)" .
                     " WHERE bi.battle_date>='" . $from_date . "' AND bi.battle_date<='" . $to_date . "'" .
                     " AND bm.attack_grade <= bm.defense_grade AND bf.win_star=3 AND bm.attack=1";
  $data_win_fight = mysqli_query($dbc, $query_win_fight);

  $user_ID_3star = array();
  $user_ID_data = array();
  while ($row_win_fight = mysqli_fetch_array($data_win_fight)) {
    $user_id = $row_win_fight['attack_pos'];
    $fight_data = $row_win_fight['battle_date'] . ': ' . $row_win_fight['attack_grade'] . '本攻' . $row_win_fight['defense_grade'] . '本';
    if (array_key_exists($user_id, $user_ID_3star)) {
      $user_ID_3star[$user_id] ++;
    }
    else {
      $user_ID_3star[$user_id] = 1;
    }
    if (array_key_exists($user_id, $user_ID_data)) {
      array_push($user_ID_data[$user_id], $fight_data);
    }
    else {
      $user_data = array();
      $user_ID_data[$user_id] = $user_data;
      array_push($user_ID_data[$user_id], $fight_data);
    }
  }
  arsort($user_ID_3star);

  echo '<br><table border="1">';
  echo '<tr><td>三星打手</td><td>成功进攻次数</td><td>详情</td></tr>';
  foreach ($user_ID_3star as $key => $value) {
    $query_user_name = "SELECT user_name FROM `clan_user_info` WHERE user_id=" . $key;
    $data_user_name = mysqli_query($dbc, $query_user_name);
    $row_user_name = mysqli_fetch_array($data_user_name);
    echo '<td>' . $row_user_name['user_name'] . '</td>';
    echo '<td>' . $value . '次</td>';
    echo '<td>';
    foreach ($user_ID_data[$key] as $key_data => $value_data) {
      echo $value_data . '; ';
    }
    echo '</td>';
    echo '</tr>';
  }
  echo '</table>';


  // Checking the 3 star wining members on the lower grade
  $query_win_fight = "SELECT bm.attack_pos, bm.attack_grade, bm.defense_grade, bi.battle_date" .
                     " FROM clan_battle_info as bi" .
                     " INNER JOIN clan_battle_map AS bm USING (battle_id)" .
                     " INNER JOIN clan_battle_fight AS bf USING (fight_id)" .
                     " WHERE bi.battle_date>='" . $from_date . "' AND bi.battle_date<='" . $to_date . "'" .
                     " AND bm.attack_grade > bm.defense_grade AND bf.win_star=3 AND bm.attack=1";
  $data_win_fight = mysqli_query($dbc, $query_win_fight);

  $user_ID_3star = array();
  $user_ID_data = array();

  while ($row_win_fight = mysqli_fetch_array($data_win_fight)) {
    $user_id = $row_win_fight['attack_pos'];
    $fight_data = $row_win_fight['battle_date'] . ': ' . $row_win_fight['attack_grade'] . '本攻' . $row_win_fight['defense_grade'] . '本';
    if (array_key_exists($user_id, $user_ID_3star)) {
      $user_ID_3star[$user_id] ++;
    }
    else {
      $user_ID_3star[$user_id] = 1;
    }
    if (array_key_exists($user_id, $user_ID_data)) {
      array_push($user_ID_data[$user_id], $fight_data);
    }
    else {
      $user_data = array();
      $user_ID_data[$user_id] = $user_data;
      array_push($user_ID_data[$user_id], $fight_data);
    }
  }
  arsort($user_ID_3star);

  echo '<br><table border="1">';
  echo '<tr><td>强推达人</td><td>攻</td><td>守</td></tr>';
  foreach ($user_ID_3star as $key => $value) {
    $query_user_name = "SELECT user_name FROM `clan_user_info` WHERE user_id=" . $key;
    $data_user_name = mysqli_query($dbc, $query_user_name);
    $row_user_name = mysqli_fetch_array($data_user_name);
    echo '<td>' . $row_user_name['user_name'] . '</td>';
    echo '<td>' . $value . '次</td>';
    echo '<td>';
    foreach ($user_ID_data[$key] as $key_data => $value_data) {
      echo $value_data . '; ';
    }
    echo '</td>';
    echo '</tr>';
  }
  echo '</table>';


  // checking members who attack higher grade with failure
  $query_win_fight = "SELECT bm.attack_pos, bm.attack_grade, bm.defense_grade, bi.battle_date" .
                     " FROM clan_battle_info as bi" .
                     " INNER JOIN clan_battle_map AS bm USING (battle_id)" .
                     " INNER JOIN clan_battle_fight AS bf USING (fight_id)" .
                     " WHERE bi.battle_date>='" . $from_date . "' AND bi.battle_date<='" . $to_date . "'" .
                     " AND bm.attack_grade < bm.defense_grade AND bf.win_star<3 AND bm.attack=1";
  $data_win_fight = mysqli_query($dbc, $query_win_fight);

  $user_ID_steal = array();
  $user_ID_data = array();

  while ($row_win_fight = mysqli_fetch_array($data_win_fight)) {
    $user_id = $row_win_fight['attack_pos'];
    $fight_data = $row_win_fight['battle_date'] . ': ' . $row_win_fight['attack_grade'] . '本攻' . $row_win_fight['defense_grade'] . '本';
    if (array_key_exists($user_id, $user_ID_steal)) {
      $user_ID_steal[$user_id] ++;
    }
    else {
      $user_ID_steal[$user_id] = 1;
    }
    if (array_key_exists($user_id, $user_ID_data)) {
      array_push($user_ID_data[$user_id], $fight_data);
    }
    else {
      $user_data = array();
      $user_ID_data[$user_id] = $user_data;
      array_push($user_ID_data[$user_id], $fight_data);
    }
  }
  arsort($user_ID_steal);

  echo '<br><table border="1">';
  echo '<tr><td>探路/偷本小强</td><td>攻</td><td>守</td></tr>';
  foreach ($user_ID_steal as $key => $value) {
    $query_user_name = "SELECT user_name FROM `clan_user_info` WHERE user_id=" . $key;
    $data_user_name = mysqli_query($dbc, $query_user_name);
    $row_user_name = mysqli_fetch_array($data_user_name);
    echo '<td>' . $row_user_name['user_name'] . '</td>';
    echo '<td>' . $value . '次</td>';
    echo '<td>';
    foreach ($user_ID_data[$key] as $key_data => $value_data) {
      echo $value_data . '; ';
    }
    echo '</td>';
    echo '</tr>';
    echo '</tr>';
  }
  echo '</table>';

  mysqli_close($dbc);
?>

<?php
  //Insert the page footer
  require_once('footer.php');
?>
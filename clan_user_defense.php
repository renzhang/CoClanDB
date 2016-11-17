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

  echo '<br><br>';
  echo '<a href="clan_user_defense.php?id=0">最近一战</a>';
  echo ' | ';
  echo '<a href="clan_user_defense.php?id=1">最近五战</a>';
  echo ' | ';
  echo '<a href="clan_user_defense.php?id=2">最近十战</a>';
  echo '<br>';

  echo '<br>Clanwar Record<br>';
  echo '<table border="1">';
  $query_battle_info = "SELECT battle_date, battle_size FROM `clan_battle_info`";
  $data_battle_info = mysqli_query($dbc, $query_battle_info);
  $row_month = '0';
  $month_start_date = '0';
  $month_end_date = '0';
  while($row_battle_info = mysqli_fetch_array($data_battle_info)) {
    $battle_date = $row_battle_info['battle_date'];

    $battle_month = date('M', strtotime($battle_date));
    $battle_size = $row_battle_info['battle_size'];
    $link_query_date = "clan_user_defense.php?from_date=" . $battle_date . "&to_date=" . $battle_date;
    if ($battle_month != $row_month) {
      if ($row_month != '0') {
        echo '</tr>';
        echo '<tr><td>Battle: ' . $amount_month . '</td>';
        $link_query_user = "clan_user_defense.php?from_date=" . $month_start_date . "&to_date=" . $month_end_date;
        echo '<td><a href="' . $link_query_user . '">全体</a> </td>';
        $link_query_user = "clan_user_defense.php?from_date=" . $month_start_date . "&to_date=" . $month_end_date . "&grade=10";
        echo '<td><a href="' . $link_query_user . '">上（...~10）</a> </td>';
        $link_query_user = "clan_user_defense.php?from_date=" . $month_start_date . "&to_date=" . $month_end_date . "&grade=9";
        echo '<td><a href="' . $link_query_user . '">中（9）</a> </td>';
        $link_query_user = "clan_user_defense.php?from_date=" . $month_start_date . "&to_date=" . $month_end_date . "&grade=8";
        echo '<td><a href="' . $link_query_user . '">少（8~...）</a> </td></tr>';
      }
      $row_month = $battle_month;
      $month_start_date = $battle_date;
      $amount_month = 1;
      echo '<tr><th rowspan="2">' . $row_month . '</th>';
      echo '<td><a href="' . $link_query_date . '">' . $battle_date . ' (' . $battle_size . '人)</a> </td>';
    }
    else {
      $month_end_date = $battle_date;
      $amount_month++;
      echo '<td><a href="' . $link_query_date . '">' . $battle_date . ' (' . $battle_size . '人)</a> </td>';
    }
  }
  echo '<tr><td>Battle: ' . $amount_month . '</td>';
  $link_query_user = "clan_user_defense.php?from_date=" . $month_start_date . "&to_date=" . $month_end_date;
  echo '<td><a href="' . $link_query_user . '">全体</a> </td>';
  $link_query_user = "clan_user_defense.php?from_date=" . $month_start_date . "&to_date=" . $month_end_date . "&grade=10";
  echo '<td><a href="' . $link_query_user . '">上（...~10）</a> </td>';
  $link_query_user = "clan_user_defense.php?from_date=" . $month_start_date . "&to_date=" . $month_end_date . "&grade=9";
  echo '<td><a href="' . $link_query_user . '">中（9）</a> </td>';
  $link_query_user = "clan_user_defense.php?from_date=" . $month_start_date . "&to_date=" . $month_end_date . "&grade=8";
  echo '<td><a href="' . $link_query_user . '">少（8~...）</a> </td></tr>';
  echo '</table>';

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
  echo '<br> 帮战次数：' . $battle_show . '  ';
  echo 'FROM ' . $from_date . ' TO ' . $to_date . '<br>';
  echo '<table border="0">';
  $link_query_grade = "clan_user_defense.php?from_date=" . $from_date . "&to_date=" . $to_date;
  echo '<tr><td><a href="' . $link_query_grade . '">全体</a> </td>';
  $link_query_grade = "clan_user_defense.php?from_date=" . $from_date . "&to_date=" . $to_date . "&grade=10";
  echo '<td><a href="' . $link_query_grade . '">上（...~10）</a> </td>';
  $link_query_grade = "clan_user_defense.php?from_date=" . $from_date . "&to_date=" . $to_date . "&grade=9";
  echo '<td><a href="' . $link_query_grade . '">中（9）</a> </td>';
  $link_query_grade = "clan_user_defense.php?from_date=" . $from_date . "&to_date=" . $to_date . "&grade=8";
  echo '<td><a href="' . $link_query_grade . '">少（8~...）</a> </td></tr>';
  echo '</table>';


  // Retrieve the user data from MySQL
  mysqli_set_charset($dbc, 'utf8');

  if ( isset($_GET['grade'])) {
    $grade_set = (int)$_GET['grade'];
    $query_user = "SELECT ui.user_id, ui.user_name, ug.grade, ug.levels, ug.cup, ug.star" .
      " FROM `clan_user_info` AS ui" .
      " INNER JOIN `clan_user_grade` AS ug USING (user_id)" .
      " WHERE status=1";
    if ($grade_set>=10) {
      $query_user .= " AND grade>=" . $grade_set;
    }
    else {
      $query_user .= " AND grade=" . $grade_set;
    }
  }
  else {
    $query_user = "SELECT ui.user_id, ui.user_name, ug.grade" .
      " FROM `clan_user_info` AS ui" .
      " INNER JOIN `clan_user_grade` AS ug USING (user_id)" .
      " WHERE status=1" .
      " ORDER BY ug.grade DESC";
  }
  $data_user = mysqli_query($dbc, $query_user);

  $user_data_IDs = array();
  $user_star_IDs = array();
  $user_crush_IDs = array();
  // Loop through the array of user data, formatting it as HTML
  while ($row_user = mysqli_fetch_array($data_user)) {
    $user_id = $row_user['user_id'];
    $user_name = $row_user['user_name'];
    $user_grade = $row_user['grade'];
    $defense_time = 0;
    $aver_lose_star = 0;
    $aver_crush_rate = 0;

    $query_war_record = "SELECT bi.battle_id, bm.attack_pos, bf.win_star, bf.crush_rate" .
      " FROM clan_battle_info AS bi" .
      " INNER JOIN clan_battle_map as bm USING (battle_id)" .
      " INNER JOIN clan_battle_fight as bf USING (fight_id)" .
      " WHERE attack=0 AND defense_pos=" . $user_id .
      " AND battle_date>='" . $from_date .
      "' AND battle_date<='" . $to_date . "'";
    $data_war_record = mysqli_query($dbc, $query_war_record);
    while ($row_war_record = mysqli_fetch_array($data_war_record)) {
      $defense_time ++;
      $aver_lose_star  += $row_war_record['win_star'];
      $aver_crush_rate += $row_war_record['crush_rate'];
    }
    $aver_lose_star  = round(($aver_lose_star / $defense_time),1);
    $aver_crush_rate = round(($aver_crush_rate / $defense_time * 100),1);
    $user_data = array();
    if ($defense_time != 0 AND $defense_time >= $battle_show) {
      $user_star_IDs[$user_id] = $aver_lose_star;
      $user_crush_IDs[$user_id] = $aver_crush_rate;
      array_push($user_data, $user_name);
      array_push($user_data, $user_grade);
      array_push($user_data, $defense_time);
      array_push($user_data, $aver_lose_star);
      array_push($user_data, $aver_crush_rate);
      $user_data_IDs[$user_id] = $user_data;
    }
  }
  echo '<br>防守失星排名<br>';
  echo '<table border="1">';
  echo '<tr><td>成员</td><td>本位</td><td>防御次数</td><td>失星</td><td>摧毁率</td></tr>';
  asort($user_star_IDs);
  foreach ($user_star_IDs as $key => $value) {
    echo '<tr><td><a href="viewplayer.php?user_id=' . $key . '">' . $user_data_IDs[$key][0] . '</a> </td>';
    echo '<td>' . $user_data_IDs[$key][1] . '</td>';
    echo '<td>' . $user_data_IDs[$key][2] . '</td>';
    echo '<td>' . $user_data_IDs[$key][3] . '&#9733</td>';
    echo '<td>' . $user_data_IDs[$key][4] . '</td>';
    echo '</tr>';
  }
  echo '</table>';
  echo '<br>防守摧毁率排名<br>';
  echo '<table border="1">';
  echo '<tr><td>成员</td><td>本位</td><td>防御次数</td><td>失星</td><td>摧毁率</td></tr>';
  asort($user_crush_IDs);
  foreach ($user_crush_IDs as $key => $value) {
    echo '<tr><td><a href="viewplayer.php?user_id=' . $key . '">' . $user_data_IDs[$key][0] . '</a> </td>';
    echo '<td>' . $user_data_IDs[$key][1] . '</td>';
    echo '<td>' . $user_data_IDs[$key][2] . '</td>';
    echo '<td>' . $user_data_IDs[$key][3] . '&#9733</td>';
    echo '<td>' . $user_data_IDs[$key][4] . '</td>';
    echo '</tr>';
  }
  echo '</table>';

  mysqli_close($dbc);
?>

<?php
  //Insert the page footer
  require_once('footer.php');
?>
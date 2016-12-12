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

  echo '<br>';

  echo '<br>Clanwar Report<br>';
  echo '<table border="1">';
  $query_battle_info = "SELECT battle_date, battle_size FROM `clan_battle_info`";
  $data_battle_info = mysqli_query($dbc, $query_battle_info);
  $row_month = '0';
  $month_start_date = '0';
  $month_end_date = '0';
  while($row_battle_info = mysqli_fetch_array($data_battle_info)) {
    $battle_date = $row_battle_info['battle_date'];
    $battle_month = date('M', strtotime($battle_date));
    $battle_year = date('Y', strtotime($battle_date));
    if ($battle_month != $row_month) {
      if ($row_month != '0') {
        echo '<td>Battle: ' . $amount_month . '</td>';
        $link_query_monty_report = "month_report.php?from_date=" . $month_start_date . "&to_date=" . $month_end_date;
        echo '<td><a href="' . $link_query_monty_report . '">月度总结</a> </td></tr>';
      }
      echo '<tr><td>' . $battle_year . '/' . $battle_month . '</td>';
      $row_month = $battle_month;
      $month_start_date = $battle_date;
      $amount_month = 1;
    }
    else {
      $month_end_date = $battle_date;
      $amount_month++;
    }
  }
  echo '<td>Battle: ' . $amount_month . '</td>';
  $link_query_monty_report = "month_report.php?from_date=" . $month_start_date . "&to_date=" . $month_end_date;
  echo '<td><a href="' . $link_query_monty_report . '">月度总结</a> </td>';
  echo '</table>';

  if ( isset($_GET['from_date']) AND isset($_GET['to_date']) ) {
    mysqli_set_charset($dbc, 'utf8');
    $from_date = $_GET['from_date'];
    $to_date = $_GET['to_date'];
  }
  elseif ( isset($_GET['id']) ) {
    # get how many battles are listed
    # 0: last battle
    $month_id = (int) $_GET['id'];

    if ($month_id != 0) {
      echo "ERROR calling! Generate current month report:<br>";
    }
    $from_date =  date('Y-m-01', strtotime(date('Y-m-d')));
    $to_date = date('Y-m-d', strtotime(date('Y-m-01', strtotime(date('Y-m-d'))) . ' +1 month -1 day'));
  }
  else {
    echo "ERROR calling!<br>";
    $from_date =  date('Y-m-01', strtotime(date('Y-m-d')));
    $to_date = date('Y-m-d', strtotime(date('Y-m-01', strtotime(date('Y-m-d'))) . ' +1 month -1 day'));
  }

  // Retrieve the user data from MySQL
  mysqli_set_charset($dbc, 'utf8');

  // Calculate Monty Result list
  $query_battle_info = "SELECT battle_id, battle_date, battle_size, star_win, star_lose, crushrate_win, crushrate_lose" .
                       " FROM `clan_battle_info`" .
                       " WHERE battle_date>='" . $from_date .
                       "' AND battle_date<='" . $to_date . "'";
  $data_battle_info = mysqli_query($dbc, $query_battle_info);
  $month_win = 0;
  $month_draw = 0;
  $month_lose = 0;

  while($row_battle_info = mysqli_fetch_array($data_battle_info)) {
    $star_attack = $row_battle_info['star_win'];
    $star_defense = $row_battle_info['star_lose'];
    $crushrate_attack = $row_battle_info['crushrate_win'];
    $crushrate_defense = $row_battle_info['crushrate_lose'];

    if ($star_attack < $star_defense) {
      $month_lose++;
    }
    elseif ($star_attack > $star_defense) {
      $month_win++;
    }
    elseif ($crushrate_attack > $crushrate_defense) {
      $month_win++;
    }
    elseif ($crushrate_attack < $crushrate_defense) {
      $month_lose++;
    }
    else {
      $month_draw++;
    }
  }
  $amount_month = $month_win + $month_lose + $month_draw;

  echo '<br>';
  echo date('Y-m', strtotime($to_date)) . "月度总结<br>";
  echo $amount_month . '战 ';
  echo $month_win . '胜 ';
  echo $month_draw . '平 ';
  echo $month_lose . '负 <br>';

  $query_war_record = "SELECT bm.defense_pos" .
                      " FROM clan_battle_info AS bi" .
                      " INNER JOIN clan_battle_map as bm USING (battle_id)" .
                      " INNER JOIN clan_battle_fight as bf USING (fight_id)" .
                      " WHERE attack=1" .
                      " AND battle_date>='" . $from_date .
                      "' AND battle_date<='" . $to_date . "'";
  $data_war_record = mysqli_query($dbc, $query_war_record);
  $fight_total = mysqli_num_rows($data_war_record);

  $query_war3star_record = "SELECT bm.defense_pos" .
                           " FROM clan_battle_info AS bi" .
                           " INNER JOIN clan_battle_map as bm USING (battle_id)" .
                           " INNER JOIN clan_battle_fight as bf USING (fight_id)" .
                           " WHERE attack=1 AND win_star=3" .
                           " AND battle_date>='" . $from_date .
                           "' AND battle_date<='" . $to_date . "'";
  $data_war3star_record = mysqli_query($dbc, $query_war3star_record);
  $fight_3star = mysqli_num_rows($data_war3star_record);

  $query_warhighrate_record = "SELECT bm.defense_pos" .
                              " FROM clan_battle_info AS bi" .
                              " INNER JOIN clan_battle_map as bm USING (battle_id)" .
                              " INNER JOIN clan_battle_fight as bf USING (fight_id)" .
                              " WHERE attack=1 AND crush_rate>0.9 AND crush_rate<1" .
                              " AND battle_date>='" . $from_date .
                              "' AND battle_date<='" . $to_date . "'";
  $data_warhighrate_record = mysqli_query($dbc, $query_warhighrate_record);
  $fight_highrate = mysqli_num_rows($data_warhighrate_record);

  echo "三星率:" . number_format($fight_3star / $fight_total * 100, 2) . '%<br>';
  echo "高摧毁率（90%~99%）:" . number_format($fight_highrate / $fight_total * 100, 2) . '%<br>';

  $query_user = "SELECT ui.user_id, ui.user_name" .
              " FROM `clan_user_info` AS ui" .
              " INNER JOIN `clan_user_grade` AS ug USING (user_id)" .
              " WHERE status=1" .
              " ORDER BY ug.grade DESC";
  $data_user = mysqli_query($dbc, $query_user);

  $user_high_star_IDs = array();
  $user_high_crush_IDs = array();
  $user_middle_star_IDs = array();
  $user_middle_crush_IDs = array();
  $user_junior_star_IDs = array();
  $user_junior_crush_IDs = array();
  $user_data_IDs = array();

  $attend_user_IDs = array();
  $absent_user_IDs = array();
  $unlucky_user_IDs = array();
  $unlucky_data_IDs = array();
  $fullstar_user_IDs = array();
  $fullstar_data_IDs = array();
  $defense_star_IDs = array();
  $defense_data_IDs = array();

  // Loop through the array of user data, formatting it as HTML
  while ($row_user = mysqli_fetch_array($data_user)) {
    $user_id = $row_user['user_id'];
    $user_name = $row_user['user_name'];
    $win_star = 0;
    $crush_rate = 0;

    $query_show_record = "SELECT bi.battle_id" .
                         " FROM clan_battle_info AS bi" .
                         " INNER JOIN clan_battle_map as bm USING (battle_id)" .
                         " WHERE attack=1" .
                         " AND attack_pos=" . $user_id .
                         " AND battle_date>='" . $from_date .
                         "' AND battle_date<='" . $to_date . "'";
    $data_show_record = mysqli_query($dbc, $query_show_record);
    $war_attend = mysqli_num_rows($data_show_record);
    if ($war_attend < $amount_month / 2) {
      $attend_user_IDs[$user_id] = $war_attend;
    }
    $query_fightmiss_record = "SELECT bi.battle_id" .
                              " FROM clan_battle_info AS bi" .
                              " INNER JOIN clan_battle_map as bm USING (battle_id)" .
                              " WHERE attack=1 AND defense_pos=0" .
                              " AND attack_pos=" . $user_id .
                              " AND battle_date>='" . $from_date .
                              "' AND battle_date<='" . $to_date . "'";
    $data_fightmiss_record = mysqli_query($dbc, $query_fightmiss_record);
    $fight_miss = mysqli_num_rows($data_fightmiss_record);
    if ($fight_miss > 0) {
      $absent_user_IDs[$user_id] = $fight_miss;
    }

    $query_battle = "SELECT battle_id, battle_date FROM clan_battle_info" .
                    " WHERE battle_date>='" . $from_date .
                    "' AND battle_date<='" . $to_date . "'";
    $data_battle = mysqli_query($dbc, $query_battle);
    $fullstar_time = 0;
    $fullstar_data = array();
    while ($row_battle = mysqli_fetch_array($data_battle)) {
      $query_6star_record = "SELECT bi.battle_id" .
                            " FROM clan_battle_info AS bi" .
                            " INNER JOIN clan_battle_map as bm USING (battle_id)" .
                            " INNER JOIN clan_battle_fight as bf USING (fight_id)" .
                            " WHERE attack=1 AND attack_pos=" . $user_id .
                            " AND battle_id=" . $row_battle['battle_id'] .
                            " AND win_star=3 AND defense_grade>=attack_grade";
      $data_6star_date = mysqli_query($dbc, $query_6star_record);
      if (mysqli_num_rows($data_6star_date) == 2) {
        $fullstar_time++;
        array_push($fullstar_data, $row_battle['battle_date']);
      }
    }
    if ($fullstar_time > 0) {
      $fullstar_user_IDs[$user_id] = $fullstar_time;
      $fullstar_data_IDs[$user_id] = $fullstar_data;
    }

    $query_war_record = "SELECT bi.battle_date, bm.defense_pos, bm.attack_grade, bm.defense_grade, bf.win_star, bf.crush_rate" .
                        " FROM clan_battle_info AS bi" .
                        " INNER JOIN clan_battle_map as bm USING (battle_id)" .
                        " INNER JOIN clan_battle_fight as bf USING (fight_id)" .
                        " WHERE attack=1 AND attack_pos=" . $user_id .
                        " AND battle_date>='" . $from_date .
                        "' AND battle_date<='" . $to_date . "'";
    $data_war_record = mysqli_query($dbc, $query_war_record);
    $fight_done = 0;
    $fight_miss = 0;
    $unlucky_fight = 0;
    $unlucky_data = array();
    while ($row_war_record = mysqli_fetch_array($data_war_record)) {
      $fight_done ++;
      $user_grade = $row_war_record['attack_grade'];
      $defender_grade = $row_war_record['defense_grade'];
      if ($row_war_record['defense_pos']==0) {
        $fight_miss++;
      }
      $win_star += $row_war_record['win_star'];
      if ($defender_grade < $user_grade AND $defender_grade >= 10) {
        $win_star--;
      }
      if ($row_war_record['crush_rate'] == 0.99) {
        $unlucky_fight ++;
        array_push($unlucky_data, $row_war_record['battle_date']);
      }
      $crush_rate += $row_war_record['crush_rate'];
    }
    $star_average = round(($win_star / $fight_done),1);
    $crush_average = round(($crush_rate / $fight_done * 100),1);
    $star_average_real = round(($win_star / ($fight_done - $fight_miss)),1);
    $crush_average_real = round(($crush_rate / ($fight_done - $fight_miss) * 100),1);
    if ($unlucky_fight > 0) {
      $unlucky_user_IDs[$user_id] = $unlucky_fight;
      $unlucky_data_IDs[$user_id] = $unlucky_data;
    }

    $user_data = array();
    if ( $fight_done >= $amount_month) {
      if ($user_grade > 9) {
        $user_high_star_IDs[$user_id] = $star_average;
        $user_high_crush_IDs[$user_id] = $crush_average;
      }
      elseif ($user_grade == 9) {
        $user_middle_star_IDs[$user_id] = $star_average;
        $user_middle_crush_IDs[$user_id] = $crush_average;
      }
      else {
        $user_junior_star_IDs[$user_id] = $star_average;
        $user_junior_crush_IDs[$user_id] = $crush_average;
      }

      array_push($user_data, $user_name);
      array_push($user_data, $star_average_real);
      array_push($user_data, $crush_average_real);
      array_push($user_data, $user_grade);
      array_push($user_data, $fight_done / 2);
      array_push($user_data, $fight_miss);
      $user_data_IDs[$user_id] = $user_data;
    }

    $query_war_record = "SELECT bi.battle_id, bm.attack_pos, bf.win_star, bf.crush_rate" .
                        " FROM clan_battle_info AS bi" .
                        " INNER JOIN clan_battle_map as bm USING (battle_id)" .
                        " INNER JOIN clan_battle_fight as bf USING (fight_id)" .
                        " WHERE attack=0 AND defense_pos=" . $user_id .
                        " AND battle_date>='" . $from_date .
                        "' AND battle_date<='" . $to_date . "'";
    $data_war_record = mysqli_query($dbc, $query_war_record);
    $defense_time = 0;
    $aver_lose_star = 0;
    $aver_crush_rate = 0;
    while ($row_war_record = mysqli_fetch_array($data_war_record)) {
      $defense_time ++;
      $aver_lose_star  += $row_war_record['win_star'];
      $aver_crush_rate += $row_war_record['crush_rate'];
    }
    $aver_lose_star  = round(($aver_lose_star / $defense_time),1);
    $aver_crush_rate  = $aver_crush_rate / $defense_time;
    $user_data = array();
    if ($defense_time != 0 AND $defense_time >= $amount_month * 1.5) {
      $defense_star_IDs[$user_id] = $aver_lose_star;
      array_push($user_data, $user_name);
      array_push($user_data, $defense_time);
      array_push($user_data, $aver_lose_star);
      array_push($user_data, $aver_crush_rate);
      $defense_data_IDs[$user_id] = $user_data;
    }
  }
  arsort($user_high_star_IDs);
  $user_high_star_IDs = array_slice($user_high_star_IDs, 0, 5, true);
  arsort($user_middle_star_IDs);
  $user_middle_star_IDs = array_slice($user_middle_star_IDs, 0, 5, true);
  arsort($user_junior_star_IDs);
  $user_junior_star_IDs = array_slice($user_junior_star_IDs, 0, 5, true);

  arsort($fullstar_user_IDs);
  echo '<br>六星杀手（同本）<br>';
  echo '<table border="1">';
  echo '<tr><td>成员</td><td>次数</td><td>日期</td></tr>';
  foreach ($fullstar_user_IDs as $key => $value) {
    $query_user_name = "SELECT user_name FROM clan_user_info WHERE user_id=" . $key;
    $data_user_name = mysqli_query($dbc, $query_user_name);
    $row_user_name = mysqli_fetch_array($data_user_name);
    echo '<tr><td><a href="viewplayer.php?user_id=' . $key . '">' . $row_user_name['user_name'] . '</a> </td>';
    echo '<td>' . $value . '</td>';
    echo '<td>';
    foreach ($fullstar_data_IDs[$key] as $key_data => $value_data) {
      echo $value_data . ' ';
    }
    echo '</td>';
    echo '</tr>';
  }
  echo '</table>';

  arsort($unlucky_user_IDs);
  echo '<br>悲摧战士（99%摧毁率）<br>';
  echo '<table border="1">';
  echo '<tr><td>成员</td><td>次数</td><td>日期</td></tr>';
  foreach ($unlucky_user_IDs as $key => $value) {
    $query_user_name = "SELECT user_name FROM clan_user_info WHERE user_id=" . $key;
    $data_user_name = mysqli_query($dbc, $query_user_name);
    $row_user_name = mysqli_fetch_array($data_user_name);
    echo '<tr><td><a href="viewplayer.php?user_id=' . $key . '">' . $row_user_name['user_name'] . '</a> </td>';
    echo '<td>' . $value . '</td>';
    echo '<td>';
    foreach ($unlucky_data_IDs[$key] as $key_data => $value_data) {
      echo $value_data . ' ';
    }
    echo '</td>';
    echo '</tr>';
  }
  echo '</table>';

  arsort($absent_user_IDs);
  echo '<br>未战斗（参战不攻）<br>';
  echo '<table border="1">';
  echo '<tr><td>成员</td><td>次数</td></tr>';
  foreach ($absent_user_IDs as $key => $value) {
    $query_user_name = "SELECT user_name FROM clan_user_info WHERE user_id=" . $key;
    $data_user_name = mysqli_query($dbc, $query_user_name);
    $row_user_name = mysqli_fetch_array($data_user_name);
    echo '<tr><td><a href="viewplayer.php?user_id=' . $key . '">' . $row_user_name['user_name'] . '</a> </td>';
    echo '<td>' . $value . '次</td>';
    echo '</tr>';
  }
  echo '</table>';

  asort($attend_user_IDs);
  echo '<br>休眠者（参战率<50%）<br>';
  echo '<table border="1">';
  echo '<tr><td>成员</td><td>缺席</td></tr>';
  foreach ($attend_user_IDs as $key => $value) {
    $query_user_name = "SELECT user_name FROM clan_user_info WHERE user_id=" . $key;
    $data_user_name = mysqli_query($dbc, $query_user_name);
    $row_user_name = mysqli_fetch_array($data_user_name);
    echo '<tr><td><a href="viewplayer.php?user_id=' . $key . '">' . $row_user_name['user_name'] . '</a> </td>';
    echo '<td>' . ($amount_month - $value / 2) . '/' . $amount_month . '</td>';
    echo '</tr>';
  }
  echo '</table>';

  echo '<br>进攻夺星排名（10本以下降本攻击减1星）';
  echo '<br>高本（>9）进攻夺星排名<br>';
  echo '<table border="1">';
  echo '<tr><td>成员</td><td>本位</td><td>参战</td><td>未战斗次数</td><td>战绩</td><td>摧毁率</td><td>实际战绩</td><td>实际摧毁率</td></tr>';
  foreach ($user_high_star_IDs as $key => $value) {
    echo '<tr><td><a href="viewplayer.php?user_id=' . $key . '">' . $user_data_IDs[$key][0] . '</a> </td>';
    echo '<td>' . $user_data_IDs[$key][3] . '</td>';
    echo '<td>' . $user_data_IDs[$key][4] . '/' . $amount_month . '</td>';
    echo '<td>' . $user_data_IDs[$key][5] . '</td>';
    echo '<td>' . $value . '&#9733</td>';
    echo '<td>' . $user_high_crush_IDs[$key] . '</td>';
    echo '<td>' . $user_data_IDs[$key][1] . '&#9733</td>';
    echo '<td>' . $user_data_IDs[$key][2] . '</td>';
    echo '</tr>';
  }
  echo '</table>';
  echo '<br>中本（=9）进攻夺星排名<br>';
  echo '<table border="1">';
  echo '<tr><td>成员</td><td>本位</td><td>参战</td><td>未战斗次数</td><td>战绩</td><td>摧毁率</td><td>实际战绩</td><td>实际摧毁率</td></tr>';
  foreach ($user_middle_star_IDs as $key => $value) {
    echo '<tr><td><a href="viewplayer.php?user_id=' . $key . '">' . $user_data_IDs[$key][0] . '</a> </td>';
    echo '<td>' . $user_data_IDs[$key][3] . '</td>';
    echo '<td>' . $user_data_IDs[$key][4] . '/' . $amount_month . '</td>';
    echo '<td>' . $user_data_IDs[$key][5] . '</td>';
    echo '<td>' . $value . '&#9733</td>';
    echo '<td>' . $user_middle_star_IDs[$key] . '</td>';
    echo '<td>' . $user_data_IDs[$key][1] . '&#9733</td>';
    echo '<td>' . $user_data_IDs[$key][2] . '</td>';
    echo '</tr>';
  }
  echo '</table>';
  echo '<br>低本（<9）进攻夺星排名<br>';
  echo '<table border="1">';
  echo '<tr><td>成员</td><td>本位</td><td>参战</td><td>未战斗次数</td><td>战绩</td><td>摧毁率</td><td>实际战绩</td><td>实际摧毁率</td></tr>';
  foreach ($user_junior_star_IDs as $key => $value) {
    echo '<tr><td><a href="viewplayer.php?user_id=' . $key . '">' . $user_data_IDs[$key][0] . '</a> </td>';
    echo '<td>' . $user_data_IDs[$key][3] . '</td>';
    echo '<td>' . $user_data_IDs[$key][4] . '/' . $amount_month . '</td>';
    echo '<td>' . $user_data_IDs[$key][5] . '</td>';
    echo '<td>' . $value . '&#9733</td>';
    echo '<td>' . $user_junior_star_IDs[$key] . '</td>';
    echo '<td>' . $user_data_IDs[$key][1] . '&#9733</td>';
    echo '<td>' . $user_data_IDs[$key][2] . '</td>';
    echo '</tr>';
  }
  echo '</table>';

  asort($defense_star_IDs);
  $defense_star_IDs = array_slice($defense_star_IDs, 0, 5, true);
  echo '<br>铜墙铁壁<br>';
  echo '<table border="1">';
  echo '<tr><td>成员</td><td>被攻击次数</td><td>平均失星</td><td>平均摧毁率</td></tr>';
  foreach ($defense_star_IDs as $key => $value) {
    echo '<tr><td><a href="viewplayer.php?user_id=' . $key . '">' . $defense_data_IDs[$key][0] . '</a> </td>';
    echo '<td>' . $defense_data_IDs[$key][1] . '</td>';
    echo '<td>' . number_format($defense_data_IDs[$key][2] , 1) . '</td>';
    echo '<td>' . number_format($defense_data_IDs[$key][3] * 100, 1). '%</td>';
    echo '</tr>';
  }
  echo '</table>';

  mysqli_close($dbc);
?>

<?php
  //Insert the page footer
  require_once('footer.php');
?>
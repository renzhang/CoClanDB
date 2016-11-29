<?php

  // Insert the page header
  $page_title = "部落战";
  require_once('header.php');

  require_once('appvars.php');
  require_once('connectvars.php');

  // Show the navigation menu
  require_once('navmenu.php');

  echo '<br>';
  echo '<br><a href="battlelist.php?id=0">最近一战</a>';
  echo ' | ';
  echo '<a href="battlelist.php?id=1">最近五战</a>';
  echo ' | ';
  echo '<a href="battlelist.php?id=2">最近十战</a>';
  echo '<br>';

  // Connect to the database
  $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

  // Calculate Monty Result list
  $query_battle_info = "SELECT battle_id, battle_date, battle_size, star_win, star_lose, crushrate_win, crushrate_lose FROM `clan_battle_info`";
  $data_battle_info = mysqli_query($dbc, $query_battle_info);
  $row_month = '0';
  $month_start_date = '0';
  $month_end_date = '0';
  $month_win = 0;
  $month_draw = 0;
  $month_lose = 0;

  echo '<table>';
  echo '<table border="2">';
  echo '<br>月度记录<br>';
  while($row_battle_info = mysqli_fetch_array($data_battle_info)) {
    $battle_date = $row_battle_info['battle_date'];
    $battle_month = date('M', strtotime($battle_date));

    $battle_size = $row_battle_info['battle_size'];
    $battle_id = $row_battle_info['battle_id'];
    $link_query_date = "battledata.php?id=" . $battle_id . "&side=1";

    $star_attack = $row_battle_info['star_win'];
    $star_defense = $row_battle_info['star_lose'];
    $crushrate_attack = $row_battle_info['crushrate_win'];
    $crushrate_defense = $row_battle_info['crushrate_lose'];

    if ($battle_month != $row_month) {
      if ($row_month != '0') {
        echo '<td>' . $amount_month . '战 </td>';
        echo '<td>' . $month_win . '胜 </td>';
        echo '<td>' . $month_draw . '平 </td>';
        echo '<td>' . $month_lose . '负</td>';
        $month_link = "battlelist.php?from_date=" . $month_start_date . "&to_date=" . $month_end_date;
        echo '<td><a href="' . $month_link . '">' . $row_month . '</a></td>';
        echo '</tr>';
      }
      $row_month = $battle_month;
      $month_start_date = $battle_date;
      $month_win = 0;
      $month_draw = 0;
      $month_lose = 0;
      $amount_month = 1;
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
      echo '<tr><td>' . $row_month . ': </td>';
    }
    else {
      $month_end_date = $battle_date;
      $amount_month++;
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
  }
  echo '<td>' . $amount_month . '战 </td>';
  echo '<td>' . $month_win . '胜 </td>';
  echo '<td>' . $month_draw . '平 </td>';
  echo '<td>' . $month_lose . '负</td>';
  $month_link = "battlelist.php?from_date=" . $month_start_date . "&to_date=" . $month_end_date;
  echo '<td><a href="' . $month_link . '">' . $row_month . '</a></td>';
  echo '</tr>';
  echo '</table><br>';

  # get how many battles are listed
  # 0: last battle
  # 1: last 5 battle
  # 2: last 10 battle

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

  $total_win = 0;
  $total_draw = 0;
  $total_lose = 0;

  // Grab the profile data from the database
  mysqli_set_charset($dbc, 'utf8');
  $query_battle = "SELECT battle_id, battle_date, battle_size, star_win, star_lose, crushrate_win, crushrate_lose" .
                  " FROM `clan_battle_info`" .
                  " WHERE battle_date >= '" . $from_date . "'" .
                  " AND battle_date <= '" . $to_date . "'";
  $data_battle = mysqli_query($dbc, $query_battle);
  echo '<table>';
  echo '<tr><td>日期</td><td>规模</td><td>战绩</td></tr>';
  while ($row_battle = mysqli_fetch_array($data_battle)) {
    echo '<tr><td><a href="battledata.php?id=' . $row_battle['battle_id'] . '&side=1">' . $row_battle['battle_date'] . '</a></td>';
    echo '<td>' . $row_battle['battle_size'] . '人</td>';
    echo '<td>';
    $star_attack = $row_battle['star_win'];
    $star_defense = $row_battle['star_lose'];
    $crushrate_attack = $row_battle['crushrate_win'];
    $crushrate_defense = $row_battle['crushrate_lose'];

    if ($star_attack < $star_defense) {
        echo '负 [' . $star_attack . ' : ' . $star_defense;
        $total_lose++;
    }
    elseif ($star_attack > $star_defense) {
        echo '胜 [' . $star_attack . ' : ' . $star_defense;
        $total_win++;
    }
    elseif ($crushrate_attack > $crushrate_defense) {
        echo '胜 [' . $star_attack . ' : ' . $star_defense;
        $total_win++;
    }
    elseif ($crushrate_attack < $crushrate_defense) {
        echo '负 [' . $star_attack . ' : ' . $star_defense;
        $total_lose++;
    }
    else {
        echo '平 [' . $star_attack . ' : ' . $star_defense;
        $total_draw++;
    }
    echo ']  (' . number_format($crushrate_attack * 100, 2) . '% : ' . number_format($crushrate_defense * 100, 2) . '%)<br>';

    echo '</td>';
    echo '</tr>';
  }
  echo '</table>';
  mysqli_close($dbc);
  echo "<br>From " . $from_date . " to " . $to_date . ":" ;
  echo $battle_show . '战 ' . $total_win . '胜 ' . $total_draw . '平 ' . $total_lose . '负<br>';
?>

<?php
  //Insert the page footer
  require_once('footer.php');
?>
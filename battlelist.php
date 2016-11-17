<?php

  // Insert the page header
  $page_title = "部落战";
  require_once('header.php');

  require_once('appvars.php');
  require_once('connectvars.php');

  // Show the navigation menu
  require_once('navmenu.php');

  # get how many battles are listed
  # 0: last battle
  # 1: last 5 battle
  # 2: last 10 battle
  $disp_id = (int) $_GET['id'];

  $battle_show = ($disp_id == 0) ? 1 : $disp_id * 5;
  echo '<br>';
  echo '<br><a href="battlelist.php?id=0">最近一战</a>';
  echo ' | ';
  echo '<a href="battlelist.php?id=1">最近五战</a>';
  echo ' | ';
  echo '<a href="battlelist.php?id=2">最近十战</a>';
  echo '<br><br>';

  // Connect to the database
  $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

  // Grab the profile data from the database
  mysqli_set_charset($dbc, 'utf8');
  $query = "SELECT battle_id, battle_date, battle_size, star_win, star_lose, crushrate_win, crushrate_lose" .
           " FROM `clan_battle_info` ORDER BY battle_date DESC LIMIT " . $battle_show;
  $data = mysqli_query($dbc, $query);
  echo '<table>';
  echo '<tr><td>日期</td><td>规模</td><td>战绩</td></tr>';
  while ($row = mysqli_fetch_array($data)) {
    echo '<tr><td><a href="battledata.php?id=' . $row['battle_id'] . '&side=1">' . $row['battle_date'] . '</a></td>';
    echo '<td>' . $row['battle_size'] . '人</td>';
    echo '<td>';
    $star_attack = 0;
    $star_defense = 0;


    $query_pos = "SELECT attack_pos FROM clan_battle_map " . 
                 "WHERE battle_id=" . $row['battle_id'] .
                 " AND attack=1 AND fight_id&1";
    $data_pos = mysqli_query($dbc, $query_pos);
    $i = 0;
    while ($row_pos = mysqli_fetch_array($data_pos)) {
        $i ++;
        $query_attack = "SELECT bf.win_star, bf.crush_rate " . 
                        "FROM `clan_battle_map` AS bm " . 
                        "INNER JOIN `clan_battle_fight` AS bf USING (fight_id) " . 
                        "WHERE battle_id=" . $row['battle_id'] . 
                        " AND ATTACK=1 AND defense_pos=" . $i;
        $data_attack  = mysqli_query($dbc, $query_attack);
        $star_pos = 0;
        while ($row_attack = mysqli_fetch_array($data_attack)) {
            if ($row_attack['win_star'] > $star_pos) {
                $star_pos = $row_attack['win_star'];
            }
        }
        $star_attack += $star_pos;

        $query_defense = "SELECT bf.win_star, bf.crush_rate " .
                         "FROM `clan_battle_map` AS bm " .
                         "INNER JOIN `clan_battle_fight` AS bf USING (fight_id) " .
                         "WHERE battle_id=" . $row['battle_id'] .
                         " AND ATTACK=0 AND defense_pos=" . $row_pos['attack_pos'];
        $data_defense  = mysqli_query($dbc, $query_defense);
        $star_pos = 0;
        while ($row_defense = mysqli_fetch_array($data_defense)) {
            if ($row_defense['win_star'] > $star_pos) {
                $star_pos = $row_defense['win_star'];
            }
        }
        $star_defense += $star_pos;
    }

    if ($star_attack < $star_defense) {
        echo '负 [' . $star_attack . ' : ' . $star_defense . ']';
    }
    elseif ($star_attack > $star_defense) {
        echo '胜 [' . $star_attack . ' : ' . $star_defense . ']';
    }
    else {
        echo '平 [' . $star_attack . ' : ' . $star_defense . ']';
    }

    echo '</td>';
    echo '</tr>';
  }
  echo '</table>';
  mysqli_close($dbc);
?>

<?php
  //Insert the page footer
  require_once('footer.php');
?>
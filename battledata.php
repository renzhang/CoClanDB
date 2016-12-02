<?php

  // Insert the page header
$page_title = "部落战";
require_once('header.php');

require_once('appvars.php');
require_once('connectvars.php');

  // Show the navigation menu
require_once('navmenu.php');

$id_query = $_GET['id'];
$side_query = $_GET['side'];

  // Connect to the database to add new battle information
$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

  // To query the last battle ID "0" or set the query battle ID, get battle size
if ($id_query == 0) {
    // Get battle ID of the last
  $query_last_battle = 'SELECT battle_id, battle_size, opponent_clan_id FROM `clan_battle_info` ORDER BY battle_date DESC LIMIT 1';
  $data_last_battle  = mysqli_query($dbc, $query_last_battle);
  $row_last_battle   = mysqli_fetch_array($data_last_battle);
  $battle_id = $row_last_battle['battle_id'];
  $battle_size = $row_last_battle['battle_size'];
  $battle_opponent = $row_last_battle['opponent_clan_id'];

  // calcluate clanwar result from clan_battle_fight
  $star_attack = 0;
  $star_defense = 0;
  $crushrate_attack = 0.0;
  $crushrate_defense = 0.0;

  $query_pos = "SELECT attack_pos FROM clan_battle_map" .
               " WHERE battle_id=" . $battle_id . " AND attack=1 AND fight_id&1";
  $data_pos = mysqli_query($dbc, $query_pos);
  $i = 0;
  while ($row_pos = mysqli_fetch_array($data_pos)) {
    $i ++;
    $query_attack = "SELECT bf.win_star, bf.crush_rate" .
                    " FROM `clan_battle_map` AS bm" .
                    " INNER JOIN `clan_battle_fight` AS bf USING (fight_id)" .
                    " WHERE battle_id=" . $battle_id .
                    " AND ATTACK=1 AND defense_pos=" . $i;
    $data_attack  = mysqli_query($dbc, $query_attack);
    $star_pos = 0;
    $crushrate_pos = 0.0;
    while ($row_attack = mysqli_fetch_array($data_attack)) {
      if ( $row_attack['win_star'] > $star_pos ) {
        $star_pos = $row_attack['win_star'];
        $crushrate_pos = $row_attack['crush_rate'];
      }
      elseif ($row_attack['win_star'] == $star_pos) {
        if ($row_attack['crush_rate'] > $crushrate_pos) {
          $crushrate_pos = $row_attack['crush_rate'];
        }
      }
    }
    $star_attack += $star_pos;
    $crushrate_attack += $crushrate_pos;

    $query_defense = "SELECT bf.win_star, bf.crush_rate" .
                     " FROM `clan_battle_map` AS bm" .
                     " INNER JOIN `clan_battle_fight` AS bf USING (fight_id)" .
                     " WHERE battle_id=" . $battle_id .
                     " AND ATTACK=0 AND defense_pos=" . $row_pos['attack_pos'];
    $data_defense  = mysqli_query($dbc, $query_defense);

    $star_pos = 0;
    $crushrate_pos = 0.0;
    while ($row_defense = mysqli_fetch_array($data_defense)) {
      if ($row_defense['win_star'] > $star_pos) {
        $star_pos = $row_defense['win_star'];
        $crushrate_pos = $row_defense['crush_rate'];
      }
      elseif ($row_defense['win_star'] == $star_pos) {
        if ($row_defense['crush_rate'] > $crushrate_pos) {
            $crushrate_pos = $row_defense['crush_rate'];
        }
      }
    }
    $star_defense += $star_pos;
    $crushrate_defense += $crushrate_pos;
  }

  $crushrate_attack = number_format($crushrate_attack / $i, 4);
  $crushrate_defense = number_format($crushrate_defense / $i, 4);
  echo '<br>';

  # Update clanwar result to clan_battle_info
  $result_battle_update = 1;
  $query_battle_update = "UPDATE clan_battle_info SET star_win= " .
                          $star_attack . " WHERE battle_id=" . $battle_id;
  $result_battle_update = $result_battle_update && mysqli_query($dbc, $query_battle_update);
  $query_battle_update = "UPDATE clan_battle_info SET crushrate_win= " .
                          $crushrate_attack . " WHERE battle_id=" . $battle_id;
  $result_battle_update = $result_battle_update && mysqli_query($dbc, $query_battle_update);
  $query_battle_update = "UPDATE clan_battle_info SET star_lose= " .
                          $star_defense . " WHERE battle_id=" . $battle_id;
  $result_battle_update = $result_battle_update && mysqli_query($dbc, $query_battle_update);
  $query_battle_update = "UPDATE clan_battle_info SET crushrate_lose= " .
                          $crushrate_defense . " WHERE battle_id=" . $battle_id;
  $result_battle_update = $result_battle_update && mysqli_query($dbc, $query_battle_update);
  if ($result_battle_update) {
    echo "Result Update Success!<br>";
  }
  else {
    echo "Result Update Fail!<br>";
  }

  if ($star_attack < $star_defense) {
    echo '负 [' . $star_attack . ' : ' . $star_defense . ']';
  }
  elseif ($star_attack > $star_defense) {
    echo '胜 [' . $star_attack . ' : ' . $star_defense . ']';
  }
  elseif ($crushrate_attack  > $crushrate_defense) {
    echo '胜 [' . $star_attack . ' : ' . $star_defense . ']';
  }
  elseif ($crushrate_attack  < $crushrate_defense) {
    echo '负 [' . $star_attack . ' : ' . $star_defense . ']';
  }
  else {
    echo '平 [' . $star_attack . ' : ' . $star_defense . ']';
  }
  echo ' (' . number_format($crushrate_attack * 100, 2) . '% : ' . number_format($crushrate_defense * 100, 2) . '%)<br>';
}
else {
  // Get clanwar result from clan_battle_info
  $battle_id = $id_query;
  $query_battle = 'SELECT battle_size,  opponent_clan_id, star_win, star_lose, crushrate_win, crushrate_lose' .
                  ' FROM `clan_battle_info` WHERE battle_id=' . $battle_id;
  $data_battle  = mysqli_query($dbc, $query_battle);
  $row_battle   = mysqli_fetch_array($data_battle);

  $battle_size = $row_battle['battle_size'];
  $battle_opponent = $row_battle['opponent_clan_id'];
  $star_attack = $row_battle['star_win'];
  $star_defense = $row_battle['star_lose'];
  $crushrate_attack = $row_battle['crushrate_win'] * 100;
  $crushrate_defense = $row_battle['crushrate_lose'] * 100;

  echo '<br>';
  // Display battle result
  if ($star_attack < $star_defense) {
    echo '负 [' . $star_attack . ' : ' . $star_defense . ']';
  }
  elseif ($star_attack > $star_defense) {
    echo '胜 [' . $star_attack . ' : ' . $star_defense . ']';
  }
  elseif ($crushrate_attack  > $crushrate_defense) {
    echo '胜 [' . $star_attack . ' : ' . $star_defense . ']';
  }
  elseif ($crushrate_attack  < $crushrate_defense) {
    echo '负 [' . $star_attack . ' : ' . $star_defense . ']';
  }
  else {
    echo '平 [' . $star_attack . ' : ' . $star_defense . ']';
  }
  echo ' (' . $crushrate_attack . '% : ' . $crushrate_defense . '%)<br>';
}
?>

<html>
<body>
  <table border="2">
    <tr><td colspan="9">
      <?php
      # '1' for attack record, '0' for defense record
      if ($side_query == 1) {
        echo '<a href="battledata.php?side=0&id=' . $id_query . '">攻</a>';
      }
      else {
        echo '<a href="battledata.php?side=1&id=' . $id_query . '">守</a>';
      }
      ?>
    </td></tr>
    <tr>
      <?php
      # '1' for attack record, '0' for defense record
      if ($side_query == 1) {
        echo '<td>No.#</td><td>攻方</td><td>级别</td><td>守方Pos</td><td>级别</td><td>STAR</td><td>Crush</td>';
      }
      else {
        echo '<td>No.#</td><td>攻方Pos</td><td>级别</td><td>守方</td><td>级别</td><td>STAR</td><td>Crush</td>';
      }
      ?>
    </tr>
    <?php
    // Get saved battle fights data
    $query_battle_fight = "SELECT bm.fight_id, bm.attack_pos, bm.defense_pos, bf.win_star, bf.crush_rate" .
      " FROM `clan_battle_map` AS bm" .
      " INNER JOIN `clan_battle_fight` AS bf USING (fight_id)" .
      " WHERE attack=" . $side_query .
      " AND battle_id=" . $battle_id;
    $data_battle_fight = mysqli_query($dbc, $query_battle_fight);

    $pos=0;
    while ($row_battle_fight = mysqli_fetch_array($data_battle_fight)) {
      $pos++;

      $fightid = $row_battle_fight['fight_id'];
      $attacker = $row_battle_fight['attack_pos'];
      $defender = $row_battle_fight['defense_pos'];
      $star = $row_battle_fight['win_star'];
      $crush = $row_battle_fight['crush_rate']*100;

      // Get username from clan user id: Attack from attacker / Defense from defender
      $query_user_name = "SELECT ui.user_name, ug.grade, ug.levels, ug.cup, ug.star " . 
        "FROM `clan_user_info` AS ui " . 
        "INNER JOIN `clan_user_grade` AS ug USING (user_id) " . 
        "WHERE user_id=";

      if ($side_query == 1) {
        $query_user_name .= $attacker;
      }
      else {
        $query_user_name .= $defender;
      }
      mysqli_set_charset($dbc, 'utf8');
      $data_user_name = mysqli_query($dbc, $query_user_name);
      $row_user_name = mysqli_fetch_array($data_user_name);
      $fighter_name = $row_user_name['user_name'];
      $fighter_grade = (int)$row_user_name['grade'];
      $fighter_str = round(($row_user_name['star'] / $row_user_name['levels']),1);

      // Query opponent grade
      $query_opponent = "SELECT grade, levels, cup, star FROM `clan_opponent` " . 
        "WHERE opponent_clan_id='" . $battle_opponent. "' AND opponent_user_id=";
      if ($side_query == 1) {
        $query_opponent .= $defender;
      }
      else {
        $query_opponent .= $attacker;
      }
      $data_opponent = mysqli_query($dbc, $query_opponent);
      $row_opponent = mysqli_fetch_array($data_opponent);
      $opponent_grade = (int)$row_opponent['grade'];
      $opponent_str = round(($row_opponent['star'] / $row_opponent['levels']), 1);

      $attacker_name  = ($side_query == 1) ? $fighter_name : $attacker;
      $attacker_grade = ($side_query == 1) ? $fighter_grade : $opponent_grade;
      $attacker_str = ($side_query == 1) ? $fighter_str : $opponent_str;
      $defender_name  = ($side_query == 1) ? $defender : $fighter_name;
      $defender_grade = ($side_query == 1) ? $opponent_grade : $fighter_grade;
      $defender_str = ($side_query == 1) ? $opponent_str : $fighter_str;

      // Create result list table
      echo '<tr>';
      if ($pos % 2 != 0) {
        echo '<th rowspan="2">' . ($pos + 1 ) / 2 . '</th>';
        echo '<th rowspan="2" >' . $attacker_name . '</th>';
      }
      echo '<td>';
      if ($attacker_grade > $defender_grade) {
        echo '<font color="green">' . $attacker_grade . '</font>';
      }
      elseif ($attacker_grade < $defender_grade){
        echo '<font color="red">' . $attacker_grade . '</font>';
      }
      else {
        echo '<font color="brown">' . $attacker_grade . '</font>';
      }
      echo '</td>';
      // echo '<td>';
      // if ($attacker_str > $defender_str) {
      //   echo '<font color="green">' . $attacker_str . '</font>';
      // }
      // else {
      //   echo '<font color="red">' . $attacker_str . '</font>';
      // }
      // echo '</td>';
      echo '<td>' . $defender_name . '</td>';
      echo '<td>';
      if ($attacker_grade < $defender_grade) {
        echo '<font color="green">' . $defender_grade . '</font>';
      }
      elseif ($attacker_grade > $defender_grade) {
        echo '<font color="red">' . $defender_grade . '</font>';
      }
      else {
        echo '<font color="brown">' . $defender_grade . '</font>';
      }
      echo '</td>';
      // echo '<td>';
      // if ($attacker_str < $defender_str) {
      //   echo '<font color="green">' . $defender_str . '</font>';
      // }
      // else {
      //   echo '<font color="red">' . $defender_str . '</font>';
      // }
      // echo '</td>';
      echo '<td>' . $star . '</td>';
      echo '<td>' . $crush . '%</td>';
      echo '</tr>';
    }
    ?>
  </table>
</body>
</html>


<?php
mysqli_close($dbc);
?>

<?php
  //Insert the page footer
require_once('footer.php');
?>
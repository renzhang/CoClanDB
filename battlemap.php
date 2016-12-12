<?php

  // Insert the page header
  $page_title = "部落战";
  require_once('header.php');

  require_once('appvars.php');
  require_once('connectvars.php');

  // Show the navigation menu
  require_once('navmenu.php');
?>

<?php
// Connect to the database to add new battle information
$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if (isset($_POST['opponent_id'])) {
  $opponent_id = $_POST['opponent_id'];
  $battle_date = $_POST['battle_date'];
  $battle_size = $_POST['battle_size'];
  // Insert Battle record into table
  $query_add_battle = "INSERT INTO `clan_battle_info`" .
                      " (battle_date, opponent_clan_id, battle_size)" .
                      " VALUES ('" . $battle_date ."', '". $opponent_id . "'," . $battle_size . ")";
  $result_add_battle = mysqli_query($dbc, $query_add_battle);
  $query_battle_add = "SELECT battle_id FROM `clan_battle_info` ORDER BY battle_id DESC LIMIT 1";
  $data_battle_add = mysqli_query($dbc, $query_battle_add);
  $row_battle_add = mysqli_fetch_array($data_battle_add);
  $battle_id = $row_battle_add['battle_id'];

  // $query_map_postion = "SELECT attack_pos FROM clan_battle_map" .
  //                      " WHERE battle_size=" . $battle_size

  // Insert empty position into battle map table
  for ($i=1; $i<=$battle_size; $i++) {
    // add opponent user info
    $query_add_opponent = "INSERT INTO `clan_opponent` (opponent_clan_id, opponent_user_id, grade)" .
                          " VALUES ('" . $opponent_id. "', " . $i . ", 9 )";
    $result_add_opponent = mysqli_query($dbc, $query_add_opponent);

    // add 1st attack into battle map table
    $query_add = "INSERT INTO `clan_battle_map`" .
                 " (battle_id, attack, attack_pos, attack_grade, defense_pos)" .
                 " VALUES (" . $battle_id . ", 1, 0, 0, 0)";
    $result_add = mysqli_query($dbc, $query_add);
    // Get fight_id from newly added record to udpate fight result database
    $query_fightid = "SELECT fight_id FROM clan_battle_map ORDER BY fight_id DESC LIMIT 1";
    $data_fightid = mysqli_query($dbc, $query_fightid);
    $row_fightid = mysqli_fetch_array($data_fightid);
    $update_fightid = $row_fightid['fight_id'];
    // add fight colunm into fight table
    $query_addfight = "INSERT INTO `clan_battle_fight`" .
                      " (fight_id, win_star, crush_rate) VALUES (". $update_fightid . ", 0, 0.0)";
    $result_add = mysqli_query($dbc, $query_addfight);

    // add 2nd attack into battle map table
    $result_add = mysqli_query($dbc, $query_add);
    // Get fight_id from newly added record to udpate fight result database
    $query_fightid = "SELECT fight_id FROM clan_battle_map ORDER BY fight_id DESC LIMIT 1";
    $data_fightid = mysqli_query($dbc, $query_fightid);
    $row_fightid = mysqli_fetch_array($data_fightid);
    $update_fightid = $row_fightid['fight_id'];
    // add fight colunm into fight table
    $query_addfight = "INSERT INTO `clan_battle_fight`" .
                      " (fight_id, win_star, crush_rate) VALUES (". $update_fightid . ", 0, 0.0)";
    $result_add = mysqli_query($dbc, $query_addfight);

    // add 1st defense into battle map table
    $query_add = "INSERT INTO `clan_battle_map`" .
                 " (battle_id, attack, attack_pos, attack_grade, defense_pos)" .
                 " VALUES (" . $battle_id . ", 0, " . $i . ", 0, 0)";
    $result_add = mysqli_query($dbc, $query_add);
    // Get fight_id from newly added record to udpate fight result database
    $query_fightid = "SELECT fight_id FROM clan_battle_map ORDER BY fight_id DESC LIMIT 1";
    $data_fightid = mysqli_query($dbc, $query_fightid);
    $row_fightid = mysqli_fetch_array($data_fightid);
    $update_fightid = $row_fightid['fight_id'];
    // add fight colunm into fight table
    $query_addfight = "INSERT INTO `clan_battle_fight`" .
    " (fight_id, win_star, crush_rate) VALUES (". $update_fightid . ", 0, 0.0)";
    $result_add = mysqli_query($dbc, $query_addfight);

    // add 2nd defense into battle map table
    $result_add = mysqli_query($dbc, $query_add);
    // Get fight_id from newly added record to udpate fight result database
    $query_fightid = "SELECT fight_id FROM clan_battle_map ORDER BY fight_id DESC LIMIT 1";
    $data_fightid = mysqli_query($dbc, $query_fightid);
    $row_fightid = mysqli_fetch_array($data_fightid);
    $update_fightid = $row_fightid['fight_id'];
    // add fight colunm into fight table
    $query_addfight = "INSERT INTO `clan_battle_fight`" .
    " (fight_id, win_star, crush_rate) VALUES (". $update_fightid . ", 0, 0.0)";
    $result_add = mysqli_query($dbc, $query_addfight);
  }
  $new_battle = 1;
}
elseif (isset($_GET['id'])) {
  $battle_id = $_GET['id'];
  $new_battle = 0;
}
else {
  echo "Wrong Calling!<br>";
  echo '<a href="index.php">返回</a><br>';
  $battle_id = 0;
  $new_battle = 0;
}

if ($battle_id == 0) {
  $query_battle = 'SELECT battle_id, battle_date , battle_size, opponent_clan_id FROM `clan_battle_info` ORDER BY battle_date DESC LIMIT 1';
}
else {
  $query_battle = 'SELECT battle_id, battle_date , battle_size, opponent_clan_id FROM `clan_battle_info` WHERE battle_id=' . $battle_id;
}
$data_battle  = mysqli_query($dbc, $query_battle);
$row_battle   = mysqli_fetch_array($data_battle);

$battle_id   = $row_battle['battle_id'];
$battle_date = $row_battle['battle_date'];
$battle_size = $row_battle['battle_size'];
$opponent_id = $row_battle['opponent_clan_id'];

echo '<table>';
echo '<tr><td>战时</td><td>对手</td></tr>';
echo '<tr><td>' . $battle_date . '</td><td>' . $opponent_id . '</td></tr>';
echo '</table>';

?>

<html>
<body>
  <form method="post" action="updatemap.php">
    <table>
    <tr><td>战位</td><td>成员</td><td>本位</td><td>对手本位</td></tr>
    <?php
    $query_battle_map = "SELECT bm.fight_id, bm.attack_pos, bm.defense_pos, bf.win_star, bf.crush_rate".
                        " FROM `clan_battle_map` AS bm" .
                        " INNER JOIN `clan_battle_fight` AS bf USING (fight_id)" .
                        " WHERE attack=1" .
                        " AND fight_id&1" .
                        " AND battle_id=" . $battle_id;
    $data_battle_map = mysqli_query($dbc, $query_battle_map);
    $i = 0;
    while($row_battle_map = mysqli_fetch_array($data_battle_map)) {
      $i ++;
        // Grab the clan user data from the database
      mysqli_set_charset($dbc, 'utf8');
      $query_user = "SELECT ui.user_id, ui.user_name, ug.grade" .
                    " FROM `clan_user_info` AS ui" .
                    " INNER JOIN `clan_user_grade` AS ug USING (user_id)";
      if ($new_battle) {
        $query_user .= " WHERE status=1";
      }
      $query_user .= " ORDER BY ug.grade DESC, ug.levels DESC";
      $data_user = mysqli_query($dbc, $query_user);
      $fighter = '';
      while ($row_user = mysqli_fetch_array($data_user)) {
        if ($row_user['user_id'] == $row_battle_map['attack_pos']){
          $pos_grade = $row_user['grade'];
          $fighter .=  '<option selected="selected" value = "' .  $row_user['user_id'] . '">' . $row_user['user_name'] . '</option>';
        }
        else {
          $fighter .=  '<option value = "' .  $row_user['user_id'] . '">' . $row_user['user_name'] . '</option>';
        }
      }
      $fighter .=  '<option value="0">新人</option>';

      $user_grade = '';
      for ($grade=11; $grade>=5; $grade--){
        if ($grade == $pos_grade) {
          $user_grade .= '<option selected="selected" value = "' .  $grade . '">' . $grade . '本</option>';
        }
        else {
          $user_grade .= '<option value="' .  $grade . '">' . $grade . '本</option>';
        }
      }

      $query_grade = "SELECT grade FROM clan_opponent AS co" .
      " INNER JOIN clan_battle_info AS bi USING (opponent_clan_id)" .
      " WHERE battle_id = " . $battle_id . " AND  opponent_user_id =" . $i;
      $data_grade = mysqli_query($dbc, $query_grade);
      if ($row_grade = mysqli_fetch_array($data_grade)) {
        $pos_grade = $row_grade['grade'];
      }
      else {
        $pos_grade = 0;
      }
      $opponent_grade = '';
      for ($grade=11; $grade>=5; $grade--){
        if ($grade == $pos_grade) {
          $opponent_grade .= '<option selected="selected" value = "' .  $grade . '">' . $grade . '本</option>';
        }
        else {
          $opponent_grade .= '<option value="' .  $grade . '">' . $grade . '本</option>';
        }
      }
      echo '<tr><td>' . $i . '</td>';
      echo '<td>';
      echo '<select id="Pos' . $i . '" name="Pos' . $i . '">';
      echo $fighter;
      echo '</select>';
      echo '</td><td>';
      echo '<select id="PosGrade' . $i . '" name="PosGrade' . $i . '">';
      echo $user_grade;
      echo '</select>';
      echo '</td><td>';
      echo '<select id="Opp' . $i . '" name="Opp' . $i . '">';
      echo $opponent_grade;
      echo '</select>';
      echo '</td></tr>';
    }
    ?>
    </table>
    <input type="submit" name="Submit" value="Submit" />
    <input type='hidden' name="id" value='<?php echo "$battle_id";?>' />
  </form>
</body>
</html>


<?php
  mysqli_close($dbc);
?>

<?php
  //Insert the page footer
  require_once('footer.php');
?>
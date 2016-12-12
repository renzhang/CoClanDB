<?php

    // Insert the page header
$page_title = "部落战";
require_once('header.php');

require_once('appvars.php');
require_once('connectvars.php');

    // Show the navigation menu
require_once('navmenu.php');
?>

<html>
<body>
  <table>
    <tr><td>战位</td><td>ID</td><td>成员</td><td>级别</td><td>对位级别</td></tr>
    <?php

    // Connect to the database to add new battle information
    $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if ( isset($_POST['id']) ) {
      // update war id from battlemap.php entry
      $battle_id = $_POST['id'];

      // Get battle size
      $query_battle = 'SELECT battle_size FROM `clan_battle_info`' .
                      ' WHERE battle_id=' . $battle_id;
      $data_battle  = mysqli_query($dbc, $query_battle);
      $row_battle   = mysqli_fetch_array($data_battle);
      $battle_size = $row_battle['battle_size'];

      for ($i=1; $i<=$battle_size; $i++) {
        $userid = $_POST['Pos'. $i];
        $user_grade = $_POST['PosGrade'. $i];
        $opponent_grade = $_POST['Opp'. $i];

        // Update Battle map for the player of position
        $query_fightid = "SELECT fight_id FROM `clan_battle_map`" .
                         " WHERE battle_id=" . $battle_id .
                         " AND attack=1 LIMIT " . (($i-1)*2) . ", 1";
        $data_fightid = mysqli_query($dbc, $query_fightid);
        $row_fightid = mysqli_fetch_array($data_fightid);
        $update_fightid = $row_fightid['fight_id'];
        $query_updatemap = "UPDATE `clan_battle_map`" .
                           " SET attack_pos=" . $userid .
                           " , attack_grade=" . $user_grade .
                           " WHERE fight_id=" . $update_fightid;
        $result_updatemap = mysqli_query($dbc, $query_updatemap);

        $update_fightid++;
        $query_updatemap = "UPDATE `clan_battle_map`" .
                           " SET attack_pos=" . $userid .
                           " , attack_grade=" . $user_grade .
                           " WHERE fight_id=" . $update_fightid;
        $result_updatemap = mysqli_query($dbc, $query_updatemap);

        // Update Opponent Info into table
        $query_opponentid = "SELECT opponent_clan_id FROM clan_battle_info WHERE battle_id=" . $battle_id;
        $data_opponentid = mysqli_query($dbc, $query_opponentid);
        $row_opponentid = mysqli_fetch_array($data_opponentid);

        $query_update_opponent = "UPDATE `clan_opponent`" .
                                 " SET grade= " . $opponent_grade .
                                 " WHERE opponent_clan_id='". $row_opponentid['opponent_clan_id'] . "' AND opponent_user_id=" . $i;
        $result_update_opponent = mysqli_query($dbc, $query_update_opponent);

        echo '<tr><td>' . $i . '</td>';
        echo '<td>' . $userid . '</td>';
        if ($userid != 0) {
          $query_user_name = "SELECT ui.user_name, ug.grade" .
                             " FROM `clan_user_info` AS ui " .
                             " INNER JOIN `clan_user_grade` AS ug USING (user_id)" .
                             " WHERE user_id=" . $userid;
          mysqli_set_charset($dbc, 'utf8');
          $data_user_name = mysqli_query($dbc, $query_user_name);
          $row_user_name = mysqli_fetch_array($data_user_name);
          $user_name = '<a href="viewplayer.php?user_id=' . $row_user_name['user_name'] . '">' . $row_user_name['user_name'] . '</a>';
          $user_grade = $row_user_name['grade'];
        }
        else {
          $user_name = '<a href="viewplayer.php?user_id=' . $userid . '&fight_id=' . ($update_fightid - 1) . '">新人</a>';
          $user_grade = 0;
        }
        echo '<td>' . $user_name . '</td>';
        echo '<td>' . $user_grade . '本</td>';
        echo '<td>' . $opponent_grade . '本</td>';
        echo '</tr>';
      }
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
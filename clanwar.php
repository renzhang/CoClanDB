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

  $opponent_id = $_POST['opponent_id'];
  $battle_date = $_POST['battle_date'];
  $battle_size = $_POST['battle_size'];

  session_start();
  $_SESSION['opponent'] = $opponent_id;
  $_SESSION['date'] = $battle_date;
  $_SESSION['size'] = $battle_size;

  // Connect to the database to add new battle information
  $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

  // Grab the profile data from the database
  mysqli_set_charset($dbc, 'utf8');
  $query = "SELECT ui.user_id, ui.user_name " .
           "FROM `clan_user_info` AS ui " .
           "INNER JOIN `clan_user_grade` AS ug USING (user_id) " .
           "WHERE status=1 " .
           "ORDER BY ug.grade DESC, ug.levels DESC";
  $data = mysqli_query($dbc, $query);
  $fighter = '';
  while ($row = mysqli_fetch_array($data)) {
    $fighter .=  '<option value = "' .  $row['user_id'] . '">' . $row['user_name'] . '</option>';
  }
  $fighter .=  '<option value="0">新人</option>';
  $opponent_grade = '';
  for ($i=11; $i>=5; $i--){
    $opponent_grade .= '<option value = "' .  $i . '">' . $i . '本</option>';
  }
  echo '<table>';
  echo '<tr><td>战时</td><td>对手</td></tr>';
  echo '<tr><td>' . $battle_date . '</td><td>' . $opponent_id . '</td></tr>';
  echo '</table>';

?>

<html>
<body>
  <form method="post" action="battlemap.php" >
    <table>
    <tr><td>战位</td><td>成员</td><td>对手本位</td></tr>
    <?php
      for ($i=1; $i<=$battle_size; $i++) {
        echo '<tr><td>' . $i . '</td>';
        echo '<td>';
        echo '<select id="Pos' . $i . '" name="Pos' . $i . '">';
        echo $fighter;
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
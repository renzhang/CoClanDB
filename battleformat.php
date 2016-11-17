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
  $battle_size = $_POST['battlesize'];
  $battle_date = $_POST['battledate'];
  $opponent = $_POST['opponent'];
  // Connect to the database
  $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
  
  // Insert Battle record into table
  $query_add = "INSERT INTO `clan_battle_info` " . 
               "(battle_date, opponent_clan_id, battle_size) " . 
               "VALUES ('$battle_date', '$opponent', '$battle_size')";
  $result_add = mysqli_query($dbc, $query_add);
  
  // Grab the profile data from the database
  mysqli_set_charset($dbc, 'utf8');
  $query = "SELECT user_id, user_name FROM `clan_user_info`";
  $data = mysqli_query($dbc, $query);
  $fighter = '';
  while ($row = mysqli_fetch_array($data)) {
    $fighter .=  '<option value = "' .  $row['user_id'] . '">' . $row['user_name'] . '</option>';
  }
  echo '<table>';
  echo '<tr><td>战时</td><td>对手</td></tr>';
  echo '<tr><td>' . $battle_date . '</td><td>' . $opponent . '</td></tr>';
  echo '</table>';

?>  

<html>
<body>  
  <form method="post" action="battleentry.php">
    <table>
    <tr><td>战位</td><td>成员</td></tr>
    <?php
      for ($i=1; $i<=$battle_size; $i++) {
        echo '<tr><td>' . $i . '</td>';
        echo '<td>';
        echo '<select id="Pos' . $i . '" name="Pos' . $i . '">';
        echo $fighter;
        echo '</select>';
        echo '</td></tr>';
      }
    ?>
    </table>
    <input type="text" name="battle_date" value='<?php $battle_date ?>' />
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

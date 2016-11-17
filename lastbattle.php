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
  $query_last_battle = 'SELECT battle_id, battle_size FROM `clan_battle_info` ORDER BY battle_date DESC LIMIT 1';
  $data_last_battle  = mysqli_query($dbc, $query_last_battle);
  $row_last_battle   = mysqli_fetch_array($data_last_battle);
  $battle_id = $row_last_battle['battle_id'];
  $battle_size = $row_last_battle['battle_size'];
}
else {
  $battle_id = $id_query;
  $query_battle = 'SELECT battle_size  FROM `clan_battle_info` WHERE battle_id=' . $battle_id;
  $data_battle  = mysqli_query($dbc, $query_battle);
  $row_battle   = mysqli_fetch_array($data_battle);
  $battle_size = $row_battle['battle_size'];
}
?>

<html>
<body>
  <form method="post" action="updatefight.php" >
    <table border="2">
    <tr><td colspan="7">
      <?php
      # '1' for attack record, '0' for defense record
      if ($side_query == 1) {
        echo '<a href="lastbattle.php?side=0&id=' . $id_query . '">攻</a>';
      }
      else {
        echo '<a href="lastbattle.php?side=1&id=' . $id_query . '">守</a>';
      }
      ?>
    </td></tr>
    <tr>
      <?php
      # '1' for attack record, '0' for defense record
      if ($side_query == 1) {
        echo '<td>No.#</td><td>攻方</td><td>守方Pos</td><td>STAR</td><td>Crush</td>';
      }
      else {
        echo '<td>No.#</td><td>攻方Pos</td><td>守方</td><td>STAR</td><td>Crush</td>';
      }
      ?>
    </tr>
    <?php
    // Get saved battle fights data
    $query_battle_fight = "SELECT bm.fight_id, bm.attack_pos, bm.defense_pos, bf.win_star, bf.crush_rate ". 
      "FROM `clan_battle_map` AS bm " . 
      "INNER JOIN `clan_battle_fight` AS bf USING (fight_id) " . 
      "WHERE attack=" . $side_query . 
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

      // select option of fight result - star winned
      $star_option =  '';
      for ($i=0; $i<=3; $i++) {
        if ($i==0) {
          $star_option .= '<option value= "' .  $i . '">' . $i . '&#9734;</option>';
        }
        elseif ($i==$star) {
          $star_option .= '<option selected="selected" value="' .  $i . '">' . $i . '&#9733;</option>';
        }
        else {
          $star_option .= '<option value= "' .  $i . '">' . $i . '&#9733;</option>';
        }
      }

      // Get clan username of the fight from clan user id: Attack from attacker / Defense from defender
      $query_user_name = "SELECT user_name FROM `clan_user_info`" . 
        " WHERE user_id=";
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

      $attacker_name  = ($side_query == 1) ? $fighter_name : $attacker;

      // Get defender option list from clan_battle_map
      $target = '<option value=0>0</option>';
      if ($side_query == 1) {
        // Get attack opponent position into option list
        for($i=1; $i<=$battle_size; $i++) {
          if ($i == $defender) {
            $target .=  '<option selected="selected" value="' .  $i . '">' . $i . '</option>';
          }
          else {
            $target .=  '<option value= "' .  $i . '">' . $i . '</option>';
          }
        }
      }
      else{
        // Get defender into option list: value[user_id], display[user_name];
        $query_user = "SELECT attack_pos FROM `clan_battle_map` " . 
          "WHERE battle_id = " . $battle_id . " AND attack=1";
        $data_user = mysqli_query($dbc, $query_user);
        $i=0;
        while ($row_user = mysqli_fetch_array($data_user)) {
          $i++;
          if ($i % 2 != 0) {
            $pos_id = $row_user['attack_pos'];
            $pos_name = ($i + 1) / 2;

            $query_user_name = "SELECT user_name FROM `clan_user_info` WHERE user_id=" . $pos_id;
            mysqli_set_charset($dbc, 'utf8');
            $data_user_name = mysqli_query($dbc, $query_user_name);
            $row_user_name = mysqli_fetch_array($data_user_name);

            $pos_name .= '.' . $row_user_name['user_name'];

            if ($pos_id == $defender) {
              $target .=  '<option selected="selected" value="' .  $pos_id . '">' . $pos_name . '</option>';
            }
            else {
              $target .=  '<option value= "' .  $pos_id . '">' . $pos_name . '</option>';
            }
          }
        }
      }

      // Create result list table
      echo '<tr>';
      if ($pos % 2 != 0) {
        echo '<th rowspan="2">' . ($pos + 1) / 2 . '</th>';
        echo '<th rowspan="2">' . $attacker_name . '</th>';
      }
      echo '<td>';
      echo '<select id="option_target' . $fightid . '" name="option_target' . $fightid . '">';
      echo $target;
      echo '</select>';
      echo '</td>';
      echo '<td>';
      echo '<select id="option_star' . $fightid . '" name="option_star' . $fightid . '">';
      echo $star_option;
      echo '</select>';
      echo '</td>';
      echo '<td>';
      echo '<input type="text" size="2" align="right" name="crush' . $fightid . '" value="' . $crush . '" />';
      echo '%';
      echo '</td>';
      echo '</tr>';
    }
    ?>
  </table>
  <input type='hidden' name="battle_id" value='<?php echo "$battle_id";?>' /> 
  <input type='hidden' name="attack" value='<?php echo "$side_query";?>' /> 
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
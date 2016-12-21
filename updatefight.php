<?php

require_once('appvars.php');
require_once('connectvars.php');

$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

$battle_id = $_POST['battle_id'];
$attack  = $_POST['attack'];

$query_clan_id = "SELECT opponent_clan_id FROM `clan_battle_info` WHERE battle_id=" . $battle_id;
$data_clan_id = mysqli_query($dbc, $query_clan_id);
$row_clan_id = mysqli_fetch_array($data_clan_id);
$opponent_clan_id = $row_clan_id['opponent_clan_id'];

$query_battle_fight = "SELECT fight_id FROM `clan_battle_map`" .
  " WHERE attack=" . $attack .
  " AND battle_id=" . $battle_id;
$data_battle_fight = mysqli_query($dbc, $query_battle_fight);

$pos=0;
$result=1;
while ($row_battle_fight = mysqli_fetch_array($data_battle_fight)) {
  $pos++;

  $fight_id = $row_battle_fight['fight_id'];
  $option_target_id = "option_target" . "$fight_id";
  $option_star_id = "option_star" . "$fight_id";
  $text_crush_id = "crush" . "$fight_id";

  $opponent = $_POST["$option_target_id"];
  $star = $_POST["$option_star_id"];
  $crush = $_POST["$text_crush_id"] / 100;

  if ($attack) {
    $query_defense_grade = "SELECT grade FROM `clan_opponent`" .
                           " WHERE opponent_clan_id='" . $opponent_clan_id . "' AND opponent_user_id=" . $opponent;
  }
  else {
    $query_defense_grade = "SELECT grade FROM `clan_user_grade`" .
                           " WHERE user_id=" . $opponent;
  }
  $data_defense_grade = mysqli_query($dbc, $query_defense_grade);
  $row_defense_grade = mysqli_fetch_array($data_defense_grade);
  $defense_grade = $row_defense_grade['grade'];

  if ($opponent != 0) {
    $query_update_map = "UPDATE `clan_battle_map` SET defense_pos=" . $opponent .
                        " , defense_grade=" . $defense_grade .
                        " WHERE fight_id=" . $fight_id;
    $result_update_map = mysqli_query($dbc, $query_update_map);
    echo $query_update_map . ':' . $result_update_map . '<br>';
    $result = $result && $result_update_map;
    $query_update_fight = "UPDATE `clan_battle_fight` SET win_star=" . $star .
      " WHERE fight_id=" . $fight_id;
    $result_update_fight = mysqli_query($dbc, $query_update_fight);
    $result = $result && $result_update_fight;
    $query_update_fight = "UPDATE `clan_battle_fight` SET crush_rate=" . $crush .
      " WHERE fight_id=" . $fight_id;
    $result_update_fight = mysqli_query($dbc, $query_update_fight);
    $result = $result && $result_update_fight;
  }
}
?>

<?php
mysqli_close($dbc);
?>

<?php
# restore battle fight result page
if ($result) {
  echo "Updated!" . '<br>';
}
else {
  echo "Failed!" . '<br>';
}
echo '<a href="lastbattle.php?side=' . $attack . '&id=' . $battle_id . '">返回</a>';
?>
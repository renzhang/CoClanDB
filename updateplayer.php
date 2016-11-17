<?php
// Insert the page header
$page_title = "部落成员信息更新";
require_once('header.php');

require_once('appvars.php');
require_once('connectvars.php');
?>

<?php
$user_id = $_POST['user_id'];
$name   = $_POST['name'];
$grade  = $_POST['grade'];
$levels = $_POST['levels'];
$cup    = $_POST['cup'];
$star   = $_POST['star'];
$key    = $_POST['key'];
$status = $_POST['status'];

// Connect to the database to add new battle information
$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
mysqli_set_charset($dbc, 'utf8');

if ($user_id == 0) {
  // add new player info, grade info, get the update user_id
  $query_player_add = "INSERT INTO `clan_user_info`" .
    " (user_name, user_key)" .
    " VALUES ('$name', '$key')";
  $result_player_add = mysqli_query($dbc, $query_player_add);

  // update new player grade info
  mysqli_set_charset($dbc, 'utf8');
  $query_id = "SELECT user_id FROM `clan_user_info` WHERE user_name='" . $name . "'";
  $data_id = mysqli_query($dbc, $query_id);
  $row_id = mysqli_fetch_array($data_id);
  $user_id = $row_id['user_id'];

  $query_info_add = "INSERT INTO `clan_user_grade` (user_id) VALUES ('$user_id')";
  $result_info_add = mysqli_query($dbc, $query_info_add);
}

// Update existing player
$query_player_update = "UPDATE `clan_user_grade` SET grade=" . $grade .
  " WHERE user_id=" . $user_id;
$result_player_update = mysqli_query($dbc, $query_player_update);
$query_player_update = "UPDATE `clan_user_grade` SET levels=" . $levels .
  " WHERE user_id=" . $user_id;
$result_player_update = mysqli_query($dbc, $query_player_update);
$query_player_update = "UPDATE `clan_user_grade` SET cup=" . $cup .
  " WHERE user_id=" . $user_id;
$result_player_update = mysqli_query($dbc, $query_player_update);
$query_player_update = "UPDATE `clan_user_grade` SET star=" . $star .
  " WHERE user_id=" . $user_id;
$result_player_update = mysqli_query($dbc, $query_player_update);
$query_player_update = "UPDATE `clan_user_info` SET user_name='" . $name .
  "' WHERE user_id=" . $user_id;
$result_player_update = mysqli_query($dbc, $query_player_update);
$query_player_update = "UPDATE `clan_user_info` SET status=" . $status .
  " WHERE user_id=" . $user_id;
$result_player_update = mysqli_query($dbc, $query_player_update);

$fight_id = $_POST['fight_id'];
if (!empty($fight_id)) {
  //update fight map need
  $query_map_update = "UPDATE `clan_battle_map` SET attack_pos=" . $user_id .
  " WHERE fight_id=" . $fight_id;
  $result_map_update = mysqli_query($dbc, $query_map_update);
  $fight_id++;
  $query_map_update = "UPDATE `clan_battle_map` SET attack_pos=" . $user_id .
  " WHERE fight_id=" . $fight_id;
  $result_map_update = mysqli_query($dbc, $query_map_update);
}

echo $name . '信息已更新 <br>';
echo '<a href="index.php?view_id=3">返回</a>';
mysqli_close($dbc);
?>

<?php
//Insert the page footer
require_once('footer.php');
?>
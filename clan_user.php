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
  echo '<a href="clan_user.php?id=0">最近一战</a>';
  echo ' | ';
  echo '<a href="clan_user.php?id=1">最近五战</a>';
  echo ' | ';
  echo '<a href="clan_user.php?id=2">最近十战</a>';
  echo '<br>';

  // Connect to the database
  $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

  // Grab the profile data from the database
  mysqli_set_charset($dbc, 'utf8');

  // Retrieve the user data from MySQL
  mysqli_set_charset($dbc, 'utf8');
  $query_user = "SELECT ui.user_id, ui.user_name, ug.grade, ug.levels, ug.cup, ug.star " . 
    "FROM `clan_user_info` AS ui " . 
    "INNER JOIN `clan_user_grade` AS ug USING (user_id) " . 
    "WHERE status=1 " .
    "ORDER BY ug.star DESC";
  $data_user = mysqli_query($dbc, $query_user);

  // Loop through the array of user data, formatting it as HTML
  echo '<table>';
  echo '<tr><td>成员</td><td>本位</td><td>战绩</td><td>平均摧毁率</td></tr>';
  while ($row_user = mysqli_fetch_array($data_user)) {
    echo '<tr><td><a href="viewplayer.php?user_id=' . $row_user['user_id'] . '">' . $row_user['user_name'] . '</a> </td>';
    echo '<td>' . $row_user['grade'] . '</td>';
    echo '<td>' . $row_user['star'] . '&#9734</td>';
    //$query_war_record = "SELECT fai.opponent_user_id, fai.crush_rate, fai. " . 
    //                    "FROM `clan_fight_attack_info` AS fai " . 
    //                    "WHERE fai.user_id = " . $row_user['user_id'];
    //$data_war_record = mysqli_query($dbc, $query_war_record);
    //while ($row_user_record = mysqli_fetch_array($data_war_record)) {
    //  echo '<td>' . $row_user_record['crush_rate'] * 100 . '%</td>';
    //  echo '<td>No.' . $row_user_record['opponent_user_id'] . '</td>';
    //}
    echo '</tr>';
  }
  echo '</table>';
  mysqli_close($dbc);
?>

<?php
  //Insert the page footer
  require_once('footer.php');
?>

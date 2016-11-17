<?php

  // Insert the page header
  $page_title = "info";
  require_once('header.php');

  require_once('appvars.php');
  require_once('connectvars.php');

  // Show the navigation menu
  require_once('navmenu.php');

  // Connect to the database
  $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

  // Grab the profile data from the database
  mysqli_set_charset($dbc, 'utf8');
  $user_id = $_GET['user_id'];
  $fight_id = $_GET['fight_id'];

  $query_player = "SELECT ui.user_name, ui.user_key, ui.status, ug.till_date, ug.grade, ug.levels, ug.cup, ug.star" .
                  " FROM `clan_user_info` AS ui " .
                  " INNER JOIN `clan_user_grade` AS ug USING (user_id) " .
                  " WHERE user_id = " . $user_id;
  $data_player = mysqli_query($dbc, $query_player);

  if (mysqli_num_rows($data_player) == 1) {
    // The user row was found so display the user data
    $row_player = mysqli_fetch_array($data_player);
    $name = $row_player['user_name'];
    $status = $row_player['status'];
    $grade = $row_player['grade'];
    $levels = $row_player['levels'];
    $cup = $row_player['cup'];
    $star = $row_player['star'];
    $userkey = $row_player['user_key'];
  }
  else {
    $name = '?';
    $status = '0';
    $grade = '0';
    $levels = '0';
    $cup = '0';
    $star = '0';
    $userkey = '?';
  }
  if ($status){
    $status_radio = '<input type="radio" name="status" value="1" checked>在<br>';
    $status_radio .= '<input type="radio" name="status" value="0">离';
  }
  else {
    $status_radio = '<input type="radio" name="status" value="1">在<br>';
    $status_radio .= '<input type="radio" name="status" value="0"checked>离';
  }
  mysqli_close($dbc);
?>

<html>
<body>
  <form method="post" action="updateplayer.php" >
    <table>
    <tr><td>大名</td><td>本位</td><td>级别</td><td>最高杯</td><td>战星</td><td>USER_KEY</td><td>是否在帮？</td></tr>
    <?php
      echo '<tr><td><input type="text" name="name" value="' .$name. '" /></td>';
      echo '<td><input type="text" name="grade" value="' .$grade. '" /></td>';
      echo '<td><input type="text" name="levels" value="' .$levels. '" /></td>';
      echo '<td><input type="text" name="cup" value="' .$cup. '" /></td>';
      echo '<td><input type="text" name="star" value="' .$star. '" /></td>';
      echo '<td><input type="text" name="key" value="' .$userkey. '" /></td>';
      echo '<td><form>' . $status_radio . '</form></td></tr>';
    ?>
    </table>
    <input type="submit" name="Submit" value="Submit" />
    <input type='hidden' name="user_id" value='<?php echo "$user_id";?>' />
    <input type='hidden' name="fight_id" value='<?php echo "$fight_id";?>' />
  </form>
</body>
</html>

<?php
  // Insert the page footer
  require_once('footer.php');
?>

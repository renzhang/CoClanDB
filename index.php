<?php
  // Insert the page header
  $page_title = 'ww';
  require_once('header.php');

  require_once('appvars.php');
  require_once('connectvars.php');

  // Show the navigation menu
  require_once('navmenu.php');

  echo '<br>';
  echo '<br><a href="index.php?view_id=3">现成员</a>';
  echo ' | ';
  echo '<a href="index.php?view_id=1">战星前 10</a>';
  echo ' | ';
  echo '<a href="index.php?view_id=2">战星前 25</a>';
  echo ' | ';
  echo '<a href="index.php?view_id=4">历史成员</a>';
  echo '<br>';

  // Connect to the database
  $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

  $view_id = $_GET['view_id'];
  if ($view_id == 1) {
    // First 10 members in the clan order by winning stars
    $amount_set = 10;
    $status_set = 1;
    $order_set = 0;
  }
  elseif ($view_id == 2) {
    // First 25 members in the clan order by winning stars
    $amount_set = 25;
    $status_set = 1;
    $order_set = 0;
  }
  elseif ($view_id == 3) {
    // All members in the clan order by grade and cups
    $amount_set = 50;
    $status_set = 1;
    $order_set = 1;
  }
  elseif ($view_id == 4) {
    // All history members in the clan order by winning stars
    $amount_set = 200;
    $status_set = 0;
    $order_set = 1;
  }
  else {
    // First 3 members in the clan order by winning stars
    $amount_set = 3;
    $status_set = 1;
    $order_set = 0;
  }

  // Retrieve the user data from MySQL
  mysqli_set_charset($dbc, 'utf8');
  $query_user = "SELECT ui.user_id, ui.user_name, ug.grade, ug.levels, ug.cup, ug.star" .
           " FROM `clan_user_info` AS ui" .
           " INNER JOIN `clan_user_grade` AS ug USING (user_id)";
  if ($status_set) {
    $query_user .= " WHERE status=1";
  }
  if ($order_set == 0) {
    $query_user .= " ORDER BY ug.star DESC " . "LIMIT " . $amount_set;
  }
  elseif ($order_set == 1) {
    $query_user .= " ORDER BY ug.grade DESC, ug.cup DESC " . "LIMIT " . $amount_set;
  }
  $data_user = mysqli_query($dbc, $query_user);

  // Loop through the array of user data, formatting it as HTML
  if ($view_id == 3) {
    // All members in the clan order by grade and cups
    echo '<h4> 现成员 </h4>';
  }
  elseif ($view_id == 4) {
    // All history members in the clan order by winning stars
    echo '<h4> 历史成员 </h4>';
  }
  else {
    // First n members in the clan order by winning stars
    echo '<h4> 战星前' . $amount_set . '名:</h4>';
  }

  echo '<table>';
  echo '<tr><td>成员</td><td>本位</td><td>战绩</td><td>级别</td><td>最高夺杯</td></tr>';
  $members = 0;
  while ($row_user = mysqli_fetch_array($data_user)) {
    $members ++;
    echo '<tr><td><a href="viewplayer.php?user_id=' . $row_user['user_id'] . '">' . $row_user['user_name'] . '</a> </td>';
    echo '<td>' . $row_user['grade'] . '</td>';
    echo '<td>' . $row_user['star'] . '&#9734</td>';
    echo '<td>' . $row_user['levels'] . '</td>';
    echo '<td>' . $row_user['cup'] . '</td>';
    echo '</tr>';
  }
  echo '</table>';
  if ($view_id == 3) {
    echo '<br>' . $members . ' members in the Clan.' . '  ';
    echo '<a href="viewplayer.php?user_id=0">新成员？</a>';
  }

  mysqli_close($dbc);
?>

<?php
  // Insert the page footer
  require_once('footer.php');
?>
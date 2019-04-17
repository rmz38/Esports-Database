<?php
// DO NOT REMOVE!
include("includes/init.php");
// DO NOT REMOVE!
$db = open_sqlite_db("secure/data.sqlite");
//$sql = "SELECT * FROM club_members";
//$params = array();

//below are the functions for printing
function print_record($record){?>
  <tr>
    <td><?php echo htmlspecialchars($record["name"]); ?> </td>
    <td><?php echo htmlspecialchars($record["netid"]); ?> </td>
    <td <?php fade_words($record["role"]) ?>><?php echo htmlspecialchars(convert_null($record["role"], "role")); ?> </td>
    <td <?php fade_words($record["game"]) ?>><?php echo htmlspecialchars(convert_null($record["game"], "game")); ?> </td>
    <td <?php fade_words($record["graduation_year"]) ?>><?php echo htmlspecialchars(convert_null($record["graduation_year"],"graduation_year")); ?> </td>
    <td <?php fade_words($record["email"]) ?>><?php echo htmlspecialchars(convert_null($record["email"], "eamil")); ?> </td>
</tr>
<?php }
//this function changes null items in a record to a word such as "unknown" or "no email" etc
function convert_null($item, $type){
  if($item == NULL){
    if($type == "game"){
      return "No Games";
    }
    else if($type == "email"){
      return "No Email";
    }
    else if($type == "graduation_year"){
      return "Year Unknown";
    }
    else if($type == "role"){
      return "None Known";
    }
    else{
      return "N/A";
    }
  }
  else{
    return $item;
  }
}
//function for making null items ex. "no email" more faded and easier to ignore in search results
function fade_words($item){
  if($item == NULL){
    echo "class = faded";
  }
}
//search form code
// const SEARCH_FIELD_LIST = [ //not sure if needed anymore
//   "name" => "Name",
//   "netid" => "NetID",
//   "role" => "Role",
//   "game" => "Game",
//   "graduation_year" => "Graduation Year",
//   "email" => "Email"
// ];

if ($_GET['search_box'] != "" || $_GET['search_box'] != NULL && isset($_GET['search'])){ //&& isset($_POST['category'])
  $do_the_search = TRUE;
  //get search terms from search box input
  $search_box = filter_input(INPUT_GET, "search_box", FILTER_SANITIZE_STRING);
  $search_box = trim($search_box);
  // $sql = "SELECT * FROM reviews WHERE ".$field." LIKE '%'||:search||'%' COLLATE NOCASE;";
  //     $params = array(
  //       ':search' => $search_box
  //     );


}else{
  //nothing was input into search box
   // may not be necessary anymore
  $do_the_search = FALSE;
  $category = NULL;
  $search_box = NULL;
}

// code for inserting records on an html form
// $game_types = exec_sql_query($db, "SELECT DISTINCT game FROM club_members", NULL)->fetchAll(PDO::FETCH_COLUMN);//used to find members that play specific games, also allows my admin team that is searching to find the game they need without accidentally spelling something wrong

if(isset($_GET['insert'])){
  $name = filter_input(INPUT_GET,"insert_name",FILTER_SANITIZE_STRING);
  $netid = filter_input(INPUT_GET,"insert_netid",FILTER_SANITIZE_STRING);
  $role = filter_input(INPUT_GET,"insert_role",FILTER_SANITIZE_STRING);
  $game = filter_input(INPUT_GET,"insert_game",FILTER_SANITIZE_STRING);
  $graduation_year = filter_input(INPUT_GET,"insert_graduation_year",FILTER_VALIDATE_INT);
  $email = filter_input(INPUT_GET,"insert_email",FILTER_VALIDATE_EMAIL);
  //query for inserting record based on inputs from form into the database
  $sql = "INSERT INTO club_members (name, netid, role, game, graduation_year, email) VALUES (:name, :netid, :role, :game, :graduation_year, :email);";
  $params = array(
    ':name' => $name,
    ':netid' => $netid,
    ':role' => $role,
    ':game' => $game,
    ':graduation_year' => $graduation_year,
    ':email' => $email
  );
  $result = exec_sql_query($db, $sql, $params);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="styles/site.css">

  <title>Home</title>
</head>

<body>
  <!-- https://www.toptal.com/designers/subtlepatterns/patterns/connectwork.png is the source of the body background image-->
  <!-- TODO: This should be your main page for your site. -->
  <header>
    <h1><?php include("includes/title.php"); ?></h1>
  </header>
  <form id = "search_form" method = "get" action = "index.php">
      <div class = "pad_box">
        <input id = "search_box" type = "text" placeholder = "Search" name="search_box">
        <button id = "search" type="submit" name = "search" value = "asdf">Search</button>
      </div>
  </form>

  <?php
    if($do_the_search){// do the search, get results
      $sql = "SELECT * FROM club_members WHERE name LIKE '%'||:search||'%' OR graduation_year LIKE '%'||:search||'%' OR netid LIKE '%'||:search||'%' OR role LIKE '%'||:search||'%' OR game LIKE '%'||:search||'%' OR email LIKE '%'||:search||'%';";
      $params = array(
        ':search' => $search_box
      );
    } else {
      // Nothing to query, return all members
      $sql = "SELECT * FROM club_members";
      $params = array();
    }

    // Get the members to show in table
    $result = exec_sql_query($db, $sql, $params);
    $records = $result->fetchAll();
  ?>
  <?php if($do_the_search){ ?>
            <h2>Search Results: <?php echo count($records); ?></h2>
            <p>Searching for nothing will display all members again</p>
  <?php }else{ ?>
            <h2>All Members: <?php echo count($records); ?></h2>
  <?php } ?>
<!-- need to put in sql data  -->
<div class = "black_box">
  <table>
    <tr>
      <th>Name</th>
      <th>NetID</th>
      <th>Role</th>
      <th>Game</th>
      <th>Graduation Year</th>
      <th>Email</th>
    </tr>
  <?php
  if(count($records) > 0){
    foreach($records as $record){
      print_record($record);
    }
  } else{
    //no results found, so put a row saying something like "no results"
    echo "<tr class = 'faded'> <td colspan = '6'>None Found </td></tr>";
  }?>
  </table>
</div>
  <div class = "white_box">
    <h2>Insert Member:</h2>
    <form id = "insert_form" method = "get" action = "index.php">
      <fieldset id = "iform">
        <div class = "pad_box">
          <input id = "name" type = "text" placeholder = "Name (Required)" name="insert_name">
          <input id = "netid" type = "text" placeholder = "NetID(Required)" name="insert_netid">
          <input id = "role" type = "text" placeholder = "Role" name="insert_role">
          <input id = "game" type = "text" placeholder = "Game" name="insert_game">
          <input id = "graduation_year" type = "text" placeholder = "Graduation Year" name="insert_graduation_year">
          <input id = "email" type = "email" placeholder = "Email" name="insert_email">
          <button id = "insert" name = "insert" value = "insert">Insert</button> <!--make sure name is correct later-->
        </div>
      </fieldset>
    </form>
  </div>
  <?php include("includes/footer.php"); ?>

</body>
</html>

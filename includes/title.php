<?php $index = "These are the current members of Esports at Cornell";
if($searched){

}else if($_SERVER['PHP_SELF'] == "index.php"){
    echo $index;
}
else{
    echo $index; // there are no other pages at the moment
}
?>

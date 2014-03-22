<?php
require_once "../../config.php";
require_once $CFG->dirroot."/pdo.php";
require_once $CFG->dirroot."/lib/lms_lib.php";

session_start();

// Sanity checks
$LTI = requireData(array('user_id', 'link_id', 'role','context_id'));
$instructor = isInstructor($LTI);
$p = $CFG->dbprefix;

// Gets counts and max of the submissions
$stmt = pdoQueryDie($pdo,
    "SELECT R.result_id AS result_id, R.link_id AS link_id, R.grade AS grade, 
        R.note AS note, R.updated_at as updated_at, L.title as title
    FROM {$p}lti_result AS R JOIN {$p}lti_link as L 
        ON R.link_id = L.link_id
    WHERE R.user_id = :UID AND L.context_id = :CID AND R.grade IS NOT NULL",
    array(":UID" => $LTI['user_id'], ":CID" => $LTI['context_id'])
);

// View 
headerContent();
startBody();
flashMessages();
welcomeUserCourse($LTI);

echo('<table border="1">');
$first = true;
while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
    if ( $first ) {
        echo("\n<tr>\n");
        foreach($row as $k => $v ) {
            echo("<th>".htmlent_utf8($k)."</th>\n");
        }
        echo("</tr>\n");
    }
    $first = false;
    echo("\n<tr>\n");
    foreach($row as $k => $v ) {
        echo("<td>".htmlent_utf8($v)."</td>\n");
    }
    echo("</tr>\n");
}
echo("</table>\n");

?>
<form method="post">
<br/>
<input type=submit name=doCancel onclick="location='<?php echo(sessionize('index.php'));?>'; return false;" value="Cancel">
</form>
<?

footerContent();
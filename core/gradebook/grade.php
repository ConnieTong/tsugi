<?php
require_once "../../config.php";
require_once $CFG->dirroot."/db.php";
require_once $CFG->dirroot."/lib/lti_util.php";
require_once $CFG->dirroot."/lib/lms_lib.php";

session_start();

// Sanity checks
$LTI = requireData(array('user_id', 'link_id', 'role','context_id', "result_id"));
$instructor = isInstructor($LTI);
if ( ! $instructor ) die("Requires instructor role");
$p = $CFG->dbprefix;

// Gets counts and max of the submissions
$stmt = pdoQueryDie($db,
    "SELECT R.result_id AS result_id, grade, note, R.json AS json, displayname, email
    FROM {$p}lti_result AS R
    JOIN {$p}lti_user AS U ON R.user_id = U.user_id
    WHERE R.link_id = :LID
    GROUP BY U.email",
    array(":LID" => $LTI['link_id'])
);

// View 
headerContent();
startBody();
flashMessages();
welcomeUserCourse($LTI);

echo('<table border="1">');
echo("\n<tr><th><th>Email</th><th>Grade</th></tr>\n");

while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
    echo('<tr><td><a href="detail.php?result_id='.$row['result_id'].'">'.htmlent_utf8($row['displayname'])."</a></td>
        <td>".htmlent_utf8($row['email'])."</td>
        <td>".htmlent_utf8($row['grade'])."</td>
    </tr>\n");
}
echo("</table>\n");

footerContent();

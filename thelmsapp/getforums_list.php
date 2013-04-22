
<link rel="stylesheet" type="text/css" href="style.css" media="screen" />
<?php
// PAGE Details
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/my/lib.php');

require_login();
global $USER, $CFG;

$userid = $USER->id;  // Owner of the page

$context = $usercontext = get_context_instance(CONTEXT_SYSTEM);
//$context = $usercontext = get_context_instance(CONTEXT_USER, $userid, MUST_EXIST);


$header = "$SITE->shortname";

$params = array();
$PAGE->set_context($context);

$PAGE->set_url('/local/thelmsapp/getmycourses_list.php', array('id'=>$userid));
$PAGE->navbar->add(get_string('select_forum', 'local_thelmsapp'));
$PAGE->set_title($header);

$courseid = $_GET["courseid"];  

$coursetable = $DB->get_record('course', array('id'=>$courseid));
$course_name = $coursetable->shortname;

$module = $DB->get_record('modules', array('name'=>'forum'));
$moduleid = $module->id;

$course_module = $DB->get_records('course_modules', array('course'=>$courseid , 'module'=>$moduleid));
$course_module_id_arr = array();
$forum_name = array();
$forum_intro = array();
$icount = 0;
foreach ($course_module as $cm) 
{
	$course_module_id_arr[$icount] = $cm->id;
	
	$forum = $DB->get_record('forum', array('course'=>$courseid , 'id'=>$cm->instance));
	$forum_name[$icount] = $forum->name;
	$forum_intro[$icount] = $forum->intro;
	
	$icount++;
}
$num = $icount;

echo $OUTPUT->header();
?>

<script language='javascript'>
function loadPage(id)
{
	window.location.href = <?php $CFG->wwwroot; ?>'/mod/forum/view.php?id='+id;
}
</script>

<!--link rel="stylesheet" type="text/css" href="style2.css" /-->
<!--div id="travel" style='color:#ffffff'> Επιλογή Μαθήματος </div-->

<div>
<table align='center'>
<tr><td><h3><?php echo $course_name; ?></h3></td></tr>
<tr><td>

<?php
for($i=0; $i<$num; $i++)
{
$temp = $i+1;

?>
<!--a href="javascript:loadPage('<?php //echo $course_module_id_arr[$i]; ?>')"><?php //echo $forum_arr[$i]->name; ?>test</a-->

<input type="radio" id="r<?php echo $temp; ?>" name="rr" class="regular-radio" value="<?php echo $course_module_id_arr[$i]; ?>" onClick='javascript:loadPage("<?php echo $course_module_id_arr[$i]; ?>")'/>

<label for="r<?php echo $temp; ?>"><span></span><?php echo $forum_name[$i]; ?><!--&nbsp;(<?php //echo $forum_intro[$i]; ?>)--></label>
<br/>
<label for="radio-1-<?php echo $temp; ?>"></label>

<?php
} 
if($icount==0) 
{
	echo '<p align="center">'.get_string('msg_noforum', 'local_thelmsapp').'</p>';
}
?>

</td></tr>
</table>
</div>
    

<?php
echo $OUTPUT->footer();
?>


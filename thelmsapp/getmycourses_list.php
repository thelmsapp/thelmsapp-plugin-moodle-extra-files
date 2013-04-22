
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
$PAGE->navbar->add(get_string('selectcourse', 'local_thelmsapp'));
$PAGE->set_title($header);


// Κώδικας για να βρεθούν τα μαθήματα που είναι εγγεγραμμένος ο χρήστης
$type_select = $_GET["tp"];  //Βαθμοί -> type=1 --- Ανακοινώσεις -> type=2


$content = array();

	// limits the number of courses showing up
	$courses_limit = 100;
	// FIXME: this should be a block setting, rather than a global setting
    if (isset($CFG->mycoursesperpage)) {
		$courses_limit = $CFG->mycoursesperpage;
    }

    $morecourses = false;
    if ($courses_limit > 0) {
        $courses_limit = $courses_limit + 1;
    }

    $courses = enrol_get_my_courses('id, shortname, modinfo, sectioncache', 'visible DESC,sortorder ASC', $courses_limit);
        

    $site = get_site();

    if (is_enabled_auth('mnet')) {
            $remote_courses = get_my_remotecourses();
    }
    if (empty($remote_courses)) {
        $remote_courses = array();
    }

    //if (($courses_limit > 0) && (count($courses)+count($remote_courses) >= $courses_limit)) {
            // get rid of any remote courses that are above the limit
        $remote_courses = array_slice($remote_courses, 0, $courses_limit - count($courses), true);
        if (count($courses) >= $courses_limit) {
            //remove the 'marker' course that we retrieve just to see if we have more than $courses_limit
            array_pop($courses);
        }
        $morecourses = true;
    //}


    if (array_key_exists($site->id,$courses)) {
        unset($courses[$site->id]);
    }

	$count=0;
    foreach ($courses as $c) 
    {
        if (isset($USER->lastcourseaccess[$c->id])) {
            $courses[$c->id]->lastaccess = $USER->lastcourseaccess[$c->id];
        } else {
            $courses[$c->id]->lastaccess = 0;
        }
            
        $content[$count] =  array();
        $content[$count][0] = $courses[$c->id]->id;
        $content[$count][1] = $courses[$c->id]->shortname;

        $cat = $DB->get_record('course_categories', array('id'=>$courses[$c->id]->category));
        	
        $content[$count][2] = $cat->name; //$courses[$c->id]->category;
        
		$count++;

    }
    
    

echo $OUTPUT->header();
?>

<script language='javascript'>
function loadPage(id)
{
	var type_select = '<?php echo $type_select; ?>';


	if(type_select==1) //Βαθμοί
	{
		window.location.href = <?php $CFG->wwwroot; ?>'/grade/report/index.php?id='+id; 
		//'http://192.168.1.82/grade/report/index.php?id='+id; 
	}
	if(type_select==2) //forum
	{
		window.location.href = <?php $CFG->wwwroot; ?>'/local/thelmsapp/getforums_list.php?courseid='+id; 
		//window.location.href = <?php $CFG->wwwroot; ?>'/mod/forum/view.php?id='+id; 
		//'http://192.168.1.82/mod/forum/view.php?id='+id; 
	}get
}
</script>

<!--link rel="stylesheet" type="text/css" href="style2.css" /-->
<!--div id="travel" style='color:#ffffff'> Επιλογή Μαθήματος </div-->

<div>
<table align='center'>
<tr><td>

<?php
for($i=0;$i<$count;$i++)
{
$temp = $i+1;

?>

<input type="radio" id="r<?php echo $temp; ?>" name="rr" class="regular-radio" value="<?php echo $content[$i][0]; ?>" onClick='javascript:loadPage("<?php echo $content[$i][0]; ?>")'/>

<label for="r<?php echo $temp; ?>"><span></span><?php echo $content[$i][1]; ?>&nbsp;(<?php echo $content[$i][2]; ?>)</label>
<label for="radio-1-<?php echo $temp; ?>"></label>


<?php
} 
if($count==0)
    {
    	echo '<p align="center">'.get_string('msg_nocourse', 'local_thelmsapp').'</p>';
    }
?>

</td></tr>
</table>
</div>
    

<?php
echo $OUTPUT->footer();
?>


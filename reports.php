<?php

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
global $CFG, $USER;

require_once('locallib.php');


//$search = trim(optional_param('search', '', PARAM_NOTAGS));  // search string
$searchstring = optional_param('search', NULL, PARAM_RAW);
$page = optional_param('page', 0, PARAM_INT);   // which page to show
$perpage = optional_param('perpage', 10, PARAM_INT);   // how many per page
$dept = optional_param('dept', false, PARAM_TEXT);

//security check
$context = get_context_instance(CONTEXT_SYSTEM);
$PAGE->set_context($context);
//        if (!has_capability('local/evaluations:admin', $context)) {
//        print_error(get_string('restricted', 'local_evaluations'));
//    }



$PAGE->set_url($CFG->wwwroot . '/local/evaluations/admin.php');

$navlinks = array(
    array(
        'name' => get_string('nav_ev_mn', 'local_evaluations'),
        'link' => $CFG->wwwroot . '/local/evaluations/index.php',
        'type' => 'misc'
    ), array(
        'name' => get_string('dept_selection', 'local_evaluations'),
        'link' => '',
        'type' => 'misc'
    )
);

if ($dept) {
    $navlinks[1]['link'] = $CFG->wwwroot . '/local/evaluations/reports.php';

    array(
        'name' => get_string('nav_reports', 'local_evaluations'),
        'link' => '',
        'type' => 'misc'
    );
}



$nav = build_navigation($navlinks);


$PAGE->set_title(get_string('nav_reports', 'local_evaluations'));
$PAGE->set_heading(get_string('nav_reports', 'local_evaluations'));
$PAGE->requires->css('/local/evaluations/style.css');
require_login();
echo $OUTPUT->header();

if (!$dept) {

    $department_list = get_departments();
    $your_administrations = $DB->get_records('department_administrators', array('userid' => $USER->id));

    $your_depts = array();
    foreach ($your_administrations as $administration) {
        $your_depts[$administration->department] = $department_list[$administration->department];
    }

    echo '<ol>';
    foreach ($your_depts as $code => $dept) {
        echo '<li><a href="reports.php?dept=' . $code . '">' . $dept . '</a></li>';
    }
    echo '</ol>';

    echo $OUTPUT->footer();
    die();
}

if (is_dept_admin($dept, $USER)) {
    $totalcount = $DB->count_records('course');
    $url = new moodle_url($CFG->wwwroot . '/local/evaluations/reports.php', array('perpage' => $perpage));
    echo $OUTPUT->paging_bar($totalcount, $page, $perpage, $url);
    //james april
    //here we seperate if the search has been performed or not...    
    if (empty($searchstring)) {
        $courses = get_courses_page($categoryid = "all", $sort = "c.fullname ASC", $fields = "c.*", &$totalcount, $perpage * $page, $perpage);
    } else {
        $searchterms = explode(" ", $searchstring);
        $courses = get_courses_search($searchterms, "fullname ASC", 0, 50, &$totalcount);
    }
} else {
    //we could probably just makke courses empty so they can't get the reports
    //$courses = get_instructing_courses();
}

if (empty($courses) && !is_dept_admin($dept, $USER)) {
    print_error(get_string('restricted', 'local_evaluations'));
}


//Display Form


echo '<table cellpadding="1" style="text-align: center;">';



foreach ($courses as $course) {
    if (strpos($course->fullname, $dept) === 0) {
        $current = time();


        $sql = "SELECT * 
        FROM {evaluations} e 
        WHERE e.course = $course->id 
                AND e.start_time < $current 
                AND e.end_time < $current AND e.complete = 1 AND e.deleted <> 1";



        $evals = $DB->get_records_sql($sql);





        echo '<tr>';
        echo '<td colspan=2><center><div class="roundedBorder"><b>' . $course->fullname . '</div></b></center></td>';

        echo '</tr>';

        if ($evals == null) {
            echo '<tr><td colspan=2>' . get_string('none', 'local_evaluations') . '</td></tr>';
        } else {

            foreach ($evals as $eval) {

                echo '<tr>';
                $href = $CFG->wwwroot . '/local/evaluations/report.php?evalid=' . $eval->id . '&dept=' . $dept;

                echo "<td><a href='$href'>" . $eval->name . "</a></td>";

                echo "<td><a href='$href&force=D'>Download</a></td>";

                echo '</tr>';
            }
        }
        echo '<tr><td><br></td></tr>';
    }
}


echo '</tr></table>';


$searchURL = $CFG->wwwroot . '/local/evaluations/reports.php';
echo "
     <center> <form action='$searchURL' method='post'>
      Search: <input type='text' name ='search'/>
      <input type='submit' />
      </form></center>
   ";

echo $OUTPUT->footer();
?>
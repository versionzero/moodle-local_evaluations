<?php

/**
 * ************************************************************************
 * *                              Evaluation                             **
 * ************************************************************************
 * @package     local                                                    **
 * @subpackage  Evaluation                                               **
 * @name        Evaluation                                               **
 * @copyright   oohoo.biz                                                **
 * @link        http://oohoo.biz                                         **
 * @author      Dustin Durrand           				 **
 * @author      (Modified By) James Ward   				 **
 * @author      (Modified By) Andrew McCann				 **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later **
 * ************************************************************************
 * ********************************************************************** */
/**
 * This is the department administration page. You can deal with the administration
 * of each department from here. (Change preamble, standard questions, etc.)
 */
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('locallib.php');

// ----- Parameters ----- //
$dept = optional_param('dept', false, PARAM_TEXT);


// ----- Security ----- //
require_login();

$department_list = get_departments();
$your_administrations = $DB->get_records('department_administrators',
        array('userid' => $USER->id));

$your_depts = array();
foreach ($your_administrations as $administration) {
    $your_depts[$administration->department] = $department_list[$administration->department];
}

if (count($your_depts) == 0) {
    print_error('You are not an adminstrator for any departments');
}

// ----- Navigation ----- //
//bread crumbs
$navlinks = array(
    array(
        'name' => get_string('nav_ev_mn', 'local_evaluations'),
        'link' => $CFG->wwwroot . '/local/evaluations/index.php',
        'type' => 'misc'
    ),
    array(
        'name' => get_string('dept_selection', 'local_evaluations'),
        'link' => '',
        'type' => 'misc'
    ),
);

//If a department was selected the create a link the the department selection page
if ($dept) {
    $navlinks[1]['link'] = $CFG->wwwroot . '/local/evaluations/admin.php';

    $navlinks[] = array(
        'name' => get_string('nav_admin', 'local_evaluations'),
        'link' => '',
        'type' => 'misc'
    );
}
$nav = build_navigation($navlinks);


// ----- Stuff ----- //
$context = get_context_instance(CONTEXT_SYSTEM);
$PAGE->set_context($context);
$PAGE->set_title(get_string('nav_admin', 'local_evaluations'));
$PAGE->set_heading(get_string('nav_admin', 'local_evaluations'));
$PAGE->requires->css('/local/evaluations/style.css');
$PAGE->set_url($CFG->wwwroot . '/local/evaluations/admin.php');

// ----- Output ----- //
echo $OUTPUT->header();

if ($dept !== false && is_dept_admin($dept, $USER)) {
    //If the user is the department admin then generate a list of all department admin options.
    echo '<ol>';
    echo '<li><a href="' . $CFG->wwwroot . '/local/evaluations/change_preamble.php?dept=' . $dept . '">' . get_string('preamble',
            'local_evaluations') . '</a></li>';
    echo '<li><a href="' . $CFG->wwwroot . '/local/evaluations/standard_questions.php?dept=' . $dept . '">' . get_string('nav_st_qe',
            'local_evaluations') . '</a></li>';
    echo '<li><a href="' . $CFG->wwwroot . '/local/evaluations/coursecompare.php?dept=' . $dept . '">' . get_string('nav_cs_mx',
            'local_evaluations') . '</a></li>'; //COURSE COMPARE WAS REMOVED LEAVING CODE IN SO IT CAN BE ADDED LATER

    echo '</ol>';
} else {
    //If no dept was selected then cretae a list of all departments the user has available.
    echo '<ol>';
    foreach ($your_depts as $dept_code => $deptartment) {
        echo '<li><a href="' . $CFG->wwwroot . '/local/evaluations/admin.php?dept=' . $dept_code . '"> ' . $deptartment . '</a></li>';
    }
    echo'</ol>';
}
echo $OUTPUT->footer();
?>

<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file contains main class for the course format Topic
 *
 * @since     Moodle 2.0
 * @package   format_nead_unicentro
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot. '/course/format/lib.php');

class format_nead_unicentro_course_header implements renderable {
    private $course;

    function __construct($course)
    { 
      $this->course = $course;
    }

    public function getCourse(){
      return $this->course;
    }

}

class format_nead_unicentro_course_content_header implements renderable {
  
    private $course;

    function __construct($course)
    { 
      $this->course = $course;
    }

    public function getCourse(){
      return $this->course;
    }
    
    
  
}

/**
 * Main class for the Nead/Unicentro course format
 *
 * @package    format_nead_unicentro
 * @copyright  2012 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_nead_unicentro extends format_base {

    /**
    * Returns the format's settings and gets them if they do not exist.
    * @return type The settings as an array.
    */
    public function get_settings() {
	return get_config('format_nead_unicentro');
    }

    /**
     * Returns true if this course format uses sections
     *
     * @return bool
     */
    public function uses_sections() {
        return true;
    }

    
    public function course_header() {
      return new format_nead_unicentro_course_header($this->get_course());
    }
    
    public function course_content_header() {
      return new format_nead_unicentro_course_content_header($this->get_course());
    }
    
    public function section_format_options($foreditform = false) {
        static $sectionformatoptions = false;
        if ($sectionformatoptions === false) {
            $sectionformatoptions = array(
                'tabtitle' => array(
                    'type' => PARAM_NOTAGS,
                ),
                /*
                'menuitemgroup' => array(
                    'type' => PARAM_NOTAGS,
                ),
                */
            );
        }

        if ($foreditform && !isset($sectionformatoptions['tabtitle']['label'])) {
            $sectionformatoptionsedit = array(
                'tabtitle' => array(
                    'label' => new lang_string('tabtitle', 'format_nead_unicentro'),
                    'element_type' => 'text',
                    'help' => 'tabtitle',
                    'help_component' => 'format_nead_unicentro',
                ),
                /*
                'menuitemgroup' => array(
                    'label' => new lang_string('menuitemgroup', 'format_nead_unicentro'),
                    'element_type' => 'text',
                    'help' => 'menuitemgroup',
                    'help_component' => 'format_nead_unicentro',
                ),
                */
            );
            $sectionformatoptions = array_merge_recursive($sectionformatoptions, $sectionformatoptionsedit);
        }
        return $sectionformatoptions;
    }

    
    /**
     * Returns the display name of the given section that the course prefers.
     *
     * Use section name is specified by user. Otherwise use default ("Topic #")
     *
     * @param int|stdClass $section Section object from database or just field section.section
     * @return string Display name that the course format prefers, e.g. "Topic 2"
     */
    public function get_section_name($section) {
        $section = $this->get_section($section);
        if ((string)$section->name !== '') {
            return format_string($section->name, true,
                    array('context' => context_course::instance($this->courseid)));
        } else if ($section->section == 0) {
            return get_string('section0name', 'format_nead_unicentro');
        } else {
            return get_string('topic').' '.$section->section;
        }
    }

    /**
     * Returns URL to the stored file via pluginfile.php.
     *
     * Note the theme must also implement pluginfile.php handler,
     * theme revision is used instead of the itemid.
     *
     * @param string $setting
     * @param string $filearea
     * @return string protocol relative URL or null if not present
     */
    public function setting_file_url($setting, $filearea) {
        global $CFG;

        if (empty($this->get_settings()->$setting)) {
            return null;
        }
        

        $component = 'format_nead_unicentro';
        $itemid = '-1';
        $filepath = $this->get_settings()->$setting;
        $syscontext = context_system::instance();
        
        
        $url = moodle_url::make_file_url("$CFG->wwwroot/pluginfile.php", "/$syscontext->id/$component/$filearea/$itemid".$filepath);

        // Now this is tricky because the we can not hardcode http or https here, lets use the relative link.
        // Note: unfortunately moodle_url does not support //urls yet.

        $url = preg_replace('|^https?://|i', '//', $url->out(false));

        return $url;
    }

    public function option_file_url($option, $filearea) {
        global $CFG, $course;
        
        if (empty($this->get_format_options()[$option])) {
            return null;
        }
        $component = 'format_nead_unicentro';
        $itemid = '0';
        $filepath = $this->get_format_options()[$option];
        $context = context_course::instance($this->courseid);
        
        
        //public function get_area_files($contextid, $component, $filearea, $itemid = false, $sort = "itemid, filepath, filename", $includedirs = true) {
        
	$fs = get_file_storage();
	
	if($fs->is_area_empty($context->id, "format_nead_unicentro", "headingimage")) return '';

	$files = $fs->get_area_files($context->id, "format_nead_unicentro", "headingimage");
	
	$imgurl = '';
	
	foreach ($files as $file) {
	  $imgurl = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename());
	}
       
        //http://10.1.1.228/pluginfile.php/18/format_nead_unicentro/headingimage/0/logo-nead-ps.png
        
        $url = $imgurl . '';

        // Now this is tricky because the we can not hardcode http or https here, lets use the relative link.
        // Note: unfortunately moodle_url does not support //urls yet.

        //$url = preg_replace('|^https?://|i', '//', $url->out(false));

        return $url;
    }
    
    
    /**
     * Returns the display name of the given section that the course prefers.
     *
     * Use section name is specified by user. Otherwise use default ("Topic #")
     *
     * @param int|stdClass $section Section object from database or just field section.section
     * @return string Display name that the course format prefers, e.g. "Topic 2"
     */
    public function get_section_tabtitle($section) {
        $section = $this->get_section($section);
        if((string)$section->tabtitle !== '') {
	  return $section->tabtitle;
        } else {
	  return get_section_name($section);
        }
    }

    /**
     * The URL to use for the specified course (with section)
     *
     * @param int|stdClass $section Section object from database or just field course_sections.section
     *     if omitted the course view page is returned
     * @param array $options options for view URL. At the moment core uses:
     *     'navigation' (bool) if true and section has no separate page, the function returns null
     *     'sr' (int) used by multipage formats to specify to which section to return
     * @return null|moodle_url
     */
    public function get_view_url($section, $options = array()) {
        $course = $this->get_course();
        $url = new moodle_url('/course/view.php', array('id' => $course->id));

        $sr = null;
        if (array_key_exists('sr', $options)) {
            $sr = $options['sr'];
        }
        if (is_object($section)) {
            $sectionno = $section->section;
        } else {
            $sectionno = $section;
        }
        if ($sectionno !== null) {
            if ($sr !== null) {
                if ($sr) {
                    $usercoursedisplay = COURSE_DISPLAY_MULTIPAGE;
                    $sectionno = $sr;
                } else {
                    $usercoursedisplay = COURSE_DISPLAY_SINGLEPAGE;
                }
            } else {
                $usercoursedisplay = $course->coursedisplay;
            }
            if ($sectionno != 0 && $usercoursedisplay == COURSE_DISPLAY_MULTIPAGE) {
                $url->param('section', $sectionno);
            } else {
                if (!empty($options['navigation'])) {
                    return null;
                }
                $url->set_anchor('section-'.$sectionno);
            }
        }
        return $url;
    }

    /**
     * Returns the information about the ajax support in the given source format
     *
     * The returned object's property (boolean)capable indicates that
     * the course format supports Moodle course ajax features.
     *
     * @return stdClass
     */
    public function supports_ajax() {
        $ajaxsupport = new stdClass();
        $ajaxsupport->capable = true;
        return $ajaxsupport;
    }

    /**
     * Loads all of the course sections into the navigation
     *
     * @param global_navigation $navigation
     * @param navigation_node $node The course node within the navigation
     */
    public function extend_course_navigation($navigation, navigation_node $node) {
        global $PAGE;
        // if section is specified in course/view.php, make sure it is expanded in navigation
        if ($navigation->includesectionnum === false) {
            $selectedsection = optional_param('section', null, PARAM_INT);
            if ($selectedsection !== null && (!defined('AJAX_SCRIPT') || AJAX_SCRIPT == '0') &&
                    $PAGE->url->compare(new moodle_url('/course/view.php'), URL_MATCH_BASE)) {
                $navigation->includesectionnum = $selectedsection;
            }
        }

        // check if there are callbacks to extend course navigation
        parent::extend_course_navigation($navigation, $node);
    }

    /**
     * Custom action after section has been moved in AJAX mode
     *
     * Used in course/rest.php
     *
     * @return array This will be passed in ajax respose
     */
    function ajax_section_move() {
        global $PAGE;
        $titles = array();
        $course = $this->get_course();
        $modinfo = get_fast_modinfo($course);
        $renderer = $this->get_renderer($PAGE);
        if ($renderer && ($sections = $modinfo->get_section_info_all())) {
            foreach ($sections as $number => $section) {
                $titles[$number] = $renderer->section_title($section, $course);
            }
        }
        return array('sectiontitles' => $titles, 'action' => 'move');
    }

    /**
     * Returns the list of blocks to be automatically added for the newly created course
     *
     * @return array of default blocks, must contain two keys BLOCK_POS_LEFT and BLOCK_POS_RIGHT
     *     each of values is an array of block names (for left and right side columns)
     */
    public function get_default_blocks() {
        return array(
            BLOCK_POS_LEFT => array(),
            BLOCK_POS_RIGHT => array('search_forums', 'news_items', 'calendar_upcoming', 'recent_activity')
        );
    }

    /**
     * Definitions of the additional options that this course format uses for course
     *
     * Nead/Unicentro format uses the following options:
     * - coursedisplay
     * - numsections
     * - hiddensections
     * - headinginfo
     *
     * @param bool $foreditform
     * @return array of options
     */
    public function course_format_options($foreditform = false) {
        static $courseformatoptions = false;
        if ($courseformatoptions === false) {
            $courseconfig = get_config('moodlecourse');
            $courseformatoptions = array(
                'numsections' => array(
                    'default' => $courseconfig->numsections,
                    'type' => PARAM_INT,
                ),
                'hiddensections' => array(
                    'default' => $courseconfig->hiddensections,
                    'type' => PARAM_INT,
                ),
                'coursedisplay' => array(
                    'default' => $courseconfig->coursedisplay,
                    'type' => PARAM_INT,
                ),
                'headingimage' => array(
                    'default' => get_config('format_nead_unicentro', 'headingimage'),
                    'type' => PARAM_CLEANFILE,
                ),
                'headinginfo' => array(
                    'default' => get_config('format_nead_unicentro', 'headinginfo'),
                    'type' => PARAM_RAW,
                ),
                'teachers' => array(
                    'default' => get_config('format_nead_unicentro', 'teachers'),
                    'type' => PARAM_NOTAGS,
                ),
                'period' => array(
                    'default' => get_config('format_nead_unicentro', 'period'),
                    'type' => PARAM_NOTAGS,
                ),
            );
        }
        if ($foreditform && !isset($courseformatoptions['coursedisplay']['label'])) {
            $courseconfig = get_config('moodlecourse');
            $max = $courseconfig->maxsections;
            if (!isset($max) || !is_numeric($max)) {
                $max = 52;
            }
            $sectionmenu = array();
            for ($i = 0; $i <= $max; $i++) {
                $sectionmenu[$i] = "$i";
            }
            $courseformatoptionsedit = array(
                'numsections' => array(
                    'label' => new lang_string('numberweeks'),
                    'element_type' => 'select',
                    'element_attributes' => array($sectionmenu),
                ),
                'hiddensections' => array(
                    'label' => new lang_string('hiddensections'),
                    'help' => 'hiddensections',
                    'help_component' => 'moodle',
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            0 => new lang_string('hiddensectionscollapsed'),
                            1 => new lang_string('hiddensectionsinvisible')
                        )
                    ),
                ),
                'coursedisplay' => array(
                    'label' => new lang_string('coursedisplay'),
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            COURSE_DISPLAY_SINGLEPAGE => new lang_string('coursedisplay_single'),
                            COURSE_DISPLAY_MULTIPAGE => new lang_string('coursedisplay_multi')
                        )
                    ),
                    'help' => 'coursedisplay',
                    'help_component' => 'moodle',
                ),
                'headingimage' => array(
                    'label' => new lang_string('headingimage', 'format_nead_unicentro'),
                    'element_type' => 'filemanager',
                    'help' => 'headingimage',
                    'help_component' => 'format_nead_unicentro',
                ),
                'headinginfo' => array(
                    'label' => new lang_string('headinginfo', 'format_nead_unicentro'),
                    'element_type' => 'htmleditor',
                    'help' => 'headinginfo',
                    'help_component' => 'format_nead_unicentro',
                ),
                'teachers' => array(
                    'label' => new lang_string('teachers', 'format_nead_unicentro'),
                    'element_type' => 'text',
                    'help' => 'teachers',
                    'help_component' => 'format_nead_unicentro',
                ),
                'period' => array(
                    'label' => new lang_string('period', 'format_nead_unicentro'),
                    'element_type' => 'text',
                    'help' => 'period',
                    'help_component' => 'format_nead_unicentro',
                ),
            );
            $courseformatoptions = array_merge_recursive($courseformatoptions, $courseformatoptionsedit);
        }
        return $courseformatoptions;
    }

    /**
     * Adds format options elements to the course/section edit form.
     *
     * This function is called from {@link course_edit_form::definition_after_data()}.
     *
     * @param MoodleQuickForm $mform form the elements are added to.
     * @param bool $forsection 'true' if this is a section edit form, 'false' if this is course edit form.
     * @return array array of references to the added form elements.
     */
    public function create_edit_form_elements(&$mform, $forsection = false) {
	global $course, $CFG;
        $elements = parent::create_edit_form_elements($mform, $forsection);

        
	$options = array(
		'maxfiles' => $CFG->courseoverviewfileslimit,
		'maxbytes' => $CFG->maxbytes,  
		'subdirs' => 0,
		'accepted_types' =>  '*'
	);

	//$context = context_course::instance($course->id);
	//file_prepare_standard_filemanager($course, 'imageheader2', $options, $context, 'format_nead_unicentro', 'imageheader2', 0);
	//$elements[] = $mform->addElement('filemanager', 'imageheader2_filemanager', 'Imagem de cabeçalho', null, $options);
	//$mform->setDefault('imageheader2_filemanager', $course->imageheader2_filemanager);
	
        // Increase the number of sections combo box values if the user has increased the number of sections
        // using the icon on the course page beyond course 'maxsections' or course 'maxsections' has been
        // reduced below the number of sections already set for the course on the site administration course
        // defaults page.  This is so that the number of sections is not reduced leaving unintended orphaned
        // activities / resources.
        if (!$forsection) {
            $maxsections = get_config('moodlecourse', 'maxsections');
            $numsections = $mform->getElementValue('numsections');
            $numsections = $numsections[0];
            if ($numsections > $maxsections) {
                $element = $mform->getElement('numsections');
                for ($i = $maxsections+1; $i <= $numsections; $i++) {
                    $element->addOption("$i", $i);
                }
            }
        }
        return $elements;
    }

    /**
     * Updates format options for a course
     *
     * In case if course format was changed to 'nead_unicentro', we try to copy options
     * 'coursedisplay', 'numsections' and 'hiddensections' from the previous format.
     * If previous course format did not have 'numsections' option, we populate it with the
     * current number of sections
     *
     * @param stdClass|array $data return value from {@link moodleform::get_data()} or array with data
     * @param stdClass $oldcourse if this function is called from {@link update_course()}
     *     this object contains information about the course before update
     * @return bool whether there were any changes to the options values
     */
    public function update_course_format_options($data, $oldcourse = null) {
        global $course, $CFG, $DB;

	$options = array(
		'maxfiles' => $CFG->courseoverviewfileslimit,
		'maxbytes' => $CFG->maxbytes,
		'subdirs' => 0,
		'accepted_types' =>  '*'
	);
	$context = context_course::instance($this->courseid);
	$saved = file_save_draft_area_files($data->headingimage, $context->id, 'format_nead_unicentro', 'headingimage', 0, array('subdirs' => 0, 'maxfiles' => 1));
		
        if ($oldcourse !== null) {
            $data = (array)$data;
            $oldcourse = (array)$oldcourse;
            $options = $this->course_format_options();
            foreach ($options as $key => $unused) {
                if (!array_key_exists($key, $data)) {
                    if (array_key_exists($key, $oldcourse)) {
                        $data[$key] = $oldcourse[$key];
                    } else if ($key === 'numsections') {
                        // If previous format does not have the field 'numsections'
                        // and $data['numsections'] is not set,
                        // we fill it with the maximum section number from the DB
                        $maxsection = $DB->get_field_sql('SELECT max(section) from {course_sections}
                            WHERE course = ?', array($this->courseid));
                        if ($maxsection) {
                            // If there are no sections, or just default 0-section, 'numsections' will be set to default
                            $data['numsections'] = $maxsection;
                        }
                    }
                }
            }
        }
        return $this->update_format_options($data);
    }

    /**
     * Allows course format to execute code on moodle_page::set_course()
     *
     * @param moodle_page $page instance of page calling set_course
     */
    public function page_set_course(moodle_page $page) {
      $page->requires->jquery();
      $page->requires->jquery_plugin('bootstrap', 'format_nead_unicentro');
      $page->requires->jquery_plugin('jquery.cookie', 'format_nead_unicentro');
      $page->requires->js('/course/format/nead_unicentro/javascript/tabs.js');
      $page->requires->js('/course/format/nead_unicentro/javascript/fix.js');
    }

}


function format_nead_unicentro_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    global $DB;
       
    if ($context->contextlevel != CONTEXT_COURSE) {
        return false;
    }
    
    require_login();
    if ($filearea != 'headingimage') {
        return false;
    }
    
    $itemid = (int)array_shift($args);
    if ($itemid != 0) {
        return false;
    }
    
    $fs = get_file_storage();
    $filename = array_pop($args);
    
    if (empty($args)) {
        $filepath = '/';
    } else {
        $filepath = '/'.implode('/', $args).'/';
    }
    
    $file = $fs->get_file($context->id, 'format_nead_unicentro', $filearea, $itemid, $filepath, $filename);
    if (!$file) {
        return false;
    }
    
    send_stored_file($file, 0, 0, $forcedownload, $options);
}
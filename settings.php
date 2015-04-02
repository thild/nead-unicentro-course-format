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
// GNU General Public License for nead_unicentro details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Theme Nead/Unicentro settings.
 * @package    format_nead_unicentro
 * @copyright  Tony Alexander Hild
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    // Heading file setting.
    $name = 'format_nead_unicentro/defaultheadingimage';
    $title = get_string('defaultheadingimage', 'format_nead_unicentro');
    $description = get_string('defaultheadingimage_desc', 'format_nead_unicentro');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'defaultheadingimage');
    $setting->set_updatedcallback('format_reset_all_caches');
    $settings->add($setting);
    
}

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

namespace mod_millionaire\completion;

use core_completion\activity_custom_completion;
use mod_millionaire\util;

/**
 * Activity custom completion subclass for the millionaire activity.
 *
 * @package    mod_millionaire
 * @copyright  2019 Benedikt Kulmann <b@kulmann.biz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class custom_completion extends activity_custom_completion {

    /**
     * Fetches the completion state for a given completion rule.
     *
     * @param string $rule The completion rule.
     * @return int The completion state.
     */
    public function get_state(string $rule): int {
        $this->validate_rule($rule);

        $game = util::get_game($this->cm);
        $userid = $this->userid;

        switch ($rule) {
            case 'completionrounds':
                $requiredrounds = $game->get_completionrounds();
                if ($requiredrounds > 0) {
                    $finishedrounds = $game->count_finished_gamesessions($userid);
                    return ($finishedrounds >= $requiredrounds)
                        ? COMPLETION_COMPLETE : COMPLETION_INCOMPLETE;
                }
                return COMPLETION_COMPLETE;

            case 'completionpoints':
                $requiredpoints = $game->get_completionpoints();
                if ($requiredpoints > 0) {
                    $totalscore = $game->calculate_total_score($userid);
                    return ($totalscore >= $requiredpoints)
                        ? COMPLETION_COMPLETE : COMPLETION_INCOMPLETE;
                }
                return COMPLETION_COMPLETE;
        }

        return COMPLETION_INCOMPLETE;
    }

    /**
     * Fetch the list of custom completion rules that this module defines.
     *
     * @return array
     */
    public static function get_defined_custom_rules(): array {
        return ['completionrounds', 'completionpoints'];
    }

    /**
     * Returns an associative array of the descriptions of custom completion rules.
     *
     * @return array
     */
    public function get_custom_rule_descriptions(): array {
        $completionrounds = $this->cm->customdata->customcompletionrules['completionrounds'] ?? 0;
        $completionpoints = $this->cm->customdata->customcompletionrules['completionpoints'] ?? 0;

        return [
            'completionrounds' => get_string('completionrounds', 'millionaire') . ': ' . $completionrounds,
            'completionpoints' => get_string('completionpoints', 'millionaire') . ': ' . $completionpoints,
        ];
    }

    /**
     * Returns an array of all completion rules, in the order they should be displayed to users.
     *
     * @return array
     */
    public function get_sort_order(): array {
        return ['completionrounds', 'completionpoints'];
    }
}

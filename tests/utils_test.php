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

namespace mod_studentquiz;

defined('MOODLE_INTERNAL') || die('Direct Access is forbidden!');

global $CFG;
require_once($CFG->dirroot . '/mod/studentquiz/locallib.php');
require_once($CFG->dirroot . '/mod/studentquiz/viewlib.php');
require_once($CFG->dirroot . '/mod/studentquiz/reportlib.php');

/**
 * Unit tests for mod/studentquiz/classes/utils.php.
 *
 * @package    mod_studentquiz
 * @copyright  2017 HSR (http://www.hsr.ch)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class utils_test extends \advanced_testcase {

    /**
     * Test replace html tags.
     *
     * @dataProvider remove_html_tag_provider
     *
     * @covers ::html_to_text
     * @param string $result The data to test
     * @param string $expected The expected result
     */
    public function test_remove_html_tag(string $result, string $expected) {
        $result = utils::html_to_text($result);
        $this->assertEquals($expected, $result);
    }

    /**
     * Data provider for test_remove_html_tag.
     *
     * @return array
     */
    public function remove_html_tag_provider(): array {
        return [
            'Normal html tags' => [
                "<p>String</p>with html<br /> tag",
                "String with html tag"
            ],
            'Orderedlist and paragraph tags' => [
                "<p>paragraph 1</p><ul><li>item 1</li><li>item 2</li></ul><p>paragraph 2</p>".
                "<ol><li>item 1</li><li>item 2</li></ol>",
                "paragraph 1 item 1 item 2 paragraph 2 item 1 item 2"
            ],
            'Orderedlist with multi level' => [
                "<p>paragraph 1</p><ol><li>item 1<ol><li>subitem 1</li>".
                "<li>subitem 2<ol><li>sub subitem 1</li><li>sub subitem 2</li></ol></li></ol></li>".
                "<li>item 2</li></ol><p>paragraph 2</p><ul><li>item 1</li><li>item 2</li></ul>",
                "paragraph 1 item 1 subitem 1 subitem 2 sub subitem 1 sub subitem 2 item 2 paragraph 2 item 1 item 2"
            ],
            'Heading tags' => [
                "<h1>heading 1</h1><h2>heading 2</h2><h3>heading 3</h3><h4>heading 4</h4>".
                "<h5>heading 5</h5><h6>heading 6</h6><p>paragraph 1</p><br /><p>paragraph 2</p>",
                "heading 1 heading 2 heading 3 heading 4 heading 5 heading 6 paragraph 1 paragraph 2"
            ],
            'Content with img tag' => [
                "a picture <img src='https://example.com/bg.jpg' />",
                "a picture <img src='https://example.com/bg.jpg' />"
            ],
            'Mixed content' => [
                "<h1>heading 1</h1><h2>heading 2</h2>with a picture ".
                "<img src='https://example.com/bg.jpg' /><br /> and<p>paragraph 1</p>",
                "heading 1 heading 2 with a picture <img src='https://example.com/bg.jpg' /> and paragraph 1"
            ]
        ];
    }

    /**
     * Test replace html tags.
     *
     * @dataProvider nice_shorten_text_provider
     *
     * @covers ::nice_shorten_text
     * @param string $result The data to test
     * @param string $expected The expected result
     */
    public function test_nice_shorten_text(array $result, string $expected) {
        [$text, $length] = $result;
        $result = utils::nice_shorten_text($text, $length);
        $this->assertEquals($expected, $result);
    }

    /**
     * Data provider for test_remove_html_tag.
     *
     * @return array
     */
    public function nice_shorten_text_provider(): array {
        return [
            'Normal text already short enough' => [
                ["This is the normal text", 40],
                "This is the normal text"
            ],
            'Normal text needs shortening' => [
                ["This is the normal text", 20],
                "This is the..."
            ],
            'Line break text already short enough' => [
                [
                    "This is the normal text
                    with line break",
                    40
                ],
                "This is the normal text with line break"
            ],
            'Line break text needs shortening' => [
                [
                    "This is the normal text
                    with line break",
                    20
                ],
                "This is the..."
            ],
            'Normal text with image tag already short enough' => [
                ["<img src='sample.png' alt='Sample Image'> This is simple text", 40],
                " [Image] This is simple text"
            ],
            'Normal text with image tag needs shortening' => [
                ["<img src='sample.png' alt='Sample Image'> This is simple text", 20],
                " [Image] This is..."
            ]
        ];
    }
}

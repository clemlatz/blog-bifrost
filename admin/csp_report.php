<?php
/**
 * @package Dotclear
 * @subpackage Backend
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */

use Dotclear\Helper\File\Files;
use Dotclear\Helper\File\Path;

require __DIR__ . '/../inc/admin/prepend.php';

// From: https://github.com/nico3333fr/CSP-useful
//
// Note: this script requires PHP ≥ 5.4.
// Inspired from https://mathiasbynens.be/notes/csp-reports

class adminCSPReport
{
    /**
     * Initializes the page.
     */
    public static function init()
    {
        // Specify admin CSP log file if necessary
        if (!defined('DC_CSP_LOGFILE')) {
            define('DC_CSP_LOGFILE', Path::real(DC_VAR) . '/csp/csp_report.json');
        }

        // Dareboost wants it? Not a problem.
        header('X-Content-Type-Options: "nosniff"');
    }

    /**
     * Processes the request(s).
     */
    public static function process()
    {
        // Get the raw POST data
        $data = file_get_contents('php://input');

        // Only continue if it’s valid JSON that is not just `null`, `0`, `false` or an
        // empty string, i.e. if it could be a CSP violation report.
        if ($data = json_decode($data, true, 512, JSON_THROW_ON_ERROR)) {
            // get source-file and blocked-URI to perform some tests
            $source_file        = $data['csp-report']['source-file']        ?? '';
            $line_number        = $data['csp-report']['line-number']        ?? '';
            $blocked_uri        = $data['csp-report']['blocked-uri']        ?? '';
            $document_uri       = $data['csp-report']['document-uri']       ?? '';
            $violated_directive = $data['csp-report']['violated-directive'] ?? '';

            if (
                // avoid false positives notifications coming from Chrome extensions (Wappalyzer, MuteTab, etc.)
                // bug here https://code.google.com/p/chromium/issues/detail?id=524356
                strpos($source_file, 'chrome-extension://') === false

                // avoid false positives notifications coming from Safari extensions (diigo, evernote, etc.)
                && strpos($source_file, 'safari-extension://') === false
                && strpos($blocked_uri, 'safari-extension://') === false

                // search engine extensions ?
                && strpos($source_file, 'se-extension://') === false

                // added by browsers in webviews
                && strpos($blocked_uri, 'webviewprogressproxy://') === false

                // Google Search App see for details https://github.com/nico3333fr/CSP-useful/commit/ecc8f9b0b379ae643bc754d2db33c8b47e185fd1
                && strpos($blocked_uri, 'gsa://onpageload') === false

            ) {
                // Prepare report data (hash => info)
                $hash = hash('md5', $blocked_uri . $document_uri . $source_file . $line_number . $violated_directive);

                try {
                    // Check report dir (create it if necessary)
                    Files::makeDir(dirname(DC_CSP_LOGFILE), true);

                    // Check if report is not already stored in log file
                    $contents = '';
                    if (file_exists(DC_CSP_LOGFILE)) {
                        $contents = file_get_contents(DC_CSP_LOGFILE);
                        if ($contents) {
                            if (substr($contents, -1) == ',') {
                                // Remove final comma if present
                                $contents = substr($contents, 0, -1);
                            }
                            if ($contents != '') {
                                $list = json_decode('[' . $contents . ']', true);
                                if (is_array($list)) {
                                    foreach ($list as $value) {
                                        if (isset($value['hash']) && $value['hash'] == $hash) {
                                            // Already stored, ignore
                                            return;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    // Add report to the file
                    if (!($fp = @fopen(DC_CSP_LOGFILE, 'a'))) {
                        // Unable to open file, ignore
                        return;
                    }

                    // Prettify the JSON-formatted data
                    $violation = array_merge(['hash' => $hash], $data['csp-report']);
                    $output    = json_encode($violation, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

                    // The file content will have to be enclosed in brackets [] before
                    // beeing decoded with json_decoded(<content>,true);
                    fprintf($fp, ($contents != '' ? ',' : '') . '%s', $output);
                } catch (Exception $e) {
                    return;
                }
            }
        }
    }
}

adminCSPReport::init();
adminCSPReport::process();

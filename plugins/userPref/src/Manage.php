<?php
/**
 * @brief userPref, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
declare(strict_types=1);

namespace Dotclear\Plugin\userPref;

use Exception;
use dcCore;
use dcPage;
use dcNsProcess;
use dcWorkspace;
use Dotclear\Helper\Html\Html;
use Dotclear\Helper\Network\Http;
use form;

class Manage extends dcNsProcess
{
    /**
     * Initializes the page.
     */
    public static function init(): bool
    {
        if (defined('DC_CONTEXT_ADMIN')) {
            dcCore::app()->admin->part = !empty($_GET['part']) && $_GET['part'] == 'global' ? 'global' : 'local';
            static::$init              = true;
        }

        return static::$init;
    }

    /**
     * Processes the request(s).
     */
    public static function process(): bool
    {
        if (!static::$init) {
            return false;
        }

        // Local navigation
        if (!empty($_POST['gp_nav'])) {
            Http::redirect(dcCore::app()->admin->getPageURL() . $_POST['gp_nav']);
            exit;
        }
        if (!empty($_POST['lp_nav'])) {
            Http::redirect(dcCore::app()->admin->getPageURL() . $_POST['lp_nav']);
            exit;
        }

        // Local prefs update
        if (!empty($_POST['s']) && is_array($_POST['s'])) {
            try {
                foreach ($_POST['s'] as $ws => $s) {
                    foreach ($s as $k => $v) {
                        if ($_POST['s_type'][$ws][$k] === dcWorkspace::WS_ARRAY) {
                            $v = json_decode($v, true, 512, JSON_THROW_ON_ERROR);
                        }
                        dcCore::app()->auth->user_prefs->$ws->put($k, $v);
                    }
                }

                dcPage::addSuccessNotice(__('Preferences successfully updated'));
                Http::redirect(dcCore::app()->admin->getPageURL());
            } catch (Exception $e) {
                dcCore::app()->error->add($e->getMessage());
            }
        }

        // Global prefs update
        if (!empty($_POST['gs']) && is_array($_POST['gs'])) {
            try {
                foreach ($_POST['gs'] as $ws => $s) {
                    foreach ($s as $k => $v) {
                        if ($_POST['gs_type'][$ws][$k] === dcWorkspace::WS_ARRAY) {
                            $v = json_decode($v, true, 512, JSON_THROW_ON_ERROR);
                        }
                        dcCore::app()->auth->user_prefs->$ws->put($k, $v, null, null, true, true);
                    }
                }

                dcPage::addSuccessNotice(__('Preferences successfully updated'));
                Http::redirect(dcCore::app()->admin->getPageURL() . '&part=global');
            } catch (Exception $e) {
                dcCore::app()->error->add($e->getMessage());
            }
        }

        return true;
    }

    /**
     * Renders the page.
     */
    public static function render(): void
    {
        dcPage::openModule(
            'user:preferences',
            dcPage::jsPageTabs(dcCore::app()->admin->part) .
            dcPage::jsModuleLoad('userPref/js/index.js')
        );

        echo
        dcPage::breadcrumb(
            [
                __('System')                                    => '',
                Html::escapeHTML(dcCore::app()->auth->userID()) => '',
                __('user:preferences')                          => '',
            ]
        ) .
        dcPage::notices() .
        '<div id="local" class="multi-part" title="' . __('User preferences') . '">' .
        '<h3 class="out-of-screen-if-js">' . __('User preferences') . '</h3>';

        self::prefsTable(false);

        echo
        '</div>' .

        '<div id="global" class="multi-part" title="' . __('Global preferences') . '">' .
        '<h3 class="out-of-screen-if-js">' . __('Global preferences') . '</h3>';

        self::prefsTable(true);

        echo
        '</div>';

        dcPage::helpBlock('userPref');

        dcPage::closeModule();
    }

    /**
     * Display local or global settings
     *
     * @param      bool  $global  The global
     */
    protected static function prefsTable(bool $global = false): void
    {
        $table_header = '<div class="table-outer"><table class="prefs" id="%s"><caption class="as_h3">%s</caption>' .
            '<thead>' .
            '<tr>' . "\n" .
            '  <th class="nowrap">' . __('Setting ID') . '</th>' . "\n" .
            '  <th>' . __('Value') . '</th>' . "\n" .
            '  <th>' . __('Type') . '</th>' . "\n" .
            '  <th>' . __('Description') . '</th>' . "\n" .
            '</tr>' . "\n" .
            '</thead>' . "\n" .
            '<tbody>';
        $table_footer = '</tbody></table></div>';

        /** @var array<string|dcWorkspace> */
        $workspaces = dcCore::app()->auth->user_prefs->dumpWorkspaces();
        $prefs      = [];
        if ($global) {
            $prefix     = 'g_';
            $prefix_id  = '#' . $prefix;
            $field_name = 'gs';
            $nav_id     = 'gp_nav';
            $submit_id  = 'gp_submit';

            foreach ($workspaces as $ws => $workspace) {
                foreach ($workspace->dumpGlobalPrefs() as $k => $v) {
                    $prefs[$ws][$k] = $v;
                }
            }
        } else {
            $prefix     = 'l_';
            $prefix_id  = '#' . $prefix;
            $field_name = 's';
            $nav_id     = 'lp_nav';
            $submit_id  = 'lp_submit';

            foreach ($workspaces as $ws => $workspace) {
                foreach ($workspace->dumpPrefs() as $k => $v) {
                    $prefs[$ws][$k] = $v;
                }
            }
        }

        ksort($prefs, SORT_FLAG_CASE | SORT_STRING);
        if (count($prefs)) {
            $ws_combo = [];
            foreach ($prefs as $ws => $s) {
                $ws_combo[$ws] = $prefix_id . $ws;
            }
            echo
            '<form action="' . dcCore::app()->adminurl->get('admin.plugin') . '" method="post" class="anchor-nav-sticky">' .
            '<p class="anchor-nav">' .
            '<label for="' . $nav_id . '" class="classic">' . __('Goto:') . '</label> ' .
            form::combo($nav_id, $ws_combo, ['class' => 'navigation']) .
            ' <input type="submit" value="' . __('Ok') . '" id="' . $submit_id . '" />' .
            '<input type="hidden" name="p" value="userPref" />' .
            dcCore::app()->formNonce() .
            '</p></form>';
        }

        echo
        '<form action="' . dcCore::app()->adminurl->get('admin.plugin') . '" method="post">';
        foreach ($prefs as $ws => $s) {
            ksort($s);
            echo sprintf($table_header, $prefix . $ws, $ws);
            foreach ($s as $k => $v) {
                $strong = $global ? false : !$v['global'];
                echo self::prefLine($k, $v, $ws, $field_name, $strong);
            }
            echo $table_footer;
        }

        echo
        '<p><input type="submit" value="' . __('Save') . '" />' .
        '<input type="button" value="' . __('Cancel') . '" class="go-back reset hidden-if-no-js" />' .
        '<input type="hidden" name="p" value="userPref" />' .
        dcCore::app()->formNonce() .
        '</p></form>';
    }

    /**
     * Return table line (td) to display a setting
     *
     * @param      string  $id            The identifier
     * @param      array   $s             The setting
     * @param      string  $ws            The workspace
     * @param      string  $field_name    The field name
     * @param      bool    $strong_label  The strong label
     *
     * @return     string
     */
    protected static function prefLine(string $id, array $s, string $ws, string $field_name, bool $strong_label): string
    {
        switch ($s['type']) {
            case dcWorkspace::WS_BOOLEAN:
            case dcWorkspace::WS_BOOL:
                $field = form::combo(
                    [$field_name . '[' . $ws . '][' . $id . ']', $field_name . '_' . $ws . '_' . $id],
                    [__('yes') => 1, __('no') => 0],
                    $s['value'] ? 1 : 0
                );

                break;

            case dcWorkspace::WS_ARRAY:
                $field = form::field(
                    [$field_name . '[' . $ws . '][' . $id . ']', $field_name . '_' . $ws . '_' . $id],
                    40,
                    null,
                    Html::escapeHTML(json_encode($s['value'], JSON_THROW_ON_ERROR))
                );

                break;

            case dcWorkspace::WS_INTEGER:
            case dcWorkspace::WS_INT:
            case dcWorkspace::WS_FLOAT:
            case 'integer':
            case 'float':
                $field = form::number(
                    [$field_name . '[' . $ws . '][' . $id . ']', $field_name . '_' . $ws . '_' . $id],
                    null,
                    null,
                    Html::escapeHTML((string) $s['value'])
                );

                break;

            case dcWorkspace::WS_STRING:
            case dcWorkspace::WS_TEXT:
            default:
                $field = form::field(
                    [$field_name . '[' . $ws . '][' . $id . ']', $field_name . '_' . $ws . '_' . $id],
                    40,
                    null,
                    Html::escapeHTML($s['value'])
                );

                break;
        }

        $type = form::hidden(
            [$field_name . '_type' . '[' . $ws . '][' . $id . ']', $field_name . '_' . $ws . '_' . $id . '_type'],
            Html::escapeHTML($s['type'])
        );

        $slabel = $strong_label ? '<strong>%s</strong>' : '%s';

        return
            '<tr class="line">' .
            '<td scope="row"><label for="' . $field_name . '_' . $ws . '_' . $id . '">' . sprintf($slabel, Html::escapeHTML($id)) . '</label></td>' .
            '<td>' . $field . '</td>' .
            '<td>' . $s['type'] . $type . '</td>' .
            '<td>' . Html::escapeHTML($s['label']) . '</td>' .
            '</tr>';
    }
}

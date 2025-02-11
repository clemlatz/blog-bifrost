<?php
/**
 * @class Path
 *
 * Path manipulation utilities
 *
 * @package Dotclear
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
declare(strict_types=1);

namespace Dotclear\Helper\File;

class Path
{
    /**
     * Returns the real path of a file.
     *
     * If parameter $strict is true, file should exist. Returns false if
     * file does not exist.
     *
     * @param string    $filename        Filename
     * @param boolean    $strict    File should exists
     *
     * @return string|false
     */
    public static function real(string $filename, bool $strict = true)
    {
        $os = (DIRECTORY_SEPARATOR == '\\') ? 'win' : 'nix';

        # Absolute path?
        if ($os == 'win') {
            $absolute = preg_match('/^\w+:/', $filename);
        } else {
            $absolute = substr($filename, 0, 1) == '/';
        }

        # Standard path form
        if ($os == 'win') {
            $filename = str_replace('\\', '/', $filename);
        }

        # Adding root if !$_abs
        if (!$absolute) {
            $filename = dirname($_SERVER['SCRIPT_FILENAME']) . '/' . $filename;
        }

        # Clean up
        $filename = preg_replace('|/+|', '/', $filename);

        if (strlen($filename) > 1) {
            $filename = preg_replace('|/$|', '', $filename);
        }

        $prefix = '';
        if ($os == 'win') {
            [$prefix, $filename] = explode(':', $filename);
            $prefix .= ':/';
        } else {
            $prefix = '/';
        }
        $filename = substr($filename, 1);

        # Go through
        $parts = explode('/', $filename);
        $res   = [];

        for ($i = 0; $i < count($parts); $i++) {
            if ($parts[$i] == '.') {
                continue;
            }

            if ($parts[$i] == '..') {
                if (count($res) > 0) {
                    array_pop($res);
                }
            } else {
                array_push($res, $parts[$i]);
            }
        }

        $filename = $prefix . implode('/', $res);

        if ($strict && !@file_exists($filename)) {
            return false;
        }

        return $filename;
    }

    /**
     * Returns a clean file path
     *
     * @param string    $filename        File path
     *
     * @return string
     */
    public static function clean(?string $filename): string
    {
        // Remove double point (upper directory)
        $filename = preg_replace(['|^\.\.|', '|/\.\.|', '|\.\.$|'], '', (string) $filename);

        // Replace double slashes by one
        $filename = preg_replace('|/{2,}|', '/', (string) $filename);

        // Remove trailing slash
        $filename = preg_replace('|/$|', '', (string) $filename);

        return $filename;
    }

    /**
     * Path information
     *
     * Returns an array of information:
     * - dirname
     * - basename
     * - extension
     * - base (basename without extension)
     *
     * @param string    $filename        File path
     *
     * @return array
     */
    public static function info(string $filename): array
    {
        $pathinfo = pathinfo($filename);
        $res      = [];

        $res['dirname']   = (string) $pathinfo['dirname'];
        $res['basename']  = (string) $pathinfo['basename'];
        $res['extension'] = $pathinfo['extension'] ?? '';
        $res['base']      = preg_replace('/\.' . preg_quote($res['extension'], '/') . '$/', '', $res['basename']);

        return $res;
    }

    /**
     * Full path with root
     *
     * Returns a path with root concatenation unless path begins with a slash
     *
     * @param string    $path       File path
     * @param string    $root       Root path
     *
     * @return string
     */
    public static function fullFromRoot(string $path, string $root): string
    {
        if (substr($path, 0, 1) == '/') {
            return $path;
        }

        return $root . '/' . $path;
    }
}

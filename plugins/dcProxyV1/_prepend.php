<?php
/**
 * @brief dcProxyV1, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
class dcProxyV1
{
    public static function classAliases(array $aliases)
    {
        foreach ($aliases as $aliasName => $realName) {
            if (!class_exists($aliasName)) {
                class_alias($realName, $aliasName);
            }
        }
    }
}

// Classes aliases
dcProxyV1::classAliases([
    // alias → real name (including namespace if necessary, for both)

    // Deprecated since 2.26
    'Clearbricks' => 'Dotclear\Helper\Clearbricks',

    // Form helpers
    'formButton'    => 'Dotclear\Helper\Html\Form\Button',
    'formCheckbox'  => 'Dotclear\Helper\Html\Form\Checkbox',
    'formColor'     => 'Dotclear\Helper\Html\Form\Color',
    'formComponent' => 'Dotclear\Helper\Html\Form\Component',
    'formDate'      => 'Dotclear\Helper\Html\Form\Date',
    'formDatetime'  => 'Dotclear\Helper\Html\Form\Datetime',
    'formDiv'       => 'Dotclear\Helper\Html\Form\Div',
    'formEmail'     => 'Dotclear\Helper\Html\Form\Email',
    'formFieldset'  => 'Dotclear\Helper\Html\Form\Fieldset',
    'formFile'      => 'Dotclear\Helper\Html\Form\File',
    'formForm'      => 'Dotclear\Helper\Html\Form\Form',
    'formHidden'    => 'Dotclear\Helper\Html\Form\Hidden',
    'formInput'     => 'Dotclear\Helper\Html\Form\Input',
    'formLabel'     => 'Dotclear\Helper\Html\Form\Label',
    'formLegend'    => 'Dotclear\Helper\Html\Form\Legend',
    'formLink'      => 'Dotclear\Helper\Html\Form\Link',
    'formNote'      => 'Dotclear\Helper\Html\Form\Note',
    'formNumber'    => 'Dotclear\Helper\Html\Form\Number',
    'formOptgroup'  => 'Dotclear\Helper\Html\Form\Optgroup',
    'formOption'    => 'Dotclear\Helper\Html\Form\Option',
    'formPara'      => 'Dotclear\Helper\Html\Form\Para',
    'formPassword'  => 'Dotclear\Helper\Html\Form\Password',
    'formRadio'     => 'Dotclear\Helper\Html\Form\Radio',
    'formSelect'    => 'Dotclear\Helper\Html\Form\Select',
    'formSubmit'    => 'Dotclear\Helper\Html\Form\Submit',
    'formText'      => 'Dotclear\Helper\Html\Form\Text',
    'formTextarea'  => 'Dotclear\Helper\Html\Form\Textarea',
    'formTime'      => 'Dotclear\Helper\Html\Form\Time',
    'formUrl'       => 'Dotclear\Helper\Html\Form\Url',

    // Diff helpers
    'diff'          => 'Dotclear\Helper\Diff\Diff',
    'tidyDiff'      => 'Dotclear\Helper\Diff\TidyDiff',
    'tidyDiffChunk' => 'Dotclear\Helper\Diff\TidyDiffChunk',
    'tidyDiffLine'  => 'Dotclear\Helper\Diff\TidyDiffLine',

    // Crypt helpers
    'crypt' => 'Dotclear\Helper\Crypt',

    // Mail helpers
    'mail'       => 'Dotclear\Helper\Network\Mail\Mail',
    'socketMail' => 'Dotclear\Helper\Network\Mail\MailSocket',

    // Pager helpers
    'pager' => 'Dotclear\Helper\Html\Pager',

    // XmlTag helpers
    'xmlTag' => 'Dotclear\Helper\Html\XmlTag',

    // Rest helpers
    'restServer' => 'Dotclear\Helper\RestServer',

    // Text helpers
    'text' => 'Dotclear\Helper\Text',

    // Files and Path, … helpers
    'files'       => 'Dotclear\Helper\File\Files',
    'path'        => 'Dotclear\Helper\File\Path',
    'filemanager' => 'Dotclear\Helper\File\Manager',
    'fileItem'    => 'Dotclear\Helper\File\File',

    // Html helpers
    'html'       => 'Dotclear\Helper\Html\Html',
    'htmlFilter' => 'Dotclear\Helper\Html\HtmlFilter',

    // Mail helpers
    'http' => 'Dotclear\Helper\Network\Http',

    // Wiki helpers
    'wiki2xhtml' => 'Dotclear\Helper\Html\WikiToHtml',

    // Simple Template Systeme
    'template'               => 'Dotclear\Helper\Html\Template\Template',
    'tplNode'                => 'Dotclear\Helper\Html\Template\TplNode',
    'tplNodeBlock'           => 'Dotclear\Helper\Html\Template\TplNodeBlock',
    'tplNodeText'            => 'Dotclear\Helper\Html\Template\TplNodeText',
    'tplNodeValue'           => 'Dotclear\Helper\Html\Template\TplNodeValue',
    'tplNodeBlockDefinition' => 'Dotclear\Helper\Html\Template\TplNodeBlockDefinition',
    'tplNodeValueParent'     => 'Dotclear\Helper\Html\Template\TplNodeValueParent',

    // HTML Validation
    'htmlValidator' => 'Dotclear\Helper\Html\HtmlValidator',

    // Socket
    'netSocket' => 'Dotclear\Helper\Network\Socket\Socket',

    // L10n
    'l10n' => 'Dotclear\Helper\L10n',

    // Image helpers
    'imageMeta'  => 'Dotclear\Helper\File\Image\ImageMeta',
    'imageTools' => 'Dotclear\Helper\File\Image\ImageTools',

    // URL Handler
    'urlHandler' => 'Dotclear\Helper\Network\UrlHandler',

    // net HTTP Client
    'netHttp' => 'Dotclear\Helper\Network\HttpClient',

    // XML-RPC helper
    'xmlrpcException'           => 'Dotclear\Helper\Network\XmlRpc\XmlRpcException',
    'xmlrpcValue'               => 'Dotclear\Helper\Network\XmlRpc\Value',
    'xmlrpcMessage'             => 'Dotclear\Helper\Network\XmlRpc\Message',
    'xmlrpcRequest'             => 'Dotclear\Helper\Network\XmlRpc\Request',
    'xmlrpcDate'                => 'Dotclear\Helper\Network\XmlRpc\Date',
    'xmlrpcBase64'              => 'Dotclear\Helper\Network\XmlRpc\Base64',
    'xmlrpcClient'              => 'Dotclear\Helper\Network\XmlRpc\Client',
    'xmlrpcClientMulticall'     => 'Dotclear\Helper\Network\XmlRpc\ClientMulticall',
    'xmlrpcBasicServer'         => 'Dotclear\Helper\Network\XmlRpc\BasicServer',
    'xmlrpcIntrospectionServer' => 'Dotclear\Helper\Network\XmlRpc\IntrospectionServer',

    // Feed Helpers
    'feedParser' => 'Dotclear\Helper\Network\Feed\Parser',
    'feedReader' => 'Dotclear\Helper\Network\Feed\Reader',

    // Date helpers
    'dt' => 'Dotclear\Helper\Date',

    // Zip helpers
    'fileZip'   => 'Dotclear\Helper\File\Zip\Zip',
    'fileUnzip' => 'Dotclear\Helper\File\Zip\Unzip',

    // Database -------------------

    'dcSqlStatement'      => 'Dotclear\Database\Statement\SqlStatement',
    'dcSelectStatement'   => 'Dotclear\Database\Statement\SelectStatement',
    'dcJoinStatement'     => 'Dotclear\Database\Statement\JoinStatement',
    'dcUpdateStatement'   => 'Dotclear\Database\Statement\UpdateStatement',
    'dcInsertStatement'   => 'Dotclear\Database\Statement\InsertStatement',
    'dcDeleteStatement'   => 'Dotclear\Database\Statement\DeleteStatement',
    'dcTruncateStatement' => 'Dotclear\Database\Statement\TruncateStatement',

    'sessionDB' => 'Dotclear\Database\Session',

    'cursor'          => 'Dotclear\Database\Cursor',
    'record'          => 'Dotclear\Database\Record',
    'staticRecord'    => 'Dotclear\Database\StaticRecord',
    'extStaticRecord' => 'Dotclear\Database\StaticRecord',

    'i_dbLayer' => 'Dotclear\Database\InterfaceHandler',
    'dbLayer'   => 'Dotclear\Database\AbstractHandler',

    'i_dbSchema' => 'Dotclear\Database\InterfaceSchema',
    'dbSchema'   => 'Dotclear\Database\AbstractSchema',

    'dbStruct'      => 'Dotclear\Database\Structure',
    'dbStructTable' => 'Dotclear\Database\Table',

    // Core -----------------------

    'dcRecord' => 'Dotclear\Database\MetaRecord',
]);

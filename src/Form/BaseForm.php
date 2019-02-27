<?php
/**
 * LoginForm.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Form;

use Zend\Form\Form;
use Zend\I18n\Translator\Resources;
use Zend\Validator\Translator;
use Zend\Validator\AbstractValidator;

use Cinemasunshine\PortalAdmin\Translator\ValidatorTranslator;

/**
 * Base form class
 */
class BaseForm extends Form
{
    /** @var array */
    public static $imageMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
    ];
    
    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();
        
        $translator = new ValidatorTranslator();
        $translationFile = Resources::getBasePath() . sprintf(Resources::getPatternForValidator(), 'ja');
        $translator->addTranslationFile(
            'phpArray',
            $translationFile
        );

        AbstractValidator::setDefaultTranslator($translator);
    }
    
    /**
     * build data
     *
     * 配列の添字は文字列しておく（特にネストされたフィールドについて）
     * NG: <input type="text" name="items[0][name]">
     * OK: <input type="text" name="items[str][name]">
     *
     * @param array $params
     * @param array $uploadedFiles
     * @return void
     */
    public static function buildData(array $params, array $uploadedFiles)
    {
        return array_merge_recursive(
            $params,
            BaseForm::parseUploadedFiles($uploadedFiles)
        );
    }
    
    /**
     * parse uploaded files
     *
     * ネストされたfileの値をパースするために作成。
     *
     * @param array $uploadedFiles
     * @return void
     */
    private static function parseUploadedFiles(array $uploadedFiles)
    {
        $parsed = [];
        
        foreach ($uploadedFiles as $field => $uploadedFile) {
            if (!isset($uploadedFile['error'])) {
                if (is_array($uploadedFile)) {
                    $parsed[$field] = static::parseUploadedFiles($uploadedFile);
                }
                continue;
            }

            $parsed[$field] = [];
            if (!is_array($uploadedFile['error'])) {
                $parsed[$field] = [
                    'tmp_name' => $uploadedFile['tmp_name'],
                    'name'     => isset($uploadedFile['name']) ? $uploadedFile['name'] : null,
                    'type'     => isset($uploadedFile['type']) ? $uploadedFile['type'] : null,
                    'size'     => isset($uploadedFile['size']) ? $uploadedFile['size'] : null,
                    'error'    => $uploadedFile['error'],
                ];
            } else {
                $subArray = [];
                foreach ($uploadedFile['error'] as $fileIdx => $error) {
                    // normalise subarray and re-parse to move the input's keyname up a level
                    $subArray[$fileIdx]['name'] = $uploadedFile['name'][$fileIdx];
                    $subArray[$fileIdx]['type'] = $uploadedFile['type'][$fileIdx];
                    $subArray[$fileIdx]['tmp_name'] = $uploadedFile['tmp_name'][$fileIdx];
                    $subArray[$fileIdx]['error'] = $uploadedFile['error'][$fileIdx];
                    $subArray[$fileIdx]['size'] = $uploadedFile['size'][$fileIdx];

                    $parsed[$field] = static::parseUploadedFiles($subArray);
                }
            }
        }

        return $parsed;
    }
}

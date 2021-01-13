<?php

namespace App\Form;

use App\Translator\ValidatorTranslator;
use Laminas\Form\Form;
use Laminas\I18n\Translator\Resources;
use Laminas\Validator\AbstractValidator;

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

        $translator      = new ValidatorTranslator();
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
     * @return array
     */
    public static function buildData(array $params, array $uploadedFiles): array
    {
        return array_merge_recursive(
            $params,
            self::parseUploadedFiles($uploadedFiles)
        );
    }

    /**
     * parse uploaded files
     *
     * ネストされたfileの値をパースするために作成。
     *
     * @param array $uploadedFiles
     * @return array
     */
    private static function parseUploadedFiles(array $uploadedFiles): array
    {
        $parsed = [];

        foreach ($uploadedFiles as $field => $uploadedFile) {
            if (! isset($uploadedFile['error'])) {
                if (is_array($uploadedFile)) {
                    $parsed[$field] = static::parseUploadedFiles($uploadedFile);
                }

                continue;
            }

            $parsed[$field] = [];

            if (! is_array($uploadedFile['error'])) {
                $parsed[$field] = [
                    'tmp_name' => $uploadedFile['tmp_name'],
                    'name'     => $uploadedFile['name'] ?? null,
                    'type'     => $uploadedFile['type'] ?? null,
                    'size'     => $uploadedFile['size'] ?? null,
                    'error'    => $uploadedFile['error'],
                ];
            } else {
                $subArray = [];

                foreach ($uploadedFile['error'] as $fileIdx => $error) {
                    // normalise subarray and re-parse to move the input's keyname up a level
                    $subArray[$fileIdx]['name']     = $uploadedFile['name'][$fileIdx];
                    $subArray[$fileIdx]['type']     = $uploadedFile['type'][$fileIdx];
                    $subArray[$fileIdx]['tmp_name'] = $uploadedFile['tmp_name'][$fileIdx];
                    $subArray[$fileIdx]['error']    = $uploadedFile['error'][$fileIdx];
                    $subArray[$fileIdx]['size']     = $uploadedFile['size'][$fileIdx];

                    $parsed[$field] = static::parseUploadedFiles($subArray);
                }
            }
        }

        return $parsed;
    }
}

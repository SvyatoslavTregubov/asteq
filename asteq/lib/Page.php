<?php

namespace Asteq;

use Bitrix\Main\Loader;

class Page
{

    /**
     * @var Page объект класса
     */
    protected static $_instance;

    /**
     * @var int|null ID инфоблока "Страницы"
     */
    protected $iBlockId = null;

    /**
     * @var bool Флаг. Если true, то данные получены.
     */
    protected $isInit = false;

    /**
     * @var array стандартные поля элемента инфоблока
     */
    protected $arFields = null;

    /**
     * @var array пользовательские свойства, связанные с элементом инфоблока
     */
    protected $arProps = null;

    /**
     * @var array свойства инфоблока для элемента
     */
    protected $arProperties = null;

    private function __construct()
    {
        $this->iBlockId = IBLOCK_ID_PAGES;
    }

    public static function getInstance()
    {
        if (!Loader::includeModule('iblock')) {
            exit('Модуль инфоблоки не установлен');
        }

        if (self::$_instance === null) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    public function __clone()
    {
    }

    public function __wakeup()
    {
    }

    /**
     * Метод, который непосредственно получает данные из инфоблока
     * @param string $code символьный код элемента инфоблока (CODE)
     * @throws \Bitrix\Main\LoaderException
     */
    public function init($code = '')
    {

        global $APPLICATION;

        global $USER_FIELD_MANAGER;

        $page = $APPLICATION->getCurDir();

        if ($code) {
            $page = $code;
        }

        $arFilter = [
            'IBLOCK_ID' => $this->iBlockId,
            'CODE' => $page
        ];

        \Bitrix\Main\Loader::includeModule('iblock');

        $rsPage = \CIBlockElement::GetList([], $arFilter, false, false);

        if ($ob = $rsPage->getNextElement()) {

            $arFields = $ob->getFields();
            $arProperties = $ob->getProperties();

            $arProps = [];

            $arUserFields = $USER_FIELD_MANAGER->GetUserFields('ELEMENT_PROP_' . $arFields['ID'], $arFields['ID'], LANGUAGE_ID);

            foreach ($arUserFields as $code => $arUserField) {

                $arProps[$code] = $arUserField['VALUE'];
            }

            $this->arFields = $arFields;

            $this->arProps = $arProps;

            $this->arProperties = $arProperties;

            $this->isInit = true;
        }
    }

    /**
     * Метод для получения данных на странице
     * @param string $code Код элемента из инфоблока "Страницы"
     * @return array|bool Массив данных из полей и свойств элемента инфоблока
     * @throws \Bitrix\Main\LoaderException
     */
    public function getData($code = ''): array
    {
        if ($code)
            $this->init($code);

        return $this->returnData();
    }

    /**
     * Данные из элемента инфоблока "Страницы"
     * @return array данные ['FIELDS'] - стандарные поля, ['PROPS'] - пользовательские поля, ['PROPERTIES'] - свойства инфоблока
     */
    private function returnData(): array
    {
        return [
            'FIELDS' => $this->arFields,
            'PROPS' => $this->arProps,
            'PROPERTIES' => $this->arProperties
        ];
    }

    /**
     * Полное копирование всех пользовательских полей из одной одного элемента инфоблока в другой
     * @param int $entity_id исходная элемент
     * @param int $target_entity_id элемент в который будет копироваться
     */
    public function copyUF(int $entity_id, int $target_entity_id): void
    {
        $rsData = \CUserTypeEntity::GetList(array(), ['ENTITY_ID' => 'ELEMENT_PROP_' . $entity_id]);

        global $USER_FIELD_MANAGER;

        $aUserField = $USER_FIELD_MANAGER->GetUserFields(
            'ELEMENT_PROP_' . $entity_id,
            $entity_id
        );

        while ($arRes = $rsData->GetNext()) {
            $oUserTypeEntity = new \CUserTypeEntity();
            $aUserFields = array(
                'ENTITY_ID' => 'ELEMENT_PROP_' . $target_entity_id,
                'FIELD_NAME' => $arRes['FIELD_NAME'],
                'USER_TYPE_ID' => $arRes['USER_TYPE_ID'],
                'SORT' => $arRes['SORT'],
                'MULTIPLE' => $arRes['MULTIPLE'],
                'MANDATORY' => $arRes['MANDATORY'],
                'SHOW_FILTER' => $arRes['SHOW_FILTER'],
                'SHOW_IN_LIST' => $arRes['SHOW_IN_LIST'],
                'EDIT_IN_LIST' => $arRes['EDIT_IN_LIST'],
                'IS_SEARCHABLE' => $arRes['IS_SEARCHABLE'],
                'SETTINGS' => $arRes['SETTINGS']
            );
            $iUserFieldId = $oUserTypeEntity->Add($aUserFields);

            if ($iUserFieldId) {
                $USER_FIELD_MANAGER->Update('ELEMENT_PROP_' . $entity_id, $entity_id, array(
                    $arRes['FIELD_NAME'] => $aUserField[$arRes['FIELD_NAME']]['VALUE']
                ));
            }
        }
    }
}

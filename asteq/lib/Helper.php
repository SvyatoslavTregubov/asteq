<?php

namespace Asteq;

class Helper
{
    /**
     * Функция для получения элементов раздела инфоблока
     * @param $id - идентификатор раздела инфоблока
     * @return array - элементы раздела инфоблока
     */
    public static function getElementsByIBlockSectionID($id)
    {
        $arSelect = array("ID", "NAME", "CODE", "LINK", "DETAIL_PAGE_URL", "IBLOCK_ID", "IBLOCK_SECTION_ID");
        $arFilter = array('SECTION_ID' => $id, 'ACTIVE' => 'Y');
        $elements = array();
        $elements_list = \CIBlockElement::GetList(array("SORT" => "DECS"), $arFilter, false, false, $arSelect);
        while ($element = $elements_list->GetNext()) {
            $elements[] = array(
                $element['NAME'],
                $element['DETAIL_PAGE_URL'],
                array(),
                array(),
                ''
            );
        }
        return $elements;
    }

    /**
     * Проверка на наличие товаров в корзине текущего пользователя
     * @return bool - наличие товаров в корзине
     * @throws \Bitrix\Main\ArgumentException
     */
    public static function isBasketEmpty(): bool
    {
        $basket = \Bitrix\Sale\Basket::getList([
            'select' => ['NAME', 'QUANTITY'],
            'filter' => [
                '=FUSER_ID' => \Bitrix\Sale\Fuser::getId(),
                '=ORDER_ID' => null,
                '=LID' => \Bitrix\Main\Context::getCurrent()->getSite(),
                '=CAN_BUY' => 'Y',
            ]
        ]);

        return !($basket->getSelectedRowsCount());
    }

    /**
     * Функция для того, чтобы скрыть разделы от неавторизованного пользователя
     * @param string $section раздел, который надо скрыть
     * @param string $redirect_to редирект, на который будет перенаправлен неавторизованный пользователь
     */
    public static function forAuthorizedUser(string $section, string $redirect_to)
    {
        global $USER;
        $uri = \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->getRequestUri();
        if (strpos($uri, $section) !== false && !$USER->isAuthorized()) {
            LocalRedirect($redirect_to);
        }
    }

    /**
     * Получение последнего заказа текущего пользователя
     * @return array - ассоциативный массив с данными о последнем заказе пользователя
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\NotImplementedException
     */
    public static function getLastUserOrder(): array
    {
        global $USER;
        $order = \Bitrix\Sale\Order::loadByFilter([
            'order' => ['DATE_INSERT' => 'DESC'],
            'filter' => [
                'USER_ID' => $USER->GetID(),
            ],
            'limit' => 1
        ]);

        if (!$order) {
            return [];
        }
        $order = array_shift($order);
        $order_status_id = $order->getField('STATUS_ID');

        $result = array();

        $result['ORDER'] = $order;
        $result['BASKET'] = count($order->getBasket()->getBasketItems());
        $result['STATUS'] = \CSaleStatus::GetByID($order_status_id);

        return $result;
    }

    /**
     * Формирование ссылки для выхода из учетной записи
     * @return string сслыка для выхода
     */
    public static function logoutLink(): string
    {
        global $APPLICATION;
        return $APPLICATION->GetCurPageParam("logout=yes", array(
            "login",
            "logout",
            "register",
            "forgot_password",
            "change_password"
        ));
    }

    /**
     * Функция для получения имени интупа для веб формы
     * @param string $input_name - код вопроса из админки
     * @param array $arResult - результирующий массив компонента
     * @return string - имя для интупа
     */
    public static function formInputName(string $input_name, array &$arResult): string
    {
        return 'form_' . $arResult["QUESTIONS"][$input_name]["STRUCTURE"][0]["FIELD_TYPE"] . '_' . $arResult["QUESTIONS"][$input_name]["STRUCTURE"][0]["ID"] . '';
    }

    /**
     * Функция для получения окончания
     * @param int $value - число
     * @param array $status - варианты окончания
     * @return string - окончание
     */
    public static function getDecNum($value = 1, $status = array('', 'а', 'ов')): string
    {
        $array = array(2, 0, 1, 1, 1, 2);
        return $status[($value % 100 > 4 && $value % 100 < 20) ? 2 : $array[($value % 10 < 5) ? $value % 10 : 5]];
    }


    public static function getIblockElements($iblockId, $properties = true, $filter = [], $select = [
        "ID", "NAME", "IBLOCK_ID", "PREVIEW_TEXT", "PREVIEW_PICTURE", "DETAIL_TEXT", "CODE",
        "DETAIL_PICTURE", "ACTIVE_FROM", "SECTION_ID", 'DETAIL_PAGE_URL', 'PROPERTY_*'
    ])
    {
        if (!\CModule::IncludeModule("iblock"))
            return false;

        $filter["IBLOCK_ID"] = $iblockId;
        $rs = \CIBlockElement::GetList(["SORT" => "ASC"], $filter, false, false, $select);

        $elements = array();

        while ($ar = $rs->GetNextElement()) {
            $element = $ar->GetFields();
            if ($properties)
                $element["PROPERTIES"] = $ar->GetProperties();

            $elements[] = $element;
        }

        return $elements;
    }

    public static function getIblockSections($iblockId, $properties = true, $filter = [], $select = [
        "ID", "NAME", "IBLOCK_ID", "DETAIL_PICTURE", "IBLOCK_SECTION_ID"
    ])
    {
        if (!\CModule::IncludeModule("iblock"))
            return false;

        $filter["IBLOCK_ID"] = $iblockId;
        $rs = \CIBlockSection::GetList(["SORT" => "ASC"], $filter, false, $select);

        $elements = array();

        while ($ar = $rs->GetNextElement()) {
            $element = $ar->GetFields();
            if ($properties)
                $element["PROPERTIES"] = $ar->GetProperties();

            $elements[$element["ID"]] = $element;
        }

        return $elements;
    }

    public static function arraySearchById($id, $array, $param = "ID")
    {
        foreach ($array as $item) {
            if ($item[$param] == $id) {
                return $item;
            }
        }
        return false;
    }

    /**
     * Метод для определения типа видео контента по ссылке
     * @param string $link ссылка на видео
     * @return bool true если видео на youtube
     */
    static function isYoutube(string $link): bool
    {
        return strpos($link, 'youtu') !== false;
    }

    /**
     * Метод убирает лишние символы из номера телефона
     * @param string $phone номер телефона
     * @return string очищенный номер телефона
     */
    static function trimPhone(string $phone): string
    {
        return str_replace(['-', '+', '(', ')', ' '], '', $phone);
    }
}

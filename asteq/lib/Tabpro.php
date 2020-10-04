<?php

namespace Asteq;

use Bitrix\Main\Context;

class TabProp
{
    public function getTabList($elementInfo)
    {

        $request = Context::getCurrent()->getRequest();

        $addTabs = $elementInfo['ID'] > 0
            && $elementInfo['IBLOCK']['ID'] == IBLOCK_ID_PAGES
            && (!isset($request['action']) || $request['action'] != 'copy');

        return $addTabs ? [
            [
                "DIV"   => 'itech_prop_tab',
                "SORT"  => PHP_INT_MAX,
                "TAB"   => 'Blocks',
                "TITLE" => 'Blocks',
            ],
        ] : false;
    }

    public function showTabContent($div, $elementInfo, $formData)
    {

        global $USER_FIELD_MANAGER;

        $entityId = 'ELEMENT_PROP_' . $elementInfo['ID'];

        if (method_exists($USER_FIELD_MANAGER, 'showscript')) {

            echo $USER_FIELD_MANAGER->ShowScript();
        }

        echo '<tr>
            <td class="adm-detail-valign-top adm-detail-content-cell-l" colspan="2">
                <a href="/bitrix/admin/userfield_edit.php?lang=ru&ENTITY_ID=' . $entityId . '" target="_blank">Добавить поле</a>
            </td>
        </tr>';

        $arUserFields = $USER_FIELD_MANAGER->GetUserFields($entityId, $elementInfo['ID'], LANGUAGE_ID);

        foreach ($arUserFields as $FIELD_NAME => $arUserField) {
            $arUserField['VALUE_ID'] = $elementInfo['ID'];

            echo $USER_FIELD_MANAGER->GetEditFormHTML($formData, $GLOBALS[$FIELD_NAME], $arUserField);
        }
    }

    public function check()
    {

        return true;
    }

    public function action($elementInfo)
    {

        global $USER_FIELD_MANAGER;

        $entityId = 'ELEMENT_PROP_' . $elementInfo['ID'];

        $arUpdateFields = array();

        $USER_FIELD_MANAGER->EditFormAddFields($entityId, $arUpdateFields);

        $USER_FIELD_MANAGER->Update($entityId, $elementInfo['ID'], $arUpdateFields);

        return true;
    }
}

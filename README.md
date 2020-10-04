# Asteq

Модуль команды Asteq.
Модуль содержит классы помощники для работы с Битрикс.

# Основной функционал

1) Работа с OpenGraph разметкой;
2) Работа с highload справочниками;
3) Чтение/Запись в xlsx файлы;
4) Работа с выборкой данных из инфоблоков.

# Страницы

Для удобства интегрирования страниц был создан класс Page, который добавляет к элементам инфоблока функционал пользовательских полей.
Чтобы воспользовать функционалом нужно:

1) Создать инфоблок "Страницы" (название может быть любым);
2) Определить константу IBLOCK_ID_PAGES и присвоить ей значение ID инфоблка, созданного на 1 шаге;
3) Подключить модуль Asteq;
4) Зарегистрировать обработчики событий

AddEventHandler('main', 'OnAdminIBlockElementEdit', function () {

    $tabset = new \Asteq\TabProp();

    return [
        'TABSET' => 'custom_props',
        'Check' => [$tabset, 'check'],
        'Action' => [$tabset, 'action'],
        'GetTabs' => [$tabset, 'getTabList'],
        'ShowTab' => [$tabset, 'showTabContent'],
    ];
});

AddEventHandler("main", "OnBeforeProlog", function () {

    if (ERROR_404 != 'Y' && !isset($_SERVER['REAL_FILE_PATH'])) {

        \Asteq\Page::getInstance()->init();
    }
});

5) Создать элементы в инфоблоке, созданном 1 шаге. Символьный код элемента должен быть равен URL страницы (например, /about/).
6) На странице (например, /about/index.php) вызвать \Itech\Page::getInstance()->getData()



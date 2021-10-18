<?php

namespace Page;

use Codeception\Util\Locator;

class VideoPage
{
    public $URL = '/video/';

    public $searchInput = "input[name=text]"; // инпут поиска
    public $searchBTN = "div.websearch-button__text";  // кнопка "Найти"
    public static $spin = "thumb-preview__target_loading"; // wait spin
    public $nothing = ".misspell__message"; // search list is empty
    public $items = ".serp-item"; // блок с видео. can be count >=0
    public $preview_playing = ".thumb-preview__target_playing"; // при наведении появляется видео превью

    /**
     * @var \FunctionalTester;
     */
    protected $I;

    public function __construct($I)
    {
        $this->I = $I;
    }

    /**
     * @throws \Exception
     */
    public function search($text)
    {
        $this->I->fillField($this->searchInput, $text); // вводим текст
        $this->I->seeInField($this->searchInput, $text); // проверяем ввод (возможно codeception сам проверяет, тогда строчку убрать)
        $this->I->click($this->searchBTN);
        $this->I->multiWait([$this->items, $this->nothing]); // ждем либо список видео, либо запись что ничего не найдено и идем дальше
        return $this->I;
    }

    public function getItemsCount(): int
    {
        return $this->I->executeJS("return $('$this->items').size();"); // пока не знаю как по другому вытащить..
    }

    /**
     * @throws \Exception
     */
    public function mouseOnItem($ItemNum)
    {
        $this->I->moveMouseOver(Locator::elementAt($this->items, $ItemNum));
        $this->I->waitForElement($this->preview_playing, 4); // но то, что мы видим этот класс != отображение видео превью. к примеру ссылка на превью битая. или ошибка в js
        return $this->I;
    }

    public function getVideoSrc($elem)
    {
        return $this->I->grabAttributeFrom($elem . ' video', "src");
    }

}

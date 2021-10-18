<?php

class FirstCest
{

    private $searchText = "ураган";
    private $videoPage;

    function beforeAllTests($I, $helper)
    {
        $this->videoPage = new \Page\VideoPage($I);
    }

    // test
    public function videoPageWorks(FunctionalTester $I)
    {
        $I->amOnPage($this->videoPage->URL);
        $this->videoPage->search($this->searchText);

        $I->assertTrue($this->videoPage->getItemsCount() > 0, "search list is empty");
        $this->videoPage->mouseOnItem(2); // отсчет от 1, у самого первого нет превью = кинопоиск

        $src = $this->videoPage->getVideoSrc($this->videoPage->preview_playing); //проверяем заголовок src видео превью
        $I->assertNotEmpty($src, "don't see the preview src");
        $I->assertTrue($I->curl_exists($src), "can't load preview from server"); // если код !=200

        $I->assertTrue($I->checkPreview($this->videoPage->preview_playing),"preview dont change"); //тут мы сравниваем попиксельно изменение превью
        // есть конечно вероятность, что у превью видео первые пара кадров будут одинаковые, тогда надо подождать дольше
    }

    function afterAllTests($I)
    {
        $I->deleteTmpIMGs();
    }
}
       
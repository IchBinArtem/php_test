<?php


/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
 */
class FunctionalTester extends \Codeception\Actor
{
    use _generated\FunctionalTesterActions;

    public $screenPath = "tests/_output/debug/";

    public function takeElemShot(string $elem, int $num, $recCnt = 0)
    {
        if (file_exists($this->screenPath . $num . ".png")) unlink($this->screenPath . $num . ".png");
        $this->waitForElementNotVisible(\Page\VideoPage::$spin, 5);
        $this->makeElementScreenshot($elem, $num); // было б круто конечно в память делать, а не забивать I/O диска, но пока я не нашел как

        // иногда selenium web driver не делает скрин, а метод проходит. поэтому проверяем наличие скрина и если его нет, то повторно выполняем
        if (!file_exists($this->screenPath . $num . ".png") && $recCnt <= 5) {
            $this->takeElemShot($elem, $num, $recCnt + 1); //рекурсия
        } else
            $this->assertFileExists($this->screenPath . $num . ".png","can't make screenshot...");
    }

    public function deleteTmpIMGs()
    {
        // надо конечно получить все файлы в папке, и циклом удалить временные, но, т.к. я пока знаю что их 2, то просто так удалю)
        if (file_exists($this->screenPath . "1.png")) unlink($this->screenPath . "1.png");
        if (file_exists($this->screenPath . "2.png")) unlink($this->screenPath . "2.png");
    }

    public function checkPreview(string $elem): bool
    {
        $this->takeElemShot($elem, 1);
        sleep(0.3); // чтоб кадр успел смениться
        $this->takeElemShot($elem, 2);

        $img_before = imagecreatefrompng($this->screenPath . "1.png");
        $img_after = imagecreatefrompng($this->screenPath . "2.png");
        $size = getimagesize($this->screenPath . "1.png");//узнаем размеры картинки (дает нам масив size)

        for ($x = 0; $x < $size[0]; $x++)
            for ($y = 0; $y < $size[1]; $y++) {
                if (imagecolorat($img_before, $x, $y) != imagecolorat($img_after, $x, $y)) {
                    return true;
                }
            }
        return false;
    }

    public function multiWait(array $elements, int $timeOut = 5) // перечисляем несколько элементов, если один из них появился - выходим
    {
        $this->waitForJS("return $('" . implode(',', $elements) . "').size()>0;", $timeOut);
        /*
    $t = time();
    while (time() - $t < $timeOut) {
        foreach ($elements as $elem) {
            try {
                if ($this->seeElement($elem)) {
                    return true;
                }
            } catch (Exception $e) {
            }

        }
        $this->wait(0.1);
    }*/
        return false;
    }

    function curl_exists($url): bool
    {
        $headers = get_headers($url);
        return substr($headers[0], 9, 3) == 200;
    }
}

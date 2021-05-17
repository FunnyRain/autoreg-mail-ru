<?php

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverSelect;
use Facebook\WebDriver\WebDriverDimension;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Exception\WebDriverException;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;

require_once 'vendor/autoload.php';
require_once 'modules/register_module.php';
require_once 'modules/logger_module.php';

// ChromeDriver address
$serverUrl = 'http://localhost:9515';
// $proxys = "";

$desiredCapabilities = DesiredCapabilities::chrome();
$desiredCapabilities->setCapability('acceptSslCerts', false);
$ChromeOptions = new ChromeOptions();
//$ChromeOptions->addArguments(['--headless']);
$ChromeOptions->addArguments(['--disable-gpu']);
$ChromeOptions->addArguments(['--no-sandbox']);
$ChromeOptions->addArguments(['--incognito']);

// $randomProxy = explode("\n", $proxys);
// $randomProxy = $randomProxy[mt_rand(0, count($randomProxy) - 1)];
// $randomProxy = [
// explode(':', $randomProxy)[0],
// explode(':', $randomProxy)[1]
// ];
// outputString([$randomProxy[0], $randomProxy[1]]);
// $ChromeOptions->addArguments(['--proxy-server=' . $randomProxy[0] . ':' . $randomProxy[1]]);
$ChromeOptions->addArguments(['window-size=640,480']);

try {
  $desiredCapabilities->setCapability(ChromeOptions::CAPABILITY, $ChromeOptions);
  for ($q = 0; $q <= 10; $q++) {
    $driver = RemoteWebDriver::create($serverUrl, $desiredCapabilities);
    //! Регистрация
    $driver->get('https://account.mail.ru/signup?rf=auth.mail.ru&from=main');
    sleep(1);
    if ($driver->getCurrentURL() !== 'https://account.mail.ru/signup?rf=auth.mail.ru&from=main') {
      sleep(1);
      outputString(['Вылезла хуйня какая то, 5 сек']);
      $driver->manage()->window()->setSize(new WebDriverDimension(800, 600));
      $driver->findElement(WebDriverBy::xpath('/html/body/div/div/div/div[2]/div/form/section/div[2]/button[2]'))->click();
      sleep(5);
      $driver->manage()->window()->setSize(new WebDriverDimension(640, 480));
    }
    //? Заполняем: Имя
    $driver->findElement(WebDriverBy::xpath('//*[@id="fname"]'))->sendKeys($fname = randomFirstName());
    outputString(['-> ' . $fname]);
    //? Заполняем: Фамилию
    $driver->findElement(WebDriverBy::xpath('//*[@id="lname"]'))->sendKeys($lname = randomLastName());
    outputString(['-> ' . $lname]);
    //? Заполняем: День рождения
    $day = new WebDriverSelect($driver->findElement(WebDriverBy::xpath('/html/body/div[1]/div[3]/div[3]/div/div/div/div/form/div[5]/div[2]/div/div[1]/div/div/select')));
    $day->selectByIndex(mt_rand(1, 10));
    //? Заполняем: Месяц рождения
    $month = new WebDriverSelect($driver->findElement(WebDriverBy::xpath('/html/body/div[1]/div[3]/div[3]/div/div/div/div/form/div[5]/div[2]/div/div[3]/div/select')));
    $month->selectByIndex(mt_rand(1, 10));
    //? Заполняем: Год рождения
    $year = new WebDriverSelect($driver->findElement(WebDriverBy::xpath('/html/body/div[1]/div[3]/div[3]/div/div/div/div/form/div[5]/div[2]/div/div[5]/div/div/select')));
    $year->selectByIndex(mt_rand(20, 35));
    //? Заполняем: Случайный выбор пола
    $driver->findElement(WebDriverBy::xpath('/html/body/div[1]/div[3]/div[3]/div/div/div/div/form/div[8]/div[2]/div/label[' . mt_rand(1, 2) . ']'))->click();
    //? Заполняем: Мыло
    $driver->findElement(WebDriverBy::xpath('//*[@id="aaa__input"]'))->sendKeys($email = randomString());
    outputString(['-> ' . $email . '@mail.ru']);
    //? Заполняем: Пароль
    $driver->findElement(WebDriverBy::xpath('//*[@id="password"]'))->sendKeys($repeat = randomString());
    $driver->findElement(WebDriverBy::xpath('//*[@id="repeatPassword"]'))->sendKeys($repeat)->submit();
    outputString(['-> ' . $repeat]);
    //! Записываем данные
    file_put_contents('emails.txt', "{$email}@mail.ru:{$repeat}\n", FILE_APPEND);
  }
} catch (WebDriverException $e) {
  outputString([$e->getMessage()]);
}

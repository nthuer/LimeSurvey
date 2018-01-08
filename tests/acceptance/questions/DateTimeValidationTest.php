<?php
namespace ls\tests\acceptance\question;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\TimeOutException;
use ls\tests\TestBaseClassWeb;

/**
 *  LimeSurvey
 * Copyright (C) 2007-2011 The LimeSurvey Project Team / Carsten Schmitz
 * All rights reserved.
 * License: GNU/GPL License v2 or later, see LICENSE.php
 * LimeSurvey is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */


/**
 * @since 2017-10-27
 * @group datevalidation
 */
class DateTimeValidationTest extends TestBaseClassWeb
{
    /**
     * Import survey in tests/surveys/.
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        $surveyFile = self::$surveysFolder.'/limesurvey_survey_834477.lss';
        self::importSurvey($surveyFile);
        self::$testHelper->enablePreview();
    }

    /**
     * 
     */
    public function testBasic()
    {
        $urlMan = \Yii::app()->urlManager;
        $urlMan->setBaseUrl('http://' . self::$domain . '/index.php');
        $url = $urlMan->createUrl(
            'survey/index',
            [
                'sid' => self::$surveyId,
                'newtest' => 'Y',
                'lang' => 'pt'
            ]
        );

        self::$webDriver->get($url);

        try {
            $submit = self::$webDriver->findElement(WebDriverBy::id('ls-button-submit'));
        } catch (NoSuchElementException $ex) {
            $screenshot = self::$webDriver->takeScreenshot();
            $filename = self::$screenshotsFolder.'/DateTimeValidationTest.png';
            file_put_contents($filename, $screenshot);
            $this->assertFalse(
                true,
                'Url: ' . $url . PHP_EOL .
                'Screenshot in ' . $filename . PHP_EOL . $ex->getMessage()
            );
        }

        $this->assertNotEmpty($submit);
        self::$webDriver->wait(5)->until(
            WebDriverExpectedCondition::elementToBeClickable(
		WebDriverBy::id('ls-button-submit')
	    )
        );
        $submit->click();

        // After submit we should see the complete page.
        try {
            // Wait max 10 second to find this div.
            self::$webDriver->wait(5)->until(
                WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
                    WebDriverBy::className('completed-text')
                )
            );
            $div = self::$webDriver->findElement(WebDriverBy::className('completed-text'));
            $this->assertNotEmpty($div);
        } catch (NoSuchElementException $ex) {
            $screenshot = self::$webDriver->takeScreenshot();
            $filename = self::$screenshotsFolder.'/DateTimeValidationTest.png';
            file_put_contents($filename, $screenshot);
            $this->assertFalse(
                true,
                'Url: ' . $url . PHP_EOL .
                'Screenshot in ' .$filename . PHP_EOL . $ex->getMessage()
            );
        } catch (TimeOutException $ex) {
            $body = self::$webDriver->findElement(WebDriverBy::tagName('body'));
	    var_dump($body->getText());
	    $reflect = new \ReflectionClass($this);
	    //if ($reflect->getShortName() === 'Name') {
            self::$testHelper->takeScreenshot(self::$webDriver, $reflect->getShortName() . '_' . __FUNCTION__);
            $this->assertFalse(
                true,
                self::$testHelper->javaTrace($ex)
            );
	}
    }
}

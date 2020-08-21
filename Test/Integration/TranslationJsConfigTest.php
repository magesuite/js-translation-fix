<?php

namespace MageSuite\JsTranslationFix\Test\Integration;

class TranslationJsConfigTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Translation\Model\Js\Config
     */
    private $config;

    /**
     * @var \Magento\Translation\Model\Js\DataProvider
     */
    private $phraseProvider;

    public function codeSnippetsProvider(): array
    {
        return [
            ['function(e){return/\d/.test(e)},e.mage.__("Please provide house number."))}}', 'Please provide house number.']
        ];
    }

    public function setUp(): void
    {
        $this->config = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create(\Magento\Translation\Model\Js\Config::class);

        $this->phraseProvider = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create(\Magento\Translation\Model\Js\DataProvider::class);
    }

    public function testThatConfigContainsMoreThanOneRegexp()
    {
        /* This is to ensure that we haven't completely overwritten it. */
        $this->assertGreaterThan(1, count($this->config->getPatterns()));
    }

    public function testThatConfigContainsAdditionalRegexp()
    {
        $this->assertArrayHasKey('cs_translation_widget', $this->config->getPatterns());
    }

    /**
     * @dataProvider codeSnippetsProvider
     */
    public function testThatPhraseIsExtractedFromMinifiedCode(string $code, string $phrase)
    {
        $getPhrases = new \ReflectionMethod(\Magento\Translation\Model\Js\DataProvider::class, 'getPhrases');
        $getPhrases->setAccessible(true);
        $phrases = $getPhrases->invoke($this->phraseProvider, $code);

        $this->assertContains($phrase, $phrases);
    }
}

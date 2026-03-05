<?php

namespace locomotive\twig;

use Craft;
use craft\models\Site;
use Traversable;
use Twig\Extension\AbstractExtension;
use Twig\Extension\CoreExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;

class Extension extends AbstractExtension implements GlobalsInterface
{
    protected Site $site;
    protected string $baseUrl;
    protected ?string $assetsUri = null;
    protected ?string $assetsSubDir = null;

    public function __construct()
    {
        try {
            $this->site = Craft::$app->sites->SiteNotFoundException();
        } catch (\Exception $exception) {
            return;
        }
        $this->baseUrl = rtrim($this->standardizeProtocol($this->site->baseUrl), '/');
        $this->assetsUri = $this->baseUrl . '/dist/';
    }

    public function getGlobals(): array
    {
        return [
            'assetsUri'   => $this->assetsUri,
        ];
    }

    private function standardizeProtocol(?string $baseUrl): string
    {
        if (filter_var($baseUrl, FILTER_VALIDATE_URL) === false) {
            return $baseUrl;
        }

        return preg_replace('/^https?:\/\//', '//', $baseUrl, 1);
    }

    /**
     * @return list<TwigFunction>
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'html_attributes',
                [ $this, 'composeHtmlAttributes' ],
                [ 'is_safe' => [ 'html' ] ],
            ),
            new TwigFunction(
                'html_class',
                [ $this, 'composeHtmlClassAttribute' ],
                [ 'is_safe' => [ 'html' ] ],
            ),
            new TwigFunction(
                'html_tokens',
                [ $this, 'mergeTokens' ],
            ),
            new TwigFunction(
                'if',
                [ $this, 'resolveIf' ],
            ),
            new TwigFunction(
                'merge',
                'twig_array_merge',
            ),
            new TwigFunction(
                'srandom',
                [ $this, 'seededRandom' ],
            ),
        ];
    }

    /**
     * @param array|object $attributes
     */
    public function composeHtmlAttributes($attributes): ?string
    {
        $html = \html_build_attributes($attributes);
        if ($html) {
            return ' ' . $html;
        }

        return null;
    }

    public function composeHtmlClassAttribute(...$classes): ?string
    {
        $html = \html_build_attributes([ 'class' => $this->mergeTokens(...$classes) ]);
        if ($html) {
            return ' ' . $html;
        }

        return null;
    }

    /**
     * @param array{context: bool}|bool|null $current
     */
    public function composeAriaCurrentAttribute($current): ?string
    {
        if (isset($current['current'])) {
            $current = $current['current'];
        }

        if (is_bool($current)) {
            $current = $current ? 'true' : 'false';
        }

        if ($current) {
            $html = \html_build_attributes([
                'aria-current' => $current,
            ]);

            if ($html) {
                return ' ' . $html;
            }
        }

        return null;
    }

    /**
    * @param  array|\Traversable ...$arrays Any number of arrays or Traversable objects to merge
    * @return list<mixed> The merged array.
    */
    public function mergeTokens(...$arrays): array
    {
        $result = [];

        foreach ($arrays as $array) {
            if (is_array($array) || $array instanceof Traversable) {
                $array = CoreExtension::toArray($array);
            } elseif (\is_string($array)) {
                $array = \explode(' ', $array);
            } else {
                $array = (array) $array;
            }

            $array = array_filter($array, fn($token) => ($token !== null && $token !== ''));

            $result = array_merge($result, $array);
        }

        return $result;
    }

    /**
     * Resolves conditional values.
     *
     * Alternative to Twig's ternary short-hand `{{ result ? 'yes' }}`
     * that resolves to null instead of an empty string.
     *
     * > `{{ result ? 'yes' }}` is the same as `{{ result ? 'yes' : '' }}`
     *
     * In some scenarios, `null` is preferred to an empty string
     * such as in HTML attribute building.
     *
     * @param  mixed $logicalTest  The expression to test.
     * @param  mixed $valueIfTrue  The value to return if $logicalTest is true.
     * @param  mixed $valueIfFalse Optional. Defaults to null.
     * @return $logicalTest is true ? $valueIfTrue : $valueIfFalse
     */
    public function resolveIf($logicalTest, $valueIfTrue, $valueIfFalse = null)
    {
        return $logicalTest ? $valueIfTrue : $valueIfFalse;
    }

    /**
     * @param integer $seed
     * @param array $options
     *
     * @return mixed
     */
    public function seededRandom(int $seed, array $options): mixed
    {
        srand($seed);
        return $options[rand(0, count($options) - 1)];
    }
}

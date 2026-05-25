<?php

namespace modules\locomotive\twig;

use Craft;
use craft\helpers\Html;
use Traversable;
use Twig\Extension\AbstractExtension;
use Twig\Extension\CoreExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;
use Twig\TwigFunction;

class Extension extends AbstractExtension implements GlobalsInterface
{
    public function getGlobals(): array
    {
        return [];
    }

    // Functions
    // ============================================================

    /**
     * @return list<TwigFunction>
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'classAttr',
                [ $this, 'renderHtmlClassAttribute' ],
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
                'seeded_random',
                [ $this, 'seededRandom' ],
            ),
            new TwigFunction(
                'is_external_url',
                [ $this, 'isExternalUrl' ],
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
     * @param  array|\Traversable ...$arrays Any number of arrays or Traversable objects to merge
     * @return list<mixed> The merged array.
     */
    public function mergeTokens(...$arrays): array
    {
        $result = [];

        foreach ($arrays as $array) {
            if (\is_array($array) || $array instanceof Traversable) {
                $array = CoreExtension::toArray($array);
            } elseif (\is_string($array)) {
                $array = (array) \preg_split('/\s+/', $array, -1, PREG_SPLIT_NO_EMPTY);
            } else {
                $array = (array) $array;
            }

            $array = \array_filter($array, fn($token): bool => ($token !== null && $token !== ''));

            $result = \array_merge($result, $array);
        }

        return \array_values(\array_unique($result));
    }

    public function renderHtmlClassAttribute(...$classes): ?string
    {
        $html = Html::renderTagAttributes([ 'class' => $this->mergeTokens(...$classes) ]);
        if ($html) {
            return ' ' . $html;
        }

        return null;
    }

    /**
     * @param string $url
     *
     * @return boolean
     */
    public function isExternalUrl(string $url): bool
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $urlHost = parse_url($url, PHP_URL_HOST);
        $urlHost = preg_replace('/^www\./i', '', $urlHost);
        $siteHost = parse_url(Craft::$app->request->getAbsoluteUrl(), PHP_URL_HOST);
        $siteHost = preg_replace('/^www\./i', '', $siteHost);

        return ($urlHost && $siteHost !== $urlHost);
    }

    /**
     * @param integer $seed
     * @param array|float $options Number or list of items to randomly select.
     *
     * @return mixed
     */
    public function seededRandom(int $seed, array|int $options = 1): mixed
    {
        srand($seed);

        if (is_array($options)) {
            return $options[rand(0, count($options) - 1)];
        }

        return rand(0, $options);
    }

    public function toArray(): array
    {
        return [
            static::class => $this,
        ];
    }

    // Filters
    // ============================================================

    public function getFilters()
    {
        return [
            new TwigFilter(
                'camel2Kebab',
                [ $this, 'camel2Kebab' ],
            ),
        ];
    }

    public function camel2Kebab($value): ?string
    {
        if (!is_scalar($value)) {
            return null;
        }

        $value =  (string) $value;
        return strtolower(preg_replace(
            '/(?<=\d)(?=[A-Za-z])|(?<=[A-Za-z])(?=\d)|(?<=[a-z])(?=[A-Z])/',
            '-',
            $value,
        ));
    }
}

<?php

namespace locomotive\twig;

use Craft;
use craft\models\Site;
use Traversable;
use Twig\Extension\AbstractExtension;
use Twig\Extension\CoreExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;
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
            $this->site = Craft::$app->sites->SiteNotFoundExceptionSiteNotFoundException();
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
                [ 'is_safe' => [ 'html' ] ]
            ),
            new TwigFunction(
                'html_class',
                [ $this, 'composeHtmlClassAttribute' ],
                [ 'is_safe' => [ 'html' ] ]
            ),
            new TwigFunction(
                'html_tokens',
                [ $this, 'mergeTokens' ]
            ),
            new TwigFunction(
                'if',
                [ $this, 'resolveIf' ]
            ),
            new TwigFunction(
                'merge',
                'twig_array_merge'
            ),
            new TwigFunction(
                'srandom',
                [$this, 'seededRandom']
            ),
        ];
    }

    public function getFilters()
    {
        return [
            new TwigFilter(
                'camel2Kebab',
                [ $this, 'camel2Kebab' ]
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
                $array = (array)$array;
            }

            $array = array_filter($array, fn($token) => ($token !== null && $token !== ''));

            $result = array_merge($result, $array);
        }

        return $result;
    }

    public function resolveIf($logicalTest, $valueIfTrue, $valueIfFalse = null)
    {
        return $logicalTest ? $valueIfTrue : $valueIfFalse;
    }

    /**
     * Convert a string into a slug.
     *
     * This method strips punctuation to replicate slugs from Charcoal Legacy.
     *
     * @param string $str The string to slugify.
     *
     * @return string The slugify-ed string.
     */
    public function slugify($str): ?string
    {
        // Remove punctuation
        $punctuation = '/[&%\?\!\(\)\[\]\{\}\\\"\':#\.,;]/';
        $slug = strtolower(preg_replace($punctuation, '', $str));

        $separator = '-';
        $delimiters = '-_|';
        $pregDelim = preg_quote($delimiters);
        $directories = '\\/';
        $pregDir = preg_quote($directories);

        // Strip HTML
        $slug = strip_tags($slug);

        // Remove diacritics
        $slug = htmlentities($slug, ENT_COMPAT, 'UTF-8');
        $slug = preg_replace('!&([a-zA-Z])(uml|acute|grave|circ|tilde|cedil|ring);!', '$1', $slug);

        // Simplify ligatures
        $slug = preg_replace('!&([a-zA-Z]{2})(lig);!', '$1', $slug);

        // Remove unescaped HTML characters
        $unescaped = '!&(raquo|laquo|rsaquo|lsaquo|rdquo|ldquo|rsquo|lsquo|hellip|amp|nbsp|quot|ordf|ordm);!';
        $slug = preg_replace($unescaped, '', $slug);

        // Unify all dashes/underscores as one separator character
        $flip = ($separator === '-') ? '_' : '-';
        $slug = preg_replace('![' . preg_quote($flip) . ']+!u', $separator, $slug);

        // Remove all whitespace and normalize delimiters
        $slug = preg_replace('![_\|\s|\(\)]+!', $separator, $slug);

        // Squeeze multiple delimiters and whitespace with a single separator
        $slug = preg_replace('![' . $pregDelim . '\s]{2,}!', $separator, $slug);

        // Squeeze multiple URI path delimiters
        $slug = preg_replace('![' . $pregDir . ']{2,}!', $separator, $slug);

        // Remove delimiters surrouding URI path delimiters
        $slug = preg_replace(
            '!(?<=[' . $pregDir . '])[' . $pregDelim . ']|[' . $pregDelim . '](?=[' . $pregDir . '])!',
            '',
            $slug
        );

        // Strip leading and trailing dashes or underscores
        $slug = trim($slug, $delimiters);

        return $slug;
    }

    public function splitByLength($string, $length = 12)
    {
        $words = explode(' ', $string);
        $result = [];
        $currentLine = '';

        foreach ($words as $word) {
            if (strlen($currentLine . ' ' . $word) <= $length) {
                $currentLine .= ($currentLine === '' ? '' : ' ') . $word;
            } else {
                $result[] = $currentLine;
                $currentLine = $word;
            }
        }

        if ($currentLine !== '') {
            $result[] = $currentLine;
        }

        return $result;
    }

    public function camel2Kebab($value): ?string
    {
        if (!is_scalar($value)) return null;

        $value =  (string)$value;
        return strtolower(preg_replace(
            '/(?<=\d)(?=[A-Za-z])|(?<=[A-Za-z])(?=\d)|(?<=[a-z])(?=[A-Z])/', '-', $value));
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
        return $options[rand(0, count($options) -1 )];
    }

    public function toArray(): array
    {
        return [
            static::class => $this,
        ];
    }
}

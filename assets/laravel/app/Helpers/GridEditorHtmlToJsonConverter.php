<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class GridEditorHtmlToJsonConverter
{
    const TYPE_CONTAINER = 'container';
    const TYPE_ROW = 'row';
    const TYPE_COLUMN = 'column';
    const TYPE_MODULE = 'module';

    /**
     * Run converter.
     *
     * @param string $html
     * @return string|null
     */
    public static function run(string $html): ?string
    {
        $converter = new self;
        $DOM = new \DOMDocument();
        @$DOM->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

        $result = $converter->parseElement($DOM->documentElement);
        return $result ? json_encode($result) : null;
    }


    /**
     * Parse element.
     *
     * @param \DOMElement $element
     * @param string|null $previousType
     * @return array|null
     */
    protected function parseElement(\DOMElement $element, string $previousType = null): ?array
    {
        $type = null;
        $data = [];

        if ($this->hasClass($element, '_grid-container') || $this->hasClass($element, 'container') ||
            $this->hasClass($element, 'container-fluid')
        ) {
            if (!is_null($previousType) && $previousType !== self::TYPE_CONTAINER) {
                return null; // nothing can contain container (only container wrapper)
            }

            $type = self::TYPE_CONTAINER;
            $data = $this->extractContainerData($element);
        } elseif ($this->hasClass($element, 'row')) {
            if ($previousType === self::TYPE_ROW) {
                return null; // row cannot contain row
            }

            $type = self::TYPE_ROW;
            $data = $this->extractData($element);
        } elseif ($this->hasClass($element, '_grid-column')) {
            if ($previousType !== self::TYPE_ROW) {
                return null; // only row can contain column
            }

            $type = self::TYPE_COLUMN;
            $data = $this->extractData($element);
        } elseif ($this->hasClass($element, '_grid-column')) {
            if ($previousType !== self::TYPE_ROW) {
                return null; // only row can contain column
            }

            $type = self::TYPE_COLUMN;
            $data = $this->extractData($element);
        } elseif ($this->hasClass($element, '_grid-module')) {
            if ($previousType === self::TYPE_ROW) {
                return null; // only row cannot contain module
            }

            $type = self::TYPE_MODULE;
            $data = $this->extractModuleData($element);
        }

        $content = [];
        foreach ($element->childNodes as $subElement) {
            if ($subElement->nodeType !== XML_TEXT_NODE) {
                $item = $this->parseElement($subElement, $type);

                if (!$item) {
                    continue;
                }

                if (!isset($item['type'])) {
                    return $item;
                }

                // Merge with container wrapper data.
                if ($type === self::TYPE_CONTAINER && $item['type'] === self::TYPE_CONTAINER) {
                    $data = array_merge($item, $data);
                    $data['fluid'] = true;
                } else {
                    $content[] = $item;
                }
            }
        }

        if (!$type) {
            return $content;
        }

        $output = [
            'type' => $type,
        ];

        if ($type !== self::TYPE_MODULE) {
            $output['tag'] = $element->tagName;
        }

        if ($content) {
            $output['content'] = $content;
        }

        return array_merge($data, $output);
    }


    /**
     * Extract container data from attributes.
     *
     * @param \DOMElement $element
     * @return array
     */
    protected function extractContainerData(\DOMElement $element): array
    {
        $data = $this->extractData($element);

        if ($this->hasClass($element, 'container-fluid')) {
            $data['fluid'] = true;
        }

        return $data;
    }


    /**
     * Extract data of module from attribute.
     *
     * @param \DOMElement $element
     * @return array
     */
    protected function extractModuleData(\DOMElement $element): array
    {
        $data = [];

        if ($element->hasAttribute('data-module-id')) {
            $data['entity_id'] = intval($element->getAttribute('data-module-id'));
        } elseif ($element->hasAttribute('data-universalmodule-id')) {
            $data['entity_id'] = intval($element->getAttribute('data-universalmodule-id'));
            $data['universal'] = true;
        }

        return $data;
    }


    /**
     * Extract element data from attributes.
     *
     * @param \DOMElement $element
     * @return array
     */
    protected function extractData(\DOMElement $element): array
    {
        $data = [];

        foreach ($element->attributes as $attribute) {
            if ($attribute->name === 'class' && $attribute->value) {
                $output = $this->parseClass($attribute->value);
                $data = array_merge($data, $output);
            } elseif ($attribute->name === 'id' && $attribute->value) {
                $data['id'] = $attribute->value;
            } elseif ($attribute->name === 'data-is-deactivated' && $attribute->value === 'true') {
                $data['active'] = false;
            } elseif ($attribute->name === 'style' && $attribute->value) {
                $output = $this->parseStyle($attribute->value);
                $data = array_merge($data, $output);
            } else {
                $data[$attribute->name] = $attribute->value;
            }
        }

        return $data;
    }


    /**
     * Parse class.
     *
     * @param string $classChain
     * @return array
     */
    protected function parseClass(string $classChain): array
    {
        $output = [];
        $outputClasses = [];
        $classes = explode(' ', $classChain);
        foreach ($classes as $class) {
            if (in_array($class, ['container', 'row', 'container-fluid']) ||
                Str::startsWith($class, '_grid-')
            ) {
                continue;
            }

            if (Str::startsWith($class, 'col-')) {
                if (strlen($class) <= 6) {
                    $output['size']['col'] = intval(substr($class, 4));
                } else {
                    $size = substr($class, 4, 2);
                    $output['size'][$size] = intval(substr($class, 7));
                }
            } else {
                $outputClasses[] = $class;
            }
        }

        if ($outputClasses) {
            $output['class'] = join(' ', $outputClasses);
        }

        return $output;
    }


    /**
     * Parse style.
     *
     * @param string $style
     * @return array
     */
    protected function parseStyle(string $style): array
    {
        $output = [];
        $outputRules = [];
        $rules = explode(';', $style);
        foreach ($rules as $rule) {
            $rule = trim($rule);

            if (Str::startsWith($rule, 'background-color:')) {
                $output['bg'] = substr($rule, strlen('background-color:'));
            } else {
                $outputRules[] = $rule;
            }
        }

        if ($outputRules) {
            $output['style'] = join(';', $outputRules);
        }

        return $output;
    }


    /**
     * Check if element has specified class.
     *
     * @param \DOMElement $element
     * @param string $class
     * @return bool
     */
    protected function hasClass(\DOMElement $element, string $class): bool
    {
        return $element->hasAttribute('class') && strstr($element->getAttribute('class'), $class);
    }
}

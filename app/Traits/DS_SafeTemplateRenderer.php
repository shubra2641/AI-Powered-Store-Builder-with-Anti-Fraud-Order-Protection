<?php

namespace App\Traits;

/**
 * Trait DS_SafeTemplateRenderer
 * 
 * Implements a secure mechanism for rendering dynamic templates stored in the 
 * database, mitigating Server-Side Template Injection (SSTI) risks by avoiding 
 * direct Blade execution.
 */
trait DS_SafeTemplateRenderer
{
    /**
     * Safely render a template string by replacing {{ $variable }} placeholders.
     * 
     * @param string $template
     * @param array $data
     * @return string
     */
    protected function safeRender(string $template, array $data): string
    {
        $rendered = $template;

        foreach ($data as $key => $value) {
            if (is_string($value) || is_numeric($value)) {
                $rendered = str_replace(
                    ['{{ $' . $key . ' }}', '{{$' . $key . '}}'],
                    e((string) $value),
                    $rendered
                );
            }
            
            if (is_object($value)) {
                foreach ($this->getObjectProperties($value) as $prop => $val) {
                    $rendered = str_replace(
                        ['{{ $' . $key . '->' . $prop . ' }}', '{{$' . $key . '->' . $prop . '}}'],
                        e((string) $val),
                        $rendered
                    );
                }
            }
        }

        return $rendered;
    }

    /**
     * Get displayable properties of an object.
     * 
     * @param object $object
     * @return array
     */
    private function getObjectProperties(object $object): array
    {
        if (method_exists($object, 'toArray')) {
            return $object->toArray();
        }

        return get_object_vars($object);
    }
}

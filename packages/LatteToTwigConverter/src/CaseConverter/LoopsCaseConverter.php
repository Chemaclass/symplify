<?php declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\CaseConverter;

use Nette\Utils\Strings;
use Symplify\LatteToTwigConverter\Contract\CaseConverter\CaseConverterInterface;

final class LoopsCaseConverter implements CaseConverterInterface
{
    public function getPriority(): int
    {
        return 400;
    }

    public function convertContent(string $content): string
    {
        // {foreach $values as $key => $value}...{/foreach} =>
        // {% for key, value in values %}...{% endfor %}
        $content = Strings::replace(
            $content,
            '#{foreach \$?(.*?) as \$([()\w ]+) => \$(\w+)}#',
            '{% for $2, $3 in $1 %}'
        );

        // {foreach $values as $value}...{/foreach} =>
        // {% for value in values %}...{% endfor %}
        $content = Strings::replace($content, '#{foreach \$?(.*?) as \$([()\w ]+)}#', '{% for $2 in $1 %}');
        $content = Strings::replace($content, '#{/foreach}#', '{% endfor %}');

        // {first}...{/first} =>
        // {% if loop.first %}...{% endif %}
        $content = Strings::replace($content, '#{first}(.*?){/first}#ms', '{% if loop.first %}$1{% endif %}');

        // {last}...{/last} =>
        // {% if loop.last %}...{% endif %}
        $content = Strings::replace($content, '#{last}(.*?){/last}#ms', '{% if loop.last %}$1{% endif %}');

        // {sep}, {/sep} => {% if loop.last == false %}, {% endif %}
        return Strings::replace($content, '#{sep}([^{]+){\/sep}#', '{% if loop.last == false %}$1{% endif %}');
    }
}

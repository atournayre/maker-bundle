<?php

namespace {{ namespace }};

{% if uses %}
{% for use in uses %}
use {{ use }};
{% endfor %}
{% endif %}

/**
 * {{ class_name }} class.
{% if description %}
 * {{ description }}
{% endif %}
 */
{% if attributes %}
{% for attribute in attributes %}
#[{{ attribute }}]
{% endfor %}
{% endif %}
class {{ class_name }}{% if extends %} extends {{ extends }}{% endif %}{% if implements %} implements {{ implements|join(', ') }}{% endif %}
{
{% if properties %}
{% for property in properties %}
    {{ property.visibility }} {{ property.type }} ${{ property.name }}{% if property.default is defined %} = {{ property.default }}{% endif %};
{% endfor %}
{% endif %}

{% if constructor %}
    public function __construct(
{% for param in constructor.params %}
        {{ param.type }} ${{ param.name }}{% if not loop.last %},{% endif %}

{% endfor %}
    ) {
{% for param in constructor.params %}
        $this->{{ param.name }} = ${{ param.name }};
{% endfor %}
    }
{% endif %}

{% if methods %}
{% for method in methods %}
    {{ method.visibility }} function {{ method.name }}({% for param in method.params %}{{ param.type }} ${{ param.name }}{% if not loop.last %}, {% endif %}{% endfor %}): {{ method.return_type }}
    {
        {{ method.body|raw }}
    }
{% endfor %}
{% endif %}
}
